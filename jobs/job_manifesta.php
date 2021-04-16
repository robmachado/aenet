<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento das manifestações de destinatário NFe do sistema AENET
 * Irá ler cada registro marcado como não processado
 */

use Aenet\NFe\Controllers\ManifestaController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\ManifestaProcess;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_manifesta em andamento
$jobname = 'job_manifesta';
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
    $evtctrl = new ManifestaController();
    $mans = $evtctrl->pendentsAll();
    $oldid_empresa = 0;
    $client = null;
    $contador = 0;
    foreach ($mans as $man) {
        if ($contador > 50) {
            break;
        }
        $std = json_decode(json_encode($man));
        $id = $std->id;
        $id_empresa = $std->id_empresa;
        $manifestar_num = $std->manifestar;
        $justificativa = $std->justificativa;
        $chave = $std->chNFe;
        if ($cad->checkInactivity($id_empresa)) {
            continue;
        }
        if ($manifestar_num == 1) {
            $tipo = '210200';
        } elseif ($manifestar_num == 2) {
            $tipo = '210240';
        } else {
            continue;
        }
        if ($id_empresa != $oldid_empresa) {
            //pega os dados do cliente dessa NFe
            $cads = $cad->get($id_empresa);
            if (empty($cads)) {
                throw new \Exception("O cliente [$id_empresa] está com o certificado vencido ou não foi encontrado.");
            }
            $client = json_decode(json_encode($cads[0]));
            $oldid_empresa = $id_empresa;
            $evtp = new ManifestaProcess($client);
        }
        $evtp->send($id, $chave, $tipo, $justificativa);
        sleep(1);
        $contador++;
    }
    $comments = "SUCESSO #$contador manifestações realizadas.";
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
