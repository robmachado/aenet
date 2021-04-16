<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\NFe;
use Aenet\NFe\Controllers\BaseController;

class ManifestaController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function pendentsAll()
    {
        return NFe::whereIn('manifestar', [1,2])
            ->select(
                'id',
                'id_empresa',
                'chNFe',
                'manifestar',
                'justificativa'
            )
            ->orderBy('id_empresa')
            ->orderBy('chNFe')
            ->get()
            ->toArray();
    }
    
    public function update($id, $astd)
    {
        $nfe = NFe::where('id', $id)->update($astd);
    }
}
