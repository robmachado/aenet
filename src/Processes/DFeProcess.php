<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\NsusController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\NFe;
use NFePHP\NFe\Common\Standardize;
use DOMDocument;
use stdClass;
use DateTime;

class DFeProcess extends BaseProcess
{
    protected $nsus;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
        $nsus = new NsusController();
    }
    
    public function search()
    {
        $lastNsu = $this->nsus->getLastNSU($this->cad->id_empresa);
        $maxNSU = $ultNSU;
        if ($limit > 100 || $limit == 0 || empty($limit)) {
            $limit = 10;
        }
        $iCount = 0;
        //executa a busca de DFe em loop
        while ($ultNSU <= $maxNSU) {
            $iCount++;
            if ($iCount >= ($limit)) {
                break;
            }
            $resp = $this->tools->sefazDistDFe($ultNSU);
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
                $this->$processo($nodom, $numnsu);
            }
            sleep(5);
        }
    }
    
    /**
     * Processa NFe
     * @param DOMDocument $dom
     */
    protected function pprocNF(DOMDocument $dom, $numnsu)
    {
        $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
        $emit = $dom->getElementsByTagName('emit')->item(0);
        //cria um novo registro de NFe tabela dfe_nfes
        $nf = new NFe();
        $nf->id_empresa = $this->cad->id_empresa;
        $nf->nsu = $numnsu;
        $nf->content = $content;
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
    protected function presEve(DOMDocument $dom, $numnsu)
    {
        return $this->pprocEv($dom, $numnsu);
    }
    
    /**
     * Processa Eventos vinculados a NFe
     * @param DOMDocument $dom
     */
    protected function pprocEv(DOMDocument $dom, $numnsu)
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
        $ev->content = $content;
        $ev->cnpj = $nodom->getElementsByTagName('CNPJ')
            ->item(0)->nodeValue;
        $ev->chNFe = $nodom->getElementsByTagName('chNFe')
            ->item(0)->nodeValue;
        $ev->tpEvento = $nodom->getElementsByTagName('tpEvento')
            ->item(0)->nodeValue;
        $ev->nSeqEvento = $nodom->getElementsByTagName('nSeqEvento')
            ->item(0)->nodeValue;
        $ev->xEvento = $nodom->getElementsByTagName('xEvento')
            ->item(0)->nodeValue;
        $ev->dhEvento = $dhEvento->format('Y-m-d H:i:s');
        $ev->dhRecbto = $dhRecbto->format('Y-m-d H:i:s');
        $ev->nProt = $nodom->getElementsByTagName('nProt')
            ->item(0)->nodeValue;
        //grava
        $ev->save();
    }
    
    protected function presNFe()
    {
        $dt = new DateTime(
            $nodom->getElementsByTagName('dhEmi')->item(0)->nodeValue
        );
        //cria um novo registro de NSU
        $nsu = new Nsu();
        $nsu->id_empresa = $this->cad->id_empresa;
        $nsu->nsu = $numnsu;
        $nsu->content = $content;
        $nsu->tipo = 'resumo';
        $nsu->manifestar = 1;
        $nsu->cnpj = $nodom->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $nsu->chNFe = $nodom->getElementsByTagName('chNFe')->item(0)->nodeValue;
        $nsu->xNome = $nodom->getElementsByTagName('xNome')->item(0)->nodeValue;
        $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
        $nsu->nProt = $nodom->getElementsByTagName('nProt')->item(0)->nodeValue;
        //salva
        $nsu->save();
    }


    public function manifestaAll()
    {
        $res = $this->nsus->getPendents($this->cad->id_empresa);
        foreach($res as $r) {
            $stdRes = json_decode(json_encode($r));
            $chave = $stdRes->chNFe;
            try {
                $st = new Standardize();
                $resp = $this->tools->sefazManifesta($chave, 210210, '', 1);
                $response = $st->toStd($response);
                if ($cStat == 135) {
                    //se sucesso pode remover
                    Nsu::destroy($stdRes->id);
                }    
            } catch (\Exception $e) {
                $e->getMessage();
            }    
        }
    }

    
    
    
}
