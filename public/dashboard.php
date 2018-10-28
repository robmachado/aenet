<?php
require_once '../bootstrap.php';

use Aenet\NFe\Common\ServerMonitor;
use Aenet\NFe\Models\Monitor;
use Aenet\NFe\DBase\Connection;

//$conn = new Connection();
//$conn->connect();

//$monitor = new ServerMonitor();

//$memoryinfo = "<small><p>Memoria Total: $monitor->totalservermemory<br>Memoria Livre: $monitor->freeservermemory <br>PHP Alocada: $monitor->phpmemoryallocate<br>PHP Usada: $monitor->phpmemoryusage<br>PHP Peak: $monitor->phppeakmemoryusage</p></small>";

//$memory = round($monitor->memoryusage,0);
//$load = round($monitor->load[2],1);
//$disk = $monitor->diskusage;
//$swap = "<small><p>SWAP Total: $monitor->totalswap<br>Swap Usado: $monitor->swapusage</p></small>";
//$uptime = "<small><p>Uptime: $monitor->uptimedays dias, $monitor->uptimehours horas e $monitor->uptimeminutes minutos</p></small>";
//$descriptions = "<h3>Sistema</h3><small><p>$monitor->ostype $monitor->osname $monitor->osrelease $monitor->osversion $monitor5->kernel $monitor->servercores<p></small>";
$memoryinfo = '';
$memory = '';
$load = '';
$disk = '';
$swap = '';
$uptime = '';
$descriptions = '';
/*
$inut = Monitor::where('job', 'job_inutiliza')->latest('dtInicio')->first()->toArray();
if (empty($inut)) {
    $inutText = 'CRON JOB está Inoperante';
} else {
    if ($inut['dtInicio'] < date())
    $inutText = '<p><b>Inicio</b></p><p>['.$inut['dtInicio'].']</p><p><b>Fim</b></p><p>['. $inut['dtFim'].']</p>';
}
*/
/*
echo "<pre>";
print_r($load);
echo "</pre>";
die;*/
$template = "<!DOCTYPE html>
<html lang=\"pt_br\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>DashBoard</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://www.jqueryscript.net/css/jquerysctipttop.css\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"base.css\">
    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js\"></script>
    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js\"></script>
</head>
<body>
    <div class=\"container\" style=\"margin:50px auto;\">
        <h1>DashBoard</h1>
        <hr>
        <div class=\"row\">
            <div class=\"col-sm\">
                <div class=\"GaugeMeter\" id=\"memory\" data-percent=\"$memory\" data-append=\"%\" data-size=\"200\" data-theme=\"Blue\" data-back=\"RGBa(0,0,0,.3)\" data-animate_gauge_colors=\"1\" data-animate_text_colors=\"1\" data-width=\"20\" data-label=\"Memory\" data-style=\"Arch\" data-label_color=\"#333333\"></div>
                     $memoryinfo
            </div>
            <div class=\"col-sm\">
                <div class=\"GaugeMeter\" id=\"load\" data-percent=\"$load\" data-append=\"%\" data-size=\"200\" data-theme=\"Blue\" data-back=\"RGBa(0,0,0,.3)\" data-animate_gauge_colors=\"1\" data-animate_text_colors=\"1\" data-width=\"20\" data-label=\"Load\" data-style=\"Arch\" data-label_color=\"#333333\"></div>
                    $uptime
            </div>
            <div class=\"col-sm\">
                <div class=\"GaugeMeter\" id=\"disk\" data-percent=\"28\" data-append=\"%\" data-size=\"200\" data-theme=\"Blue\" data-back=\"RGBa(0,0,0,.3)\" data-animate_gauge_colors=\"1\" data-animate_text_colors=\"1\" data-width=\"20\" data-label=\"Disk\" data-style=\"Arch\" data-label_color=\"#333333\"></div>
                $swap
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-sm\">
                $descriptions
            </div>
            <div class=\"col-sm\">
            </div>
            <div class=\"col-sm\">
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-sm\">
                <h3>NFe</h3>
            </div>
            <div class=\"col-sm\">
                <h3>Danfe</h3>
            </div>
            <div class=\"col-sm\">
                <h3>Email</h3>
            </div>
            <div class=\"col-sm\">
                <h3>Cancela</h3>
            </div>
            <div class=\"col-sm\">
                <h3>Inutiliza</h3>
            </div>
            <div class=\"col-sm\">
                <h3>DFe</h3>
            </div>
        </div    
    </div>
    <script src=\"GaugeMeter.js\"></script>
    <script>
        $(\".GaugeMeter\").gaugeMeter();
    </script>
</body>
</html>";

echo $template;