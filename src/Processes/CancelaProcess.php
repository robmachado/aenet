<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;

class CancelaProcess extends BaseProcess
{
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_cancela.log');
    }
}
