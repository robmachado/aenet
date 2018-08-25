<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\Inutiliza;
use Aenet\NFe\Controllers\BaseController;

class InutilizaController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function pendentsAll()
    {
        return Inutiliza::where('status', 0)
            ->where('justificativa', '<>', '')
            ->get()
            ->toArray();
    }
    
    public function update($id, $astd)
    {
        Inutiliza::where('id', $id)->update($astd);
    }
}
