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
            $astd = [];
            //$txt = str_replace('/', '', $txt);
            $xml = Convert::parse($txt, 'LOCAL_V12');
            //$this->aenet->update($id, ['arquivo_nfe_xml' => $xml[0]]);
        } catch (\Throwable $e) {
            $error = "{$e->getMessage()}";
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error
            ];
            $this->aenet->update($id, $astd);
            $trace = json_encode($e->getTrace(), JSON_PRETTY_PRINT);
            $this->logger->error("ERROR: $id - $error {$e->getCode()} {$e->getMessage()}  {$trace}");
            return false;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->aenet->update($id, $astd);
            return false;
        }

        //tenta assinar a NFe se falhar grava ERRO e retorna
        //esse registro será bloqueado até que novo TXT seja inserido e
        //o status retornado a 0.
        try {
            $astd = [];
            $xmltext = $xml[0];
            $xmlsigned = $this->tools->signNFe($xmltext);
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
            $dom = null;
        } catch (\Thowable $e) {
            $error = str_replace(["'", '"'], "", $e->getMessage());
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error,
            ];
            $this->logger->error("Exception: $id - $error");
            $this->aenet->update($id, $astd);
            return false;
        }

        //tenta enviar para a SEFAZ se falhar grava o ERRO e retorna
        try {
            $astd = [];
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
                    'status_nfe' => 8,
                    'motivo' => "$cStat - $xMotivo"
                ];
                $this->aenet->update($id, $astd);
                $this->logger->warning("cStat: $cStat - $xMotivo");
                return false;
            }
            $astd = [
                'lote' => $lote,
                'recibo' => $recibo,
                'data_envio' => date('Y-m-d'),
                'data_envio_h'=> date('H:i:s')
            ];
            $this->aenet->update($id, $astd);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        sleep(2);
        //busca o protocolo
        return $this->consulta($id, $recibo, $xmlsigned);
    }

    public function consulta($id, $recibo, $xml)
    {
        //tenta buscar o recibo
        try {
            $astd = [];
            $response = $this->tools->sefazConsultaRecibo($recibo);
            $ret = $this->nfestd->toStd($response);
            $cStat = $ret->cStat;
            $xMotivo = $ret->xMotivo;
            $infProt = $ret->protNFe->infProt;
            if ($cStat == 105) {
                //105 lote em processamento, a SEFAZ ainda não
                //terminou de avaliar o XML, tentar de novo depois
                return false;
            }
            if ($cStat != 104) {
                $this->logger->error("Error: $id - $response");
                $status_nfe  = 9;
                if ($cStat == '656') {
                    $status_nfe = 8;
                }
                $astd = [
                   'status_nfe' => $status_nfe,
                   'motivo' => "$cStat - $xMotivo"
                ];
                $this->aenet->update($id, $astd);
                return false;
            }
            $cStatNFe = $infProt->cStat;
            $xMotivoNFe = $infProt->xMotivo;
            $xmlProt = $xml;
            $nProt = 0;
            $status = 9;
            if ($cStatNFe == '100') {
                $nProt = $infProt->nProt;
                //adiciona o protocolo no xml assinado
                $xmlProt = $this->cmpt->toAuthorize($xml, $response);
                $status = 1;
            } else {
                $this->logger->warning("cStat: $cStatNFe - $xMotivoNFe");
            }
            $astd = [
                'protocolo' => $nProt,
                'status' => $cStatNFe,
                'motivo' => $xMotivoNFe,
                'status_nfe' => $status,
                'arquivo_nfe_xml' => base64_encode($xmlProt)
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->aenet->update($id, $astd);
            return false;
        }
        return true;
    }
}
