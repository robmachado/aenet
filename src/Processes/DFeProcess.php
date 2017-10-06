<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\NsusController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\NFe;
use Aenet\NFe\Models\Event;
use NFePHP\NFe\Common\Standardize;
use DOMDocument;
use stdClass;
use DateTime;

class DFeProcess extends BaseProcess
{
    /**
     * @var NsusController
     */
    protected $nsus;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_dfe.log');
        $this->nsus = new NsusController();
    }
    
    public function search()
    {
        $ultNSU = $this->nsus->getLastNSU($this->cad->id_empresa);
        $maxNSU = $ultNSU;
        $limit = 10;
        $iCount = 0;
        $nsuproc = 0;
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
                return false;
            }
            if (empty($resp)) {
                $this->logger->error("Exception: Não houve resposta do sefazDistDfe verificar ambiente");
                return false;
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
            if (empty($lote)) {
                continue;
            }
            $docs = $lote->getElementsByTagName('docZip');
            $d = [];
            foreach ($docs as $doc) {
                $numnsu = $doc->getAttribute('NSU');
                $schema = $doc->getAttribute('schema');
                $content = gzdecode(base64_decode($doc->nodeValue));
                $tipo = substr($schema, 0, 6);
                $processo = "p$tipo";
                $st = new Standardize();
                $std = $st->toStd($content);
                //processa o conteudo do NSU
                $this->$processo($std, $numnsu, $content, $tipo);
                $nsuproc++;
            }
            sleep(5);
        }
        return $nsuproc;
    }
    
    protected function saveNSU(stdClass $std, $numnsu, $content, $tipo)
    {
        $nsu = new Nsu();
        $nsu->id_empresa = $this->cad->id_empresa;
        $nsu->nsu = $numnsu;
        $nsu->content = base64_encode($content);
        $nsu->tipo = $tipo;
        $nsu->manifestar = 0;
        if ($tipo == 'procNF') {
            $dt = new DateTime($std->NFe->infNFe->ide->dhEmi);
            $dhEmi = $dt->format('Y-m-d H:i:s');
            $nsu->cnpj = $std->NFe->infNFe->emit->CNPJ;
            $nsu->chNFe = preg_replace('/[^0-9]/', '', $std->NFe->infNFe->attributes->Id);
            $nsu->xNome = $std->NFe->infNFe->emit->xNome;
            $nsu->dhEmi = $dhEmi;
            $nsu->nProt = $std->NFe->protNFe->infProt->nProt;
        } elseif ($tipo == 'resNFe') {
            $dt = new DateTime($std->dhEmi);
            $nsu->cnpj = $std->CNPJ;
            $nsu->chNFe = $std->chNFe;
            $nsu->xNome = $std->xNome;
            $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
            $nsu->nProt = $std->nProt;
            $nsu->manifestar = 1;
        } elseif ($tipo == 'procEv') {
            $dt = new DateTime($std->evento->infEvento->dhEvento);
            $nsu->cnpj = $std->evento->infEvento->CNPJ;
            $nsu->chNFe = $std->evento->infEvento->chNFe;
            $nsu->xNome = '';
            $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
            $nsu->nProt = $std->retEvento->infEvento->nProt;
        } elseif ($tipo == 'resEve') {
            $dt = new DateTime($std->dhEvento);
            $nsu->cnpj = $std->CNPJ;
            $nsu->chNFe = $std->chNFe;
            $nsu->xNome = '';
            $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
            $nsu->nProt = $std->nProt;
        }
        //salva
        $nsu->save();
        return true;
    }

    
    protected function pprocNF(stdClass $std, $numnsu, $content, $tipo)
    {
        //salva NSU
        $this->saveNSU($std, $numnsu, $content, $tipo);
        //cria um novo registro de NFe tabela dfe_nfes
        $dt = new DateTime($std->NFe->infNFe->ide->dhEmi);
        $nf = new NFe();
        $nf->id_empresa = $this->cad->id_empresa;
        $nf->nsu = $numnsu;
        $nf->chNFe = preg_replace('/[^0-9]/', '', $std->NFe->infNFe->attributes->Id);
        $nf->cnpj = $std->NFe->infNFe->emit->CNPJ;
        $nf->xNome = $std->NFe->infNFe->emit->xNome;
        $nf->content = base64_encode($content);
        $nf->dhEmi = $dt->format('Y-m-d H:i:s');
        //salva
        $nf->save();
        return true;
    }
    
    /**
     * Processa Resumos de Eventos como emissão de CTe
     * @param DOMDocument $dom
     * @return none
     */
    protected function presEve(stdClass $std, $numnsu, $content, $tipo)
    {
        //salva NSU
        $this->saveNSU($std, $numnsu, $content, $tipo);
        //salva Evento
        $dt = new DateTime($std->dhRecbto);
        $ev = new Event();
        $ev->id_empresa = $this->cad->id_empresa;
        $ev->nsu = $numnsu;
        $ev->cnpj = $std->CNPJ;
        $ev->chNFe = $std->chNFe;
        $ev->tpEvento = $std->tpEvento;
        $ev->nSeqEvento = $std->nSeqEvento;
        $ev->dhEvento = $dt->format('Y-m-d H:i:s');
        $ev->dhRecbto = $dt->format('Y-m-d H:i:s');
        $ev->nProt = $std->nProt;
        $ev->content = base64_encode($content);
        //grava
        $ev->save();
        return true;
    }
    
    protected function pprocEv(stdClass $std, $numnsu, $content, $tipo)
    {
        //salva NSU
        $this->saveNSU($std, $numnsu, $content, $tipo);
        //salva Evento
        $dtEv = new DateTime($std->evento->infEvento->dhEvento);
        $dtReg = new DateTime($std->retEvento->infEvento->dhRegEvento);
        $ev = new Event();
        $ev->id_empresa = $this->cad->id_empresa;
        $ev->nsu = $numnsu;
        $ev->cnpj = !empty($std->evento->infEvento->CNPJ) ? $std->evento->infEvento->CNPJ : '';
        $ev->chNFe = $std->retEvento->infEvento->chNFe;
        $ev->tpEvento = $std->retEvento->infEvento->tpEvento;
        $ev->nSeqEvento = $std->retEvento->infEvento->nSeqEvento;
        $ev->xEvento = !empty($std->retEvento->infEvento->xEvento) ? $std->retEvento->infEvento->xEvento : '';
        $ev->dhEvento = $dtEv->format('Y-m-d H:i:s');
        $ev->dhRecbto = $dtReg->format('Y-m-d H:i:s');
        $ev->nProt = $std->retEvento->infEvento->nProt;
        $ev->content = base64_encode($content);
        //grava
        $ev->save();
        return true;
    }
    
    protected function presNFe(stdClass $std, $numnsu, $content, $tipo)
    {
        //salva NSU
        $this->saveNSU($std, $numnsu, $content, $tipo);
        return true;
    }

    public function manifestaAll()
    {
        $res = $this->nsus->getPendents($this->cad->id_empresa);
        foreach ($res as $r) {
            $stdRes = json_decode(json_encode($r));
            $chave = $stdRes->chNFe;
            try {
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
                Nsu::where('id', $stdRes->id)->update(['manifestar' => 0]);
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $this->logger->error("Exception: $error");
            }
        }
        return true;
    }
}
