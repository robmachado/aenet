<?php

namespace Aenet\NFe\Controllers;

use stdClass;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Response;
use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\Event;
use Aenet\NFe\Models\NFe;

class NsusController extends BaseController
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPendents($id_empresa)
    {
        return Nsu::where('id_empresa', 28)
            ->where('manifestar', '1')
            ->orderBy('nsu')
            ->get()
            ->toArray();
    }
    
    public function getLastNSU($id_empresa)
    {
        //pega o maior numero de nsu da tabela
        $nsu = Nsu::where('id_empresa', $id_empresa)
            ->orderBy('nsu', 'desc')
            ->first();
        $num = 0;
        if (!empty($nsu)) {
            $num = $nsu->nsu;
        }
        return $num;
    }
}
