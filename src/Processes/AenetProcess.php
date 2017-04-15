<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\AenetController;
use NFePHP\NFe\Convert;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use stdClass;

class AenetProcess extends BaseProcess
{
    /**
     * @var AenetController
     */
    protected $aenet;
    /**
     * @var Complements
     */
    protected $cmpt;
    /**
     * @var Standardize
     */
    protected $nfestd;
    
    /**
     * Constructor
     * @param stdClass $cad
     */
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_nfe.log');
        $this->aenet = new AenetController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
    }
    
    /**
     * Converts, sign, valid, send and autorize XML
     * @param int $id
     * @param string $txt
     * @return boolean
     */
    public function send($id, $txt)
    {
        //tenta converter se falhar grava ERRO e retorna
        //esse registro será bloqueado até que novo TXT seja inserido e
        //o status retornado a 0.
        try {
            $xml = Convert::toXML($txt);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error
            ];
            $this->logger->error("Exception: $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        
        //tenta assinar a NFe se falhar grava ERRO e retorna
        //esse registro será bloqueado até que novo TXT seja inserido e
        //o status retornado a 0.
        try {
            $xmlsigned = $this->tools->signNFe($xml[0]);
            //file_put_contents('xmlsig.xml',$xmlsigned);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
            $dom->loadXML($xmlsigned);
            $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
            $chave = preg_replace('/[^0-9]/', '', $infNFe->getAttribute('Id'));
            $astd = [
                'nfe_chave_acesso' => $chave,
                'arquivo_nfe_xml' => base64_encode($xmlsigned)
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error
            ];
            $this->logger->error("Exception: $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        
        //tenta enviar para a SEFAZ se falhar grava o ERRO e retorna
        try {
            $lote = date('YmdHis').rand(0, 9);
            $recibo = 0;
            $response = $this->tools->sefazEnviaLote([$xmlsigned], $lote);
            $ret = $this->nfestd->toStd($response);
            $cStat = $ret->cStat;
            $xMotivo = $ret->xMotivo;
            $recibo = $ret->infRec->nRec;
            if ($cStat != 103) {
                $this->logger->error("Erro: $response");
                $astd = [
                    'status_nfe' => 9,
                    'motivo' => "$cStat - $xMotivo"
                ];
                $this->aenet->update($id, $astd);
                return false;
            }
            $astd = [
                'lote' => $lote,
                'recibo' => $recibo,
                'data_envio' => date('Y-m-d H:i:s')
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        
        //tenta buscar o recibo
        try {
            $response = $this->tools->sefazConsultaRecibo($recibo);
            $ret = $this->nfestd->toStd($response);
            $cStat = $ret->cStat;
            $xMotivo = $ret->xMotivo;
            $infProt = $ret->protNFe->infProt;
            if ($cStat != 104) {
                $this->logger->error("Error: $response");
                $astd = [
                    'status_nfe' => 9,
                    'motivo' => "$cStat - $xMotivo"
                ];
                $this->aenet->update($id, $astd);
                return false;
            }
            $cStatNFe = $infProt->cStat;
            $xMotivoNFe = $infProt->xMotivo;
            $xmlProt = $xmlsigned;
            $nProt = 0;
            $status = 9;
            if ($cStatNFe == '100') {
                $nProt = $infProt->nProt;
                //adiciona o protocolo no xml assinado
                $xmlProt = $this->cmpt->toAuthorize($xmlsigned, $response);
                $status = 1;
            }
            $astd = [
                'protocolo' => $nProt,
                'status' => $cStatNFe,
                'motivo' => $xMotivoNFe,
                'status_nfe' => $status,
                'arquivo_nfe_xml' => base64_encode($xmlProt)
            ];
            $this->aenet->update($id, $astd);
            //verifica se a nota foi aceita, se nãoo sai
            if ($cStatNFe != 100) {
                return false;
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $error");
            $this->aenet->update($id, $astd);
            return false;
        }
    }
    
    /**
     * Executa o cancelamento da NFe indicada
     * @param int $id
     * @param string $chave
     * @param string $xJust
     * @param string $nProt
     * @return boolean
     */
    public function cancela($id, $chave, $xJust, $nProt)
    {
        try {
            //$response = $this->tools->sefazCancela($chave, $xJust, $nProt);
            //$ret = $this->nfestd->toStd($response);
            //verificar cStat
            //$ret = $this->cmpt->toAuthorize(
            //    $this->tools->lastRequest,
            //    $response
            //);
            //$xmlProt = $this->cmpt->toAuthorize($tools->lastRequest, $response);
            $astd = [
                'cancelamento_chave_acesso' => '',
                'data_cancelamento'  => '',
                'cancelamento_protocolo' => '',
                'cancelamento_xml' => '',
                'nfe_cancelada' => '1'
            ];
            //$this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
              'status_nfe' => 9, //erro 9 esse registro será ignorado
              'motivo' => $error
            ];
            $this->logger->error("Exception: $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        return true;
    }
}
