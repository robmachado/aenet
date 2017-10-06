<?php


namespace Aenet\NFe\Controllers;

use Aenet\NFe\Controllers\BaseController;
use DateTime;
use DateInterval;
use DateTimeZone;
use Aenet\NFe\Models\Monitor;

class MonitorController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function inicialize($job)
    {
        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $mon = new Monitor();
        $mon->job = $job;
        $mon->dtInicio = $dt->format('Y-m-d H:i:s');
        $mon->comments = '';
        $mon->save();
        return $mon->id;
    }
    
    public function finalize($id, $comments)
    {
        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $dtFim = $dt->format('Y-m-d H:i:s');
        Monitor::where('id', $id)->update(['comments' => $comments, 'dtFim' => $dtFim]);
    }
    
    public function hasPendent($job)
    {
        $resp = Monitor::where('job', $job)
            ->whereNull('dtFim')->get()->toArray();
        if (!empty($resp)) {
            return $resp[0]['id'];
        }
        return 0;
    }
    
    public function clear()
    {
        $dt = new DateTime();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $di = new DateInterval('P1D');
        $di->invert = 1;
        $dt->add($di);
        Monitor::where('dtInicio', '<', $dt->format('Y-m-d H:i:s'))->delete();
    }
}
