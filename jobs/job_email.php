<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Envia email aos destinatários
 */

//busca por registros com status = 100 ou 150 com arquivo_nfe_pdf NOT NULL e
//nfe_email_enviado

