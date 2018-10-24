<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\SmtpController;
use Aenet\NFe\Controllers\EventoController;
use NFePHP\Mail\Mail;
use stdClass;

class EmailCCeProcess extends BaseProcess
{
    /**
     * @var EventoController
     */
    protected $evento;
    /**
     * @var string
     */
    protected $htmlTemplate = "<p><b>Prezados,</b></p>" .
        "<p>Você está recebendo uma Carta de Correção referente ao nosso documento " .
        "{chave}.</p><p>Essa carta de correção datada de {data} procura corrigir:</p> " .
        "<p><b>{correcao}</b></p>" .
        "<p><i>{conduso}</i></p>" .
        "<br>" .
        "{image}" .
        "<p>Atenciosamente,</p>" .
        "<p>{emitente}</p>";

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_email_cce.log', false);
        $this->evento = new EventoController();
    }
    
    private function template($id)
    {
        $ip = getenv('HOST_IP');
        $url = "http://" . $ip . "/checkReadMailCCe.php?k=". $id;
        $this->htmlTemplate = str_replace('{image}', "<img src=\"$url\"><img>", $this->htmlTemplate);
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
                'evento_email_enviado' => 1,
                'data_email'        => date('Y-m-d'),
                'data_email_h'      => date('H:i:s')
            ];
            $this->evento->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $id - $error");
        }
        return true;
    }
    
}