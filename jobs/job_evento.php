<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Processamento das Cartas de Correção de NFe do sistema AENET
 * Irá ler cada registro marcado como não processado
 */

use Aenet\NFe\Controllers\EventoController;
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\EventoProcess;
use Aenet\NFe\Common\Flags;
use Aenet\NFe\Controllers\MonitorController;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_evento em andamento
$jobname = 'job_evento';
$mon = new MonitorController();
$idjob = $mon->insert($jobname);
if (!Flags::set($jobname)) {
    //indicar a dtFim do job na tabela monitor
    $mon->update($idjob);
    //encerra prematuramente o job
    die;
}

$cad = new CadastroController();
$evtctrl = new EventoController();

//verifica por cartas de correção de NFe, ainda não realizados
//para casos onde:
//nfes_aenet_evento.status = 0 e
//nfes_aenet_evento.justificativa <> '' e
//nfes_aenet.status_nfe = 1
$cces = $evtctrl->pendentsAll();
$oldid_empresa = 0;
$client = null;
foreach ($cces as $cce) {
    $std = json_decode(json_encode($cce));
    $id = $std->id;
    $id_empresa = $std->id_empresa;
    $xCorrecao = $std->justificativa;
    $nSeqEvento = $std->sequencial;
    $chave = $std->nfe_chave_acesso;
    if ($id_empresa != $oldid_empresa) {
        //pega os dados do cliente dessa NFe
        $client = json_decode(json_encode($cad->get($id_empresa)[0]));
        $oldid_empresa = $id_empresa;
        $evtp = new EventoProcess($client);
    }
    $evtp->send($id, $chave, $xCorrecao, $nSeqEvento);
}
//indicar a dtFim do job na tabela monitor
$mon->update($idjob);
//como o job encerrou remover o arquivo de controle antes de sair;
Flags::reset($jobname);
exit;
