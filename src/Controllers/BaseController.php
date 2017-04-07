<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\DBase\Connection;

class BaseController
{

    
    protected $conn;
    
    public function __construct()
    {
        $this->conn = new Connection();
        $this->conn->connect();
    }
}
