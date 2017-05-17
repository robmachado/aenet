<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\Cancela;
use Aenet\NFe\Models\Aenet;

class CancelaController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function pendentsAll()
    {
        return Cancela::where('nfes_aenet_cancel.status', 0)
            ->select(
                'nfes_aenet_cancel.id',
                'nfes_aenet.id_empresa',
                'nfes_aenet_cancel.justificativa',
                'nfes_aenet.nfe_chave_acesso',
                'nfes_aenet.protocolo'
            )
            ->join('nfes_aenet', 'nfes_aenet.id_nfes_aenet', '=', 'nfes_aenet_cancel.id_nfes_aenet')
            ->where('nfes_aenet_cancel.justificativa', '<>', '')
            ->where('nfes_aenet.status_nfe', '=', 1)
            ->get()
            ->toArray();
    }
    
    public function update($id, $astd)
    {
        Cancela::where('id', $id)->update($astd);
    }
}
