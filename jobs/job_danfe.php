<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Cria os DANFES para as notas já protocoladas
 */

use Aenet\NFe\Controllers\AenetController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\DanfeProcess;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_danfe em andamento
$jobname = 'job_danfe';
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
    $ae = new AenetController();
    //busca os registros com status = 100 ou 150
    //e arquivo_nfe_pdf NULL
    $nfes = $ae->danfeAll(); //retorna um array
    $oldid_empresa = 0;
    $client = null;
    $contador = 0;
    foreach ($nfes as $nfe) {
        $std = json_decode(json_encode($nfe));
        $id = $std->id_nfes_aenet;
        $id_empresa = $std->id_empresa;
        $xml = base64_decode($std->arquivo_nfe_xml);
        if ($id_empresa != $oldid_empresa) {
            //pega os dados do cliente dessa NFe
            $cads = $cad->get($id_empresa);
            if (empty($cads)) {
                throw new \Exception("O cliente [$id_empresa] está com o certificado vencido ou não foi encontrado.");
            }
            $client = json_decode(json_encode($cads[0]));
            $oldid_empresa = $id_empresa;
            $dp = new DanfeProcess($client);
        }
        //em caso de erro nada será gravado na base de dados
        //apenas um log será criado
        $dp->render($id, $xml);
        $contador++;
    }
    $comments = "SUCESSO #$contador DANFEs gerados.";
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
