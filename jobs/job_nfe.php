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

$cad = new CadastroController();
$ae = new AenetController();

//verifica item em aberto em nfes_aenet
$nfes = $ae->nfeAll(); //retorna um array
$oldid_empresa = 0;
$client = null;
foreach ($nfes as $nfe) {
    $std = json_decode(json_encode($nfe));
    $id = $std->id_nfes_aenet;
    $id_empresa = $std->id_empresa;
    $txt = $std->arquivo_nfe_txt;
    if ($id_empresa != $oldid_empresa) {
        //pega os dados do cliente dessa NFe
        $client = json_decode(json_encode($cad->get($id_empresa)[0]));
        $oldid_empresa = $id_empresa;
        $aep = new AenetProcess($client);
    }
    $aep->send($id, $txt);
}
exit;
