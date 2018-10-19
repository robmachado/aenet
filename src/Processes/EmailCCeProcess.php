<?php

use Aenet\NFe\DBase\Connection;
use Aenet\NFe\Controllers\SmtpController;
use NFePHP\Mail\Mail;
use NFePHP\DA\NFe\Dacce;
use stdClass;

class EmailCCeProcess
{
    protected $conn;
    
    public function __construct()
    {
        $this->conn = new Connection();
        $this->conn->connect();
    }
    
    public function send($id, $xml, $pdf = '', $addresses = [])
    {
        try {
            $this->template($id);
            //envia os emais ao destinatário
            $smCtrl = new SmtpController();
            $smtp = json_decode(json_encode($smCtrl->get()[0]));
            $config = new stdClass();
            $config->user = $smtp->user;
            $config->password = $smtp->pass;
            $config->host = $smtp->host;
            $config->secure = $smtp->security;
            $config->port = $smtp->port;
            $config->from = $smtp->user;
            //$config->from = $this->cad->emailfrom;
            $config->fantasy = $this->cad->fantasia;
            $config->replyTo = $this->cad->emailfrom;
            $config->replyName = $this->cad->fantasia;
            //envia o email
            $resp = Mail::sendMail($config, $xml, $pdf, $addresses, $this->htmlTemplate);
            //grava os dados na tabela
            $astd = [
                'nfe_email_enviado' => 1,
                'data_email'        => date('Y-m-d'),
                'data_email_h'      => date('H:i:s')
            ];
            $this->aenet->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $id - $error");
        }
        return true;
    }
    
}