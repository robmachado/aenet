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
use Aenet\NFe\Common\Flags;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_dfe em andamento
$jobname = 'job_dfe';
if (!Flags::set($jobname)) {
    //encerra prematuramente o job
    die;
}

$cad = new CadastroController();
$clients = $cad->getAllValid();

foreach ($clients as $c) {
    $client = json_decode(json_encode($c));
    $dfe = new DFeProcess($client);
    $dfe->search();
    $dfe->manifestaAll();
}
//como o job encerrou remover o arquivo de controle antes de sair;
Flags::reset($jobname);
exit;
