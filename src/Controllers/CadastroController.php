<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\Cadastro;
use Aenet\NFe\Controllers\BaseController;
use NFePHP\Common\Certificate;
use DateTime;

class CadastroController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id)
    {
        return json_decode(
            json_encode(
                Cadastro::where('id_empresa', $id)->get()->toArray()
            )
        );
    }
    
    public function getAllValid()
    {
        $dt = new DateTime();
        return Cadastro::where(
            'crtvalid_to', '>=', $dt->format('Y-m-d H:i:s')
            )
            ->get()
            ->toArray();
    }
    
    public function validateCertNull()
    {
        $clients = Cadastro::whereNull('crtvalid_to')->get();
        foreach($clients as $client) {
            $this->updateValidateCertNull($client);
        }
    }
    
    protected function updateValidateCertNull($client)
    {
        try {
            $cert = Certificate::readPfx(
                base64_decode($client->crtpfx),
                $client->crtpass
            );
            $dt = $cert->getValidTo()->format('Y-m-d H:i:s');
            Cadastro::where('id_empresa', $client->id_empresa)
               ->update(['crtvalid_to' => $dt]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Cadastro::where('id_empresa', $client->id_empresa)
               ->update(['error' => $error]);
        }
    }
    
    public function all()
    {
        return Cadastro::all()->toArray();
    }
}
