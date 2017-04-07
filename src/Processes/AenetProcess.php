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
use stdClass;

class AenetProcess extends BaseProcess
{
    protected $aenet;
    protected $cmpt;
    protected $nfestd;
    
    public function __construct(stdClass $cad) 
    {
        parent::__construct($cad);
        $this->aenet = new AenetController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
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
            $this->aenet->update($id, $astd);
            return false;
        }
        //tenta assinar se falhar grava ERRO e retorna
        //esse registro será bloqueado até que novo TXT seja inserido e 
        //o status retornado a 0.
        try {
            $xmlsigned = $this->tools->signNFe($xml);
            $astd = [
                'nfe_chave_acesso' => '',
                'arquivo_nfe_xml' => ''
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9, //erro 9 esse registro será ignorado
                'motivo' => $error
            ];        
            $this->aenet->update($id, $astd);
            return false;
        }
        
        //tenta enviar se falhar grava o ERRO e retorna
        try {
            //$response = $this->tools->sefazEnviaLote([$xmlsigned], $idLote);
            //$recibo = $this->nfestd->toStd($response)->recibo;
            $recibo = 0;
            $astd = [
                'recibo' => $recibo,
                'data_envio' => ''
            ];
            //$this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];        
            $this->aenet->update($id, $astd);
            return false;
        }
        
        //tenta buscar o recibo
        try {
            //$response = $this->tools->sefazConsultaRecibo($recibo);
            //$prot = $this->nfestd->toStd($response)
            //$cStat = $prot->cStat;
            //$nProt = $prot->nProt;
            //$xMotivo = $prot->xMotivo;
            $astd = [
                'protocolo' => '',
                'status' => '',
                'motivo' => '',
                'status_nfe' => '',
            ];
            //$this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status_nfe' => 9,
                'motivo' => $error
            ];        
            $this->aenet->update($id, $astd);
            return false;
        }
        //verifica se a nota foi aceita
        if ($cStat !== 100) {
            return false;
        }
        
        //adiciona o protocolo no xml assinado
        $xmlProt = $this->cmpt->addProtocolo($xmlsigned, $response);
        //imprime o DANFE
        $logo = base64_decode($this->cad->logo);
        $path = realpath("../../storage");
        $logopath = $path."/logo_".$this->cad->id_empresa.".jpg";
        file_put_contents($logopath, $logo);
        $danfe = new Danfe($docxml, 'P', 'A4', $logopath, 'I', '');
        $id = $danfe->montaDANFE();
        $pdf = $danfe->render();
        
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
            'arquivo_nfe_pdf' => '',
            'arquivo_nfe_xml' => '',
            'status_nfe' => 2,
            'nfe_pdf_gerado' => '',
            'nfe_email_enviado' => '',
            'data_envio' => '',
            'data_email' => '',
            'data_danfe' => ''
        ];
        //$this->aenet->update($id, $astd);
   }
}
