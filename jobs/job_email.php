<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Envia email aos destinatários
 */

use Aenet\NFe\Controllers\AenetController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\EmailProcess;

$cad = new CadastroController();
$ae = new AenetController();

//busca por registros com status = 100 ou 150 
//com arquivo_nfe_pdf NOT NULL e
//nfe_email_enviado 
$nfes = $ae->emailAll(); //retorna um array
$oldid_empresa = 0;
$client = null;
foreach($nfes as $nfe) {
    $std = json_decode(json_encode($nfe));
    $id = $std->id_nfes_aenet;
    $id_empresa = $std->id_empresa;
    $addresses = explode(';', $std->email_destinatario);
    $xml = base64_decode($std->arquivo_nfe_xml);
    $pdf = base64_decode($std->arquivo_nfe_pdf);
    
    if ($id_empresa != $oldid_empresa) {
        //pega os dados do cliente dessa NFe
        $client = json_decode(json_encode($cad->get($id_empresa)[0]));
        $oldid_empresa = $id_empresa;
        $ep = new EmailProcess($client);
    }
    //em caso de erro nada será gravado na base
    //apenas um log será criado
    $ep->send($id, $xml, $pdf, $addresses);
}
exit;