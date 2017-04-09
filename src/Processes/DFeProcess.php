<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\NsusController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\NFe;
use Aenet\NFe\Models\Event;
use NFePHP\NFe\Common\Standardize;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use DOMDocument;
use stdClass;
use DateTime;

class DFeProcess extends BaseProcess
{
    protected $nsus;
    protected $logger;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
        $this->nsus = new NsusController();
        // create a log channel
        $storage = realpath(__DIR__ .'/../../storage');
        $this->logger = new Logger('DFe');
        $this->logger->pushHandler(new StreamHandler($storage.'/job_dfe.log', Logger::WARNING));
    }
    
    public function search()
    {
        $ultNSU = $this->nsus->getLastNSU($this->cad->id_empresa);
        $maxNSU = $ultNSU;
        $limit = 10;
        $iCount = 0;
        //executa a busca de DFe em loop
        while ($ultNSU <= $maxNSU) {
            $iCount++;
            if ($iCount >= $limit) {
                break;
            }
            try {
                $this->tools->setEnvironment(1);
                $resp = $this->tools->sefazDistDFe($ultNSU);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $this->logger->error("Exception: $error");
            }
            //extrair e salvar os retornos
            $dom = new \DOMDocument();
            $dom->loadXML($resp);
            $node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
            $tpAmb = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
            $verAplic = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $cStat = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
            $xMotivo = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $dhResp = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
            $ultNSU = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
            $maxNSU = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
            $lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);
            $docs = $lote->getElementsByTagName('docZip');
            $d = [];
            foreach ($docs as $doc) {
                $numnsu = $doc->getAttribute('NSU');
                $schema = $doc->getAttribute('schema');
                $content = gzdecode(base64_decode($doc->nodeValue));
                $tipo = substr($schema, 0, 6);
                $processo = "p$tipo";
                $nodom = new DOMDocument();
                $nodom->loadXML($content);
                //processa o conteudo do NSU
                $this->$processo($nodom, $numnsu, $content);
            }
            sleep(5);
        }
    }
    
    /**
     * Processa NFe
     * @param DOMDocument $dom
     */
    protected function pprocNF(DOMDocument $dom, $numnsu, $content)
    {
        $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
        $emit = $dom->getElementsByTagName('emit')->item(0);
        //cria um novo registro de NFe tabela dfe_nfes
        $nf = new NFe();
        $nf->id_empresa = $this->cad->id_empresa;
        $nf->nsu = $numnsu;
        $nf->content = base64_encode($content);
        $nf->cnpj = $emit->getElementsByTagName('CNPJ')
            ->item(0)->nodeValue;
        $nf->chNFe = substr($infNFe->getAttibute('Id'), 3, 44);
        //salva
        $nf->save();
    }
    
    /**
     * Processa Resumos de Eventos como emissão de CTe
     * @param DOMDocument $dom
     * @return none
     */
    protected function presEve(DOMDocument $dom, $numnsu, $content)
    {
        return $this->pprocEv($dom, $numnsu, $content);
    }
    
    /**
     * Processa Eventos vinculados a NFe
     * @param DOMDocument $dom
     */
    protected function pprocEv(DOMDocument $dom, $numnsu, $content)
    {
        $dhEvento = new DateTime(
            $dom->getElementsByTagName('dhEvento')
                ->item(0)->nodeValue
        );
        $dhRecbto = new DateTime(
            $dom->getElementsByTagName('dhRecbto')
                ->item(0)->nodeValue
        );
        //cria um novo registro de evento tabela dfe_events
        $ev = new Event();
        $ev->id_empresa = $this->cad->id_empresa;
        $ev->nsu = $numnsu;
        $ev->cnpj = $dom->getElementsByTagName('CNPJ')
            ->item(0)->nodeValue;
        $ev->chNFe = $dom->getElementsByTagName('chNFe')
            ->item(0)->nodeValue;
        $ev->tpEvento = $dom->getElementsByTagName('tpEvento')
            ->item(0)->nodeValue;
        $ev->nSeqEvento = $dom->getElementsByTagName('nSeqEvento')
            ->item(0)->nodeValue;
        $ev->xEvento = $dom->getElementsByTagName('xEvento')
            ->item(0)->nodeValue;
        $ev->dhEvento = $dhEvento->format('Y-m-d H:i:s');
        $ev->dhRecbto = $dhRecbto->format('Y-m-d H:i:s');
        $ev->nProt = $dom->getElementsByTagName('nProt')
            ->item(0)->nodeValue;
        $ev->content = base64_encode($content);
        //grava
        $ev->save();
    }
    
    protected function presNFe(DOMDocument $dom, $numnsu, $content)
    {
        $dt = new DateTime(
            $dom->getElementsByTagName('dhEmi')->item(0)->nodeValue
        );
        //cria um novo registro de NSU
        $nsu = new Nsu();
        $nsu->id_empresa = $this->cad->id_empresa;
        $nsu->nsu = $numnsu;
        $nsu->content = base64_encode($content);
        $nsu->tipo = 'resumo';
        $nsu->manifestar = 1;
        $nsu->cnpj = $dom->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $nsu->chNFe = $dom->getElementsByTagName('chNFe')->item(0)->nodeValue;
        $nsu->xNome = $dom->getElementsByTagName('xNome')->item(0)->nodeValue;
        $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
        $nsu->nProt = $dom->getElementsByTagName('nProt')->item(0)->nodeValue;
        //salva
        $nsu->save();
    }

    public function manifestaAll()
    {
        $res = $this->nsus->getPendents($this->cad->id_empresa);
        foreach ($res as $r) {
            $stdRes = json_decode(json_encode($r));
            $chave = $stdRes->chNFe;
            try {
                $this->tools->setEnvironment(1);
                $response = $this->tools->sefazManifesta($chave, 210210, '', 1);
                $st = new Standardize();
                $resp = $st->toStd($response);
                
                $idLote = $resp->idLote;
                $tpAmb = $resp->tpAmb;
                $cStatMsg = $resp->cStat;
                $xMotivoMsg = $resp->xMotivo;
                if ($cStatMsg != 128) {
                    $this->logger->error("cStat [$cStatMsg] $response");
                    continue;
                }
                $versao = $resp->retEvento->attributes->versao;
                $cStat = $resp->retEvento->infEvento->cStat;
                $xMotivo = $resp->retEvento->infEvento->xMotivo;
                $chNFe = $resp->retEvento->infEvento->chNFe;
                $tpEvento = $resp->retEvento->infEvento->tpEvento;
                $xEvento = $resp->retEvento->infEvento->xEvento;
                $nSeqEvento = $resp->retEvento->infEvento->nSeqEvento;
                $dhRegEvento = $resp->retEvento->infEvento->nSeqEvento;
                if ($cStat == 135 || $cStat == 136 || $cStat == 573) {
                    //se sucesso pode remover o registro na tabela
                    Nsu::destroy($stdRes->id);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $this->logger->error("Exception: $error");
            }
        }
    }
}
