<?php

namespace Aenet\NFe\Controllers;

use stdClass;
use NFePHP\CTe\Tools;
use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\NsuCTe;
use Aenet\NFe\Models\EventCTe;
use Aenet\NFe\Models\CTe;

class NsusControllerCTe extends BaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getLastNSU($id_empresa)
    {
        //pega o maior numero de nsu da tabela
        $nsu = NsuCTe::where('id_empresa', $id_empresa)
            ->orderBy('nsu', 'desc')
            ->first();
        $num = 0;
        if (!empty($nsu)) {
            $num = $nsu->nsu;
        }
        return $num;
    }
}
