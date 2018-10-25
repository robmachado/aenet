<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '/var/www/aenet/bootstrap.php';

use Aenet\NFe\Models\Monitor;
use Aenet\NFe\DBase\Connection;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$conn = new Connection();
$conn->connect();

$jobname = 'job_truncate_monitor';
$logger = new Logger('Aenet');
$logger->pushHandler(
    new StreamHandler(__DIR__ . "/../storage/$jobname.log", Logger::WARNING)
);
$resp = shell_exec('service cron stop');
$logger->error($resp);
Monitor::truncate();
$resp = shell_exec('service cron start');
$resp = shell_exec('service cron status');
$logger->error($resp);
die;

