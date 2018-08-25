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
    
    public function emailAll()
    {
        //nfe_email_enviado NULL e arquivo_nfe_pdf NOT NULL
        return Aenet::whereNull('nfe_email_enviado')
            ->whereNotNull('arquivo_nfe_pdf')
            ->orderBy('id_empresa')
            ->get()
            ->toArray();
    }
    
    public function reciboAll()
    {
        //status_nfe = 0 e numero do recibo > 0, protocolo = 0 e arquivo_nfe_xml NOT NULL
        return Aenet::where('status_nfe', '=', 0)
            ->where('recibo', '>', 0)
            ->where('protocolo', '=', 0)
            ->whereNotNull('arquivo_nfe_xml')
            ->orderBy('id_empresa')
            ->get()
            ->toArray();
    }
    
    public function get($id)
    {
        return Aenet::where('id_nfes_aenet', $id)->get()->toArray();
    }
    
    public function update($id, $astd)
    {
        Aenet::where('id_nfes_aenet', $id)
            ->update($astd);
    }
}
