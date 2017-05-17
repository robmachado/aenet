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

$cad = new CadastroController();
$canc = new CancelaController();

//verifica por solicitações de cancelamento de NFe, ainda não realizados
//para casos onde:
//nfes_aenet_cancel.status = 0 e
//nfes_aenet_cancel.justificativa <> '' e
//status = 100
$nfes = $canc->pendentsAll();
$oldid_empresa = 0;
$client = null;
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
}
exit;
