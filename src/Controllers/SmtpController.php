<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
