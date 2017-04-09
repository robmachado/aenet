<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Controllers\AenetController;
use Aenet\NFe\Controllers\SmtpController;
use NFePHP\NFe\Convert;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use NFePHP\DA\NFe\Danfe;
use NFePHP\Mail\Mail;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use stdClass;

class AenetProcess extends BaseProcess
{
    protected $aenet;
    protected $cmpt;
    protected $nfestd;
    protected $logger;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
        $this->aenet = new AenetController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
        $storage = realpath(__DIR__ .'/../../storage');
        $this->logger = new Logger('Aenet');
        $this->logger->pushHandler(new StreamHandler($storage.'/job_nfe.log', Logger::WARNING));
    }
    
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
        
        //tenta enviar se falhar grava o ERRO e retorna
        try {
            $lote = date('YmdHis').rand(0, 9);
            $recibo = 0;
            $response = $this->tools->sefazEnviaLote([$xmlsigned], $lote);
            $ret = $this->nfestd->toStd($response);
            $cStat = $ret->retEnviNFe->cStat;
            $xMotivo = $ret->retEnviNFe->xMotivo;
            $recibo = $ret->retEnviNFe->infRec->nRec;
            echo "<pre>";
            print_r($ret);
            echo "</pre>";
            if ($cStat != 103) {
                $this->logger->error("Erro: $response");
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
            $cStat = $ret->retConsReciNFe->cStat;
            $xMotivo = $ret->retConsReciNFe->xMotivo;
            if ($cStat != 104) {
                $this->logger->error("Error: $response");
                return false;
            }
            $protNFe = $ret->retConsReciNFe->protNFe;
            $cStatNFe = $protNFe->cStat;
            $xMotivoNFe = $protNFe->xMotivo;
            $nProt = $protNFe->nProt;
            $xmlProt = '';
            $status = 9;
            if ($cStatNFe == '100') {
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
        //imprime o DANFE
        $path = realpath("../../storage");
        $logopath = $path."/logo_".$this->cad->id_empresa.".jpg";
        if (!is_file($logopath)) {
            $logo = base64_decode($this->cad->logo);
            file_put_contents($logopath, $logo);
        }
        $pdf = '';
        try {
            $danfe = new Danfe($docxml, 'P', 'A4', $logopath, 'I', '');
            $id = $danfe->montaDANFE();
            $pdf = $danfe->render();
            $astd = [
                'arquivo_nfe_pdf' => $pdf,
                'data_danfe' => date('Y-m-d H:i:s')
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $error");
        }
        
        try {
            //envia os emais ao destinatário
            $smCtrl = new SmtpController();
            $smtp = json_decode(json_encode($smCtrl->get()));
            $config = new stdClass();
            $config->mail->user = $smtp->user;
            $config->mail->password = $smtp->pass;
            $config->mail->host = $smtp->host;
            $config->mail->secure = $smtp->security;
            $config->mail->port = $smtp->port;
            $config->mail->from = $this->cad->emailfrom;
            $config->mail->fantasy = $this->cad->fantasia;
            $config->mail->replyTo = $this->cad->emailfrom;
            $config->mail->replyName = $this->cad->fantasia;
            $mail = new Mail($config);
            $mail->loadDocuments($xmlProt, $pdf);
            $addresses = [];
            $mail->send($addresses);
            
            //grava os dados na tabela
            $astd = [
                'nfe_email_enviado' => 1,
                'data_email' => date('Y-m-d H:i:s')
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $error");
        }
        return true;
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
