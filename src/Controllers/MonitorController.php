<?php


namespace Aenet\NFe\Controllers;

use Aenet\NFe\Controllers\BaseController;
use DateTime;
use DateInterval;
use Aenet\NFe\Models\Monitor;

class MonitorController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function insert($job)
    {
        $mon = new Monitor();
        $mon->job = $job;
        $mon->dtInicio = date('Y-m-d H:i:s');
        $mon->save();
        return $mon->id;
    }
    
    public function update($id)
    {
        $dtFim = date('Y-m-d H:i:s');
        Monitor::where('id', $id)->update(['dtFim' => $dtFim]);
    }
    
    public function pendent($job)
    {
        $resp = Monitor::where('job', $job)
            ->whereNull('dtFim')->get()->toArray();
        return $resp[count($resp)-1];
    }
    
    public function clear()
    {
        $dt = new DateTime();
        $di = new DateInterval('P1D');
        $di->invert = 1;
        $dt->add($di);
        Monitor::where('dtInicio', '<', $dt->format('Y-m-d H:i:s'))->delete();
    }
}
