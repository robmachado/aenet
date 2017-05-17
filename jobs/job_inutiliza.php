<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento das Solicitações do sistema AENET
 * Irá ler cada registro marcado como não processado
 */

use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Controllers\InutilizaController;
use Aenet\NFe\Processes\InutilizaProcess;

$cad = new CadastroController();
$inuts = new InutilizaController();

//verifica por solicitações de inutilização de numeros de NFe, ainda não realizados
//para casos onde:
//nfes_aenet_inuts.status = 0 e
//nfes_aenet_inuts.justificativa <> ''
$nfes = $inuts->pendentsAll();
$oldid_empresa = 0;
$client = null;
foreach ($nfes as $nfe) {
    $std = json_decode(json_encode($nfe));
    $id = $std->id;
    $id_empresa = $std->id_empresa;
    $nSerie = $std->serie;
    $nIni = $std->num_inicial;
    $nFin = $std->num_final;
    $xJust = $std->justificativa;
    $sequencial = $std->sequencial;
    if ($id_empresa != $oldid_empresa) {
        //pega os dados do cliente dessa NFe
        $client = json_decode(json_encode($cad->get($id_empresa)[0]));
        $oldid_empresa = $id_empresa;
        $inp = new InutilizaProcess($client);
    }
    $inp->inutiliza($id, $nSerie, $nIni, $nFin, $xJust, $sequencial);
}
exit;
