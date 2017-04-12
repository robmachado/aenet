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

$cad = new CadastroController();

$clients = $cad->getAllValid();

foreach($clients as $c) {
    $client = json_decode(json_encode($c));
    $dfe = new DFeProcess($client);
    $dfe->search();
    $dfe->manifestaAll();
}

