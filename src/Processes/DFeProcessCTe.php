<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\NsusControllerCTe;
use Aenet\NFe\Models\NsuCTe;
use Aenet\NFe\Models\CTe;
use Aenet\NFe\Models\EventCTe;
use NFePHP\CTe\Common\Standardize;
use DOMDocument;
use stdClass;
use DateTime;

class DFeProcessCTe extends BaseProcess
{
    protected $type = [
        '110110 ' => 'Carta de Correção',
        '110111' => 'Cancelamento',
        '110113' => 'EPEC',
        '110160' => 'Registros do Multimodal',
        '110170' => 'Informações  do GTV',
        '310620' => 'Registro de Passagem',
        '510620' => 'Registro de Passagem Automático',
        '310610' => 'MDF-e Autorizado',
        '310611' => 'MDF-e Cancelado',
        '240130' => 'Autorizado CT-e Complementar',
        '240131' => 'Cancelado CT-e Complementar',
        '240140' => 'CT-e de Substituição',
        '240150' => 'CT-e de Anulação',
        '240160' => 'Liberação de EPEC',
        '240170' => 'Liberação Prazo Cancelamento',
        '440130' => 'Autorizado Redespacho',
        '440140' => 'Autorizado Redespacho intermediário',
        '440150' => 'Autorizado Subcontratação',
        '440160' => 'Autorizado Serviço Vinculado Multimodal ',
        '610110 ' => 'Prestação do Serviço em Desacordo',
    ];

    /**
     * @var NsusController
     */
    protected $nsus;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_dfe_cte.log');
        $this->nsus = new NsusControllerCTe();
    }

    public function search()
    {
        $ultNSU = $this->nsus->getLastNSU($this->cad->id_empresa);
        $maxNSU = $ultNSU;
        $limit = 11;
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
                $tipo = substr($schema, 0, 7);
                $st = new Standardize();
                $std = $st->toStd($content);
                //processa o conteudo do NSU
                if ($tipo == 'procCTe') {
                    //processa o conteudo do NSU
                    $this->procCTe($std, $numnsu, $content, $tipo);
                } elseif($tipo == 'procEve') {
                    $this->procEve($std, $numnsu, $content, $tipo);
                }
                $nsuproc++;
            }
            sleep(5);
        }
        return $nsuproc;
    }

    protected function procCTe(stdClass $std, $numnsu, $content, $tipo)
    {
        $dt = new \DateTime($std->CTe->infCte->ide->dhEmi);
        $dhEmi = $dt->format('Y-m-d H:i:s');
        $chCTe = preg_replace('/[^0-9]/', '', $std->CTe->infCte->attributes->Id);
        $nProt = $std->protCTe->infProt->nProt ?? '';
        //salva NSU
        $this->saveNSU($numnsu, $tipo, $chCTe, $content);
        //cria um novo registro de CTe tabela dfe_ctes
        $cte = CTe::where('id_empresa', $this->cad->id_empresa)
            ->where('chCTe', $chCTe)
            ->first();
        if (empty($cte)) {
            $nf = new CTe();
            $nf->id_empresa = $this->cad->id_empresa;
            $nf->nsu = $numnsu;
            $nf->chCTe = $chCTe;
            $nf->cnpj = $std->CTe->infCte->emit->CNPJ;
            $nf->xNome = $std->CTe->infCte->emit->xNome;
            $nf->content = base64_encode($content);
            $nf->dhEmi = $dhEmi;
            $nf->save();
        }    
        return true;
    }

    protected function procEve(stdClass $std, $numnsu, $content, $tipo)
    {
        $dtEv = new \DateTime($std->eventoCTe->infEvento->dhEvento);
        $chCTe = $std->eventoCTe->infEvento->chCTe ?? '';
        $evento = !empty($this->type[$std->eventoCTe->infEvento->tpEvento])
            ? $this->type[$std->eventoCTe->infEvento->tpEvento] : '';
        //salva NSU
        $this->saveNSU($numnsu, $tipo, $chCTe, $content);
        //Salva evento de CTe
        $ev = new EventCTe();
        $ev->id_empresa = $this->cad->id_empresa;
        $ev->nsu = $numnsu;
        $ev->chCTe = $chCTe;
        $ev->tpEvento = $std->eventoCTe->infEvento->tpEvento;
        $ev->nSeqEvento = $std->eventoCTe->infEvento->nSeqEvento;
        $ev->xEvento = $evento;
        $ev->dhEvento = $dtEv->format('Y-m-d H:i:s');
        $ev->dhRecbto = $dtEv->format('Y-m-d H:i:s');
        $ev->nProt = '';
        $ev->content = base64_encode($content);
        $ev->save();
        return true;
    }

    protected function saveNSU($numnsu, $tipo, $chCTe, $content)
    {
        $nsu = NsuCTe::where('id_empresa', $this->cad->id_empresa)
            ->where('nsu', $numnsu)
            ->first();
        if (empty($nsu)) {
            $nsu = new NsuCTe();
            $nsu->id_empresa = $this->cad->id_empresa;
            $nsu->nsu = $numnsu;
            $nsu->tipo = $tipo;
            $nsu->chCTe = $chCTe;
            $nsu->content = base64_encode($content);
            $nsu->save();
        }    
        return true;
    }
}
