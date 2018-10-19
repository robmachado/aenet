<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

use Aenet\NFe\Controllers\AenetController;

$chave = $_GET['ch'];

if (empty($chave)) {
    echo "";
    die;
}

//procurar pela chave na tabela das nfes
try {
    
    $ae = new AenetController();
    $id = $ae->find('nfe_chave_acesso', $chave);
    
    
    
} catch (\Exception $e) {
    echo $e->getMessage();
}    


