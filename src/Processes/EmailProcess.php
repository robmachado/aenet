<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\SmtpController;
use NFePHP\Mail\Mail;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use stdClass;

class EmailProcess
{
    protected $aenet;
    protected $logger;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
        $storage = realpath(__DIR__ .'/../../storage');
        $this->logger = new Logger('Aenet');
        $this->logger->pushHandler(
            new StreamHandler($storage.'/job_email.log', Logger::WARNING)
        );
    }
    
    public function send()
    {
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
}
