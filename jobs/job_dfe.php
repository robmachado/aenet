<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento de busca dos documentos destinados DFe
 * para cada cliente cadastrado com certificados válidos
 */
use Aenet\NFe\Processes\DFeProcess;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\AlertFailProcess;
use Aenet\NFe\Controllers\MonitorController;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_dfe em andamento
$jobname = 'job_dfe';
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
    $clients = $cad->getAllValid();
    foreach ($clients as $c) {
        //para cada cliente registrado iniciar um novo processo DFe
        $client = json_decode(json_encode($c));
        $dfe = new DFeProcess($client);
        //trazer os NSUs deste cliente para a base de dados
        $nsuproc = $dfe->search();
        //manifestar com ciencia da operção os resNFe localizados
        $dfe->manifestaAll();
        $comments = "SUCESSO $nsuproc NSU processados.";
    }
} catch (\Exception $e) {
    $comments = 'Exception: ' 
        . $e->getMessage()
        . " " . $e->getFile()
        . " linha #" . $e->getLine()
        . " " . date('Y-m-d H:i:s');
    $logger->error($comments);
    AlertFailProcess::sendAlert(
        'ERROR '.$jobname
        , "<h2>Exception</h2><p>"
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
