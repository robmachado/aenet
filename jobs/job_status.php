<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Este JOB irá buscar e atualizar os status dos serviços
 * das SEFAZ autorizadoras
 * Para não haver bloqueio de acesso é importante que essa busca de status seja
 * realizada em intervalos não inferiores à 10 minutos.
 * A busca de status auxilia a opereção do sistema é não é o fator
 * mais relevante, pois mesmo o status indicando ONLINE podem haver problemas
 * de lentidão acima do limite interrompendo o processo.
 */
use Aenet\NFe\Controllers\CadastroController;
use Aenet\NFe\Processes\StatusProcess;
use Aenet\NFe\Processes\ValidateCertificatesProcess as Val;
use Aenet\NFe\Common\Flags;

//antes de iniciar o processo, verifica se já existe outro processo
//em andamento, com a verificação do arquivo de controle Flag, se não conseguir
//criar o arquivo de controle, é porque existe outro job_status em andamento
$jobname = 'job_status';
if (!Flags::set($jobname)) {
    //encerra prematuramente o job
    die;
}

//instancia o controller dos cadastros de clientes
$cad = new CadastroController();
//verifica e atualiza a validade dos certificados dos clientes 
//no cadastro antes de prosseguir
$cad->validateCertNull();
//puxa todos os cadastros de clientes com certificados válidos
$clients = $cad->getAllValid();
//SE nenhum cliente nessas condições for encontrado retornar
if (count($clients) == 0) {
    die;
}
//gera um numero aleatório para pegar um dos clientes cadastrados 
//como fonte de dados para a busca dos status da SEFAZ, isso é 
//feito para não sobrecarregar um unico certificado com muitas buscas
//e evitar o bloqueio por excesso de buscas
$n = rand(0, count($clients)-1);
//carrega os dados do cliente aleatoriamente escolhido em um stdClass
$stdClient = json_decode(json_encode($clients[$n]));
//instancia o processo de gestão dos status, esse processo irá acessar cada
//SEFAZ autorizadora e gravar o resultado na base de dados
//usando os dados do cliente indicado
$stProc = new StatusProcess($stdClient);
$stProc->updateAll();
//como o job encerrou remover o arquivo de controle antes de sair;
Flags::reset($jobname);
exit;
