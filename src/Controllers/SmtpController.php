<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\Smtp;

class SmtpController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get()
    {
        return Smtp::all()->toArray();
    }
}
