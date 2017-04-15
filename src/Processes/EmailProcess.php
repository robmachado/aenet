<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\SmtpController;
use NFePHP\Mail\Mail;
use stdClass;

class EmailProcess extends BaseProcess
{
    /**
     * @var AenetController
     */
    protected $aenet;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_email.log');
        $this->aenet = new AenetController();
    }
    
    public function send($id, $xml, $pdf = '')
    {
        try {
            //envia os emais ao destinatário
            $smCtrl = new SmtpController();
            $smtp = json_decode(json_encode($smCtrl->get()[0]));
            $config = new stdClass();
            $config->user = $smtp->user;
            $config->password = $smtp->pass;
            $config->host = $smtp->host;
            $config->secure = $smtp->security;
            $config->port = $smtp->port;
            $config->from = $this->cad->emailfrom;
            $config->fantasy = $this->cad->fantasia;
            $config->replyTo = $this->cad->emailfrom;
            $config->replyName = $this->cad->fantasia;
            //envia o email
            $resp = Mail::sendMail($config, $xml, $pdf, [''], '');
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
