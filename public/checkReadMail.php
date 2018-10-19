<?php
error_reporting(0);
ini_set('display_errors', 'Off');
require_once '/var/www/aenet/bootstrap.php';

use Aenet\NFe\DBase\Connection;
use Aenet\NFe\Models\MailLog;

function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
 
$k = !empty($_GET['k']) ? $_GET['k'] : 0;

if (!empty($k) && is_numeric($k) && $k > 0) {
    try {
        $conn = new Connection();
        $conn->connect();
        $log = new MailLog();
        $log->id_nfes_aenet = $k;
        $log->data = date('Y-m-d H:i:s');
        $log->ip = get_client_ip();
        $log->save();
    } catch (\Exception $e) {
        //echo $e->getMessage();
    }
}


// imprime imagem de 1px
header("Content-type: image/jpeg");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache");
header("Cache-Control: post-check=0, pre-check=0");
header("Pragma: no-cache");
echo base64_decode("R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==");

 