<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Envia email com CCe aos destinatários
 */

use Aenet\NFe\Controllers\EventoController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\EmailCCeProcess;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo igual
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_email em andamento
$jobname = 'job_email_cce';
$mon = new MonitorController();
//verifica se existem registros de jobs pendentes
$idjob = $mon->hasPendent($jobname);
if ($idjob > 0) {
    //já existe um job iniciado, sair
    die;
}
$logger = new Logger('Aenet');
$logger->pushHandler(
    new StreamHandler(__DIR__ . "/../storage/$jobname.log", Logger::WARNING)
);
//indicar inicio de novo job
$idjob = $mon->inicialize($jobname);

try {
    $cad = new CadastroController();
    $ec = new EventoController();
    $evts = $ec->emailAll();
    $oldid_empresa = 0;
    $client = null;
    $contador = 0;
    foreach ($evts as $evt) {
        $std = json_decode(json_encode($evt));
        $id = $std->id;
        $id_empresa = $std->id_empresa;
        $addresses = explode(';', $std->email_destinatario);
        $xml = base64_decode($std->xml);
        $pdf = base64_decode($std->arquivo_evento_pdf);
        if ($id_empresa != $oldid_empresa) {
            //pega os dados do cliente dessa NFe
            $cads = $cad->get($id_empresa);
            if (empty($cads)) {
                throw new \Exception("O cliente [$id_empresa] está com o certificado vencido ou não foi encontrado.");
            }
            $client = json_decode(json_encode($cads[0]));
            $oldid_empresa = $id_empresa;
            $ep = new EmailCCeProcess($client);
            if (!empty($client->emailfrom)) {
                $addresses[] = $client->emailfrom;
            }
        }
        //em caso de erro nada será gravado na base
        //apenas um log será criado
        $ep->send($id, $xml, $pdf, $addresses);
        $contador++;
        break;
    }
    $comments = "SUCESSO #$contador emails enviados.";
} catch (\Exception $e) {
    $comments = 'Exception: '
        . $e->getMessage()
        . " " . $e->getFile()
        . " linha #" . $e->getLine()
        . " " . date('Y-m-d H:i:s');
    $logger->error($comments);
    AlertFailProcess::sendAlert(
        'ERROR '.$jobname,
        "<h2>Exception</h2><p>"
        . $e->getMessage()
        . "</p><br/><p>Script: "
        . $e->getFile()
        . "</p><br/><p>Linha: "
        . $e->getLine()
        . "</p><br/>" . date('Y-m-d H:i:s')
    );
}
//indicar a dtFim do job na tabela monitor
$mon->finalize($idjob, $comments);
die;
