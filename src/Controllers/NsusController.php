<?php

namespace Aenet\NFe\Controllers;

use stdClass;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Response;
use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\Event;
use Aenet\NFe\Models\NFe;

class NsusController extends BaseController
{
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
    }

    public function pull($limit = 10)
    {
        $ultNSU = $this->getLastNSU();
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
            $resp = $this->tools->sefazDistDFe(
                $ultNSU
            );
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
                $nodom = null;
                $emit = null;
                $infNFe = null;
                $nf = null;
                

                $numnsu = $doc->getAttribute('NSU');
                $schema= $doc->getAttribute('schema');
                $content = gzdecode(base64_decode($doc->nodeValue));
                $nodom = new \DOMDocument();
                $nodom->loadXML($content);

                
                if (substr($schema, 0, 6) == 'resNFe') {
                    $dt = new \DateTime($nodom->getElementsByTagName('dhEmi')
                        ->item(0)
                        ->nodeValue);
                    $nsu = new Nsu();
                    $nsu->id_empresa = $this->cad->id_empresa;
                    $nsu->nsu = $numnsu;
                    $nsu->content = $content;
                    $nsu->tipo = 'resumo';
                    $nsu->manifestar = 0;
                    $nsu->cnpj = $nodom->getElementsByTagName('CNPJ')
                        ->item(0)
                        ->nodeValue;
                    $nsu->chNFe = $nodom->getElementsByTagName('chNFe')
                        ->item(0)
                        ->nodeValue;
                    $nsu->xNome = $nodom->getElementsByTagName('xNome')
                         ->item(0)
                         ->nodeValue;
                    $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
                    $nsu->nProt = $nodom->getElementsByTagName('nProt')
                        ->item(0)
                        ->nodeValue;
                    $nsu->save();
                } elseif (substr($schema, 0, 7) == 'procNFe') {
                    $infNFe = $nodom->getElementsByTagName('infNFe')->item(0);
                    $emit = $nodom->getElementsByTagName('emit')->item(0);
                    $nf = new NFe();
                    $nf->id_empresa = $this->cad->id_empresa;
                    $nf->nsu = $numnsu;
                    $nf->content = $content;
                    $nf->cnpj = $emit->getElementsByTagName('CNPJ')
                        ->item(0)->nodeValue;
                    $nf->chNFe = substr($infNFe->getAttibute('Id'), 3, 44);
                    $nf->save();
                } elseif (substr($schema, 0, 10) == 'procEvento'
                        || substr($schema, 0, 9) == 'resEvento'
                ) {
                    $dhEvento = new \DateTime(
                        $nodom->getElementsByTagName('dhEvento')
                            ->item(0)->nodeValue
                    );
                    $dhRecbto = new \DateTime(
                        $nodom->getElementsByTagName('dhRecbto')
                            ->item(0)->nodeValue
                    );
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
                    $ev->save();
                    
                    $nodom = new \DOMDocument();
                    $nodom->loadXML($content);
                    $node1 = $nodom->getElementsByTagName('retDistDFeInt')->item(0);
                    $std->id_empresa = $this->cad->id_empresa;
                    $std->nsu = $numnsu;
                    $std->content = $content;
                    $std->cnpj = $nodom->getElementsByTagName('CNPJ')
                        ->item(0)->nodeValue;
                    $std->chNFe = $nodom->getElementsByTagName('chNFe')
                        ->item(0)->nodeValue;
                    $std->tpEvento = $nodom->getElementsByTagName('tpEvento')
                        ->item(0)->nodeValue;
                    $std->nSeqEvento = $nodom->getElementsByTagName('nSeqEvento')
                        ->item(0)->nodeValue;
                    $std->xEvento = $nodom->getElementsByTagName('descEvento')
                        ->item(0)->nodeValue;
                    $std->dhEvento = $nodom->getElementsByTagName('dhEvento')
                        ->item(0)->nodeValue;
                    $std->dhRecbto = $nodom->getElementsByTagName('dhRegEvento')
                        ->item(0)->nodeValue;
                    $std->nProt = $nodom->getElementsByTagName('nProt')
                        ->item(0)->nodeValue;
                    $std->content;
                }
            }
            sleep(5);
        }
    }
    
    public function manifestCience()
    {
    }
    
    public function getLastNSU()
    {
        //pega o maior numero de nsu da tabela
        $nsu = Nsu::where('id_empresa', $this->cad->id_empresa)
            ->orderBy('nsu', 'desc')
            ->first();
        $num = 0;
        if (!empty($nsu)) {
            $num = $nsu->nsu;
        }
        return $num;
    }
}
