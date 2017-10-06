<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento das Solicitações de Cancelamento de NFe do sistema AENET
 * Irá ler cada registro marcado como não processado
 */

use Aenet\NFe\Controllers\CancelaController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\CancelaProcess;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_cancela em andamento
$jobname = 'job_cancela';
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

//verifica por solicitações de cancelamento de NFe, ainda não realizados
//para casos onde:
//nfes_aenet_cancel.status = 0 e
//nfes_aenet_cancel.justificativa <> '' e
//nfes_aenet.status_nfe = 1
try {
    $cad = new CadastroController();
    $canc = new CancelaController();
    $nfes = $canc->pendentsAll();
    $oldid_empresa = 0;
    $client = null;
    $contador = 0;
    foreach ($nfes as $nfe) {
        $std = json_decode(json_encode($nfe));
        $id = $std->id;
        $id_empresa = $std->id_empresa;
        $xJust = $std->justificativa;
        $nProt = $std->protocolo;
        $chave = $std->nfe_chave_acesso;
        if ($id_empresa != $oldid_empresa) {
            //pega os dados do cliente dessa NFe
            $client = json_decode(json_encode($cad->get($id_empresa)[0]));
            $oldid_empresa = $id_empresa;
            $cancp = new CancelaProcess($client);
        }
        $cancp->cancela($id, $chave, $xJust, $nProt);
        $contador++;
    }
    $comments = "SUCESSO #$contador cancelamentos efetuados.";
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
