<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento das Solicitações do sistema AENET
 * Irá ler cada registro marcado como não processado
 */

use Aenet\NFe\Controllers\AenetController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\AenetProcess;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_nfe em andamento
$jobname = 'job_nfe';
$mon = new MonitorController();
//remove registros iniciados a mais de um dia
$mon->clear();
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
    $ae = new AenetController();
    //verifica item em aberto em nfes_aenet
    $nfes = $ae->nfeAll(); //retorna um array
    $oldid_empresa = 0;
    $client = null;
    $contador = 0;
    foreach ($nfes as $nfe) {
        $std = json_decode(json_encode($nfe));
        $id = $std->id_nfes_aenet;
        $id_empresa = $std->id_empresa;
        $txt = str_replace("\r", "", $std->arquivo_nfe_txt);
        $recibo = $std->recibo;
        $xml  = base64_decode($std->arquivo_nfe_xml);
        if ($id_empresa != $oldid_empresa) {
            //pega os dados do cliente dessa NFe
            $client = json_decode(json_encode($cad->get($id_empresa)[0]));
            $oldid_empresa = $id_empresa;
            $aep = new AenetProcess($client);
        }
        if (empty($std->recibo)) {
            //ainda não foi obtido o recibo
            $aep->send($id, $txt);
        } elseif (!empty($xml)) {
            //o recibo já existe então pegar o protocolo
            $aep->consulta($id, $recibo, $xml);
        }
        $contador++;
    }
    $comments = "SUCESSO #$contador NFe enviadas.";
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
