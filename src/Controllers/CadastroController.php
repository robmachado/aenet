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
    
    /**
     * Retorna os dados do cadastro VALIDO com um id
     * especifico em um array
     * @param int $id
     * @return array
     */
    public function get($id)
    {
        $dt = new DateTime();
        return json_decode(
            json_encode(
                Cadastro::where('id_empresa', $id)
                    ->where('crtvalid_to', '>=', $dt->format('Y-m-d H:i:s'))
                    ->get()->toArray()
            )
        );
    }
    
    /**
     * Verifica se está marcado como inativo
     * isso deve impedir quelquer processamento
     * @param int $id
     * @return boolean
     */
    public function checkInactivity($id)
    {
        $cad = Cadastro::where('id_empresa', $id)
            ->select('inactive')
            ->first();
        return $cad->inactive;
    }
    
    /**
     * Retorna todos os registros do cadastro
     * com certificado dentro da validade e com
     * o campo INACTIVE = false
     * @return array
     */
    public function getAllValid()
    {
        $dt = new DateTime();
        return Cadastro::where(
            'crtvalid_to',
            '>=',
            $dt->format('Y-m-d H:i:s')
        )
        ->where('inactive', '=', false)
        ->get();
    }
    
    /**
     * Atualiza os dados quando a data de validade do
     * certificado estiver NULL
     */
    public function validateCertNull()
    {
        $clients = Cadastro::whereNull('crtvalid_to')->get();
        foreach ($clients as $client) {
            $this->updateValidateCertNull($client);
        }
    }
    
    /**
     * Faz a verificação da data de validade e
     * verifica o funcionamento do PFX
     * @param Cadastro $client
     */
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
    
    /**
     * Retorna todos os cadastros de clientes
     * @return array
     */
    public function all()
    {
        return Cadastro::all()->toArray();
    }
}
