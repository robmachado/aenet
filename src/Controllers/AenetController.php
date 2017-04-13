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
    
    public function nfeAll()
    {
        return Aenet::where('status_nfe', 0)
            ->orderBy('id_empresa')
            ->get()
            ->toArray();
    }
    
    public function danfeAll()
    {
        //status = 100 ou 150 e arquivo_nfe_pdf NULL
        return Aenet::whereNull('arquivo_nfe_pdf')
            ->where('status', 100)    
            ->orWhere('status', 150)
            ->orderBy('id_empresa')
            ->get()
            ->toArray();
    }
    
    public function cancelAll()
    {
        return Aenet::where('justificativa', '<>', '')
            ->where('status', '=', '100')
            ->where('cancelamento_protocolo', '=', '')
            ->where('protocolo', '<>', '')
            ->where('nfe_chave_acesso', '<>', '')
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
