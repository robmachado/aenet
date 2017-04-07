<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\Aenet;
use Aenet\NFe\Controllers\InputsController;
use Aenet\NFe\Controllers\BaseController;

class AenetController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function all()
    {
        return Aenet::where('status_nfe', '=', 0)
            ->orderBy('id_empresa')
            ->get()
            ->toArray();
    }
    
    public function update($id, $astd)
    {
        Aenet::where('id_nfes_aenet', $id)
            ->update($astd);
    }
}
