<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\SmtpController;
use Aenet\NFe\Controllers\AenetController;
use NFePHP\Mail\Mail;
use stdClass;

class EmailProcess extends BaseProcess
{

    /**
     * @var AenetController
     */
    protected $aenet;
    /**
     * @var string
     */
    protected $htmlTemplate = "<p><b>Prezados {destinatario},</b></p>" .
        "<p>Você está recebendo a Nota Fiscal Eletrônica emitida em {data} com o número " .
        "{numero}, de {emitente}, no valor de R$ {valor}. " .
        "Junto com a mercadoria, você receberá também um DANFE (Documento " .
        "Auxiliar da Nota Fiscal Eletrônica), que acompanha o trânsito das mercadorias.</p>" .
        "<p><i>Podemos conceituar a Nota Fiscal Eletrônica como um documento " .
        "de existência apenas digital, emitido e armazenado eletronicamente, " .
        "com o intuito de documentar, para fins fiscais, uma operação de " .
        "circulação de mercadorias, ocorrida entre as partes. Sua validade " .
        "jurídica garantida pela assinatura digital do remetente (garantia " .
        "de autoria e de integridade) e recepção, pelo Fisco, do documento " .
        "eletrônico, antes da ocorrência do Fato Gerador.</i></p>" .
        "<p><i>Os registros fiscais e contábeis devem ser feitos, a partir " .
        "do próprio arquivo da NF-e, anexo neste e-mail, ou utilizando o " .
        "DANFE, que representa graficamente a Nota Fiscal Eletrônica. " .
        "A validade e autenticidade deste documento eletrônico pode ser " .
        "verificada no site nacional do projeto (www.nfe.fazenda.gov.br), " .
        "através da chave de acesso contida no DANFE.</i></p>" .
        "<p><i>Para poder utilizar os dados descritos do DANFE na " .
        "escrituração da NF-e, tanto o contribuinte destinatário, " .
        "como o contribuinte emitente, terão de verificar a validade da NF-e. " .
        "Esta validade está vinculada à efetiva existência da NF-e nos " .
        "arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>" .
        "<p><b>O DANFE não é uma nota fiscal, nem substitui uma nota fiscal, " .
        "servindo apenas como instrumento auxiliar para consulta da NF-e no " .
        "Ambiente Nacional.</b></p>" .
        "<p>Para mais detalhes, consulte: <a href=\"http://www.nfe.fazenda.gov.br/\">" .
        "www.nfe.fazenda.gov.br</a></p>" .
        "<br>" .
        "{image}" .
        "<p>Atenciosamente,</p>" .
        "<p>{emitente}</p>";

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_email.log');
        $this->aenet = new AenetController();
    }
    
    
    private function template($id)
    {
        $ip = $_SERVER['SERVER_ADDR'];
        $url = "http://$ip/checkReadMail.php?k=$id";
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
