<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;

class EventoProcess extends BaseProcess
{
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_evento.log');
    }
}
