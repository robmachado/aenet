<?php

namespace Aenet\NFe\Controllers;

use Aenet\NFe\Models\Evento;
use Aenet\NFe\Controllers\BaseController;

class EventoController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function pendentsAll()
    {
        return Evento::where('nfes_aenet_evento.status', 0)
            ->select(
                'nfes_aenet_evento.id',
                'nfes_aenet.id_empresa',
                'nfes_aenet_evento.justificativa',
                'nfes_aenet_evento.sequencial',
                'nfes_aenet.nfe_chave_acesso'
            )
            ->join('nfes_aenet', 'nfes_aenet.id_nfes_aenet', '=', 'nfes_aenet_evento.id_nfes_aenet')
            ->where('nfes_aenet_evento.justificativa', '<>', '')
            ->where('nfes_aenet.status_nfe', '=', 1)
            ->where('nfes_aenet_evento.tipo', '=', 110110)
            ->get()
            ->toArray();
    }
    
    public function update($id, $astd)
    {
        Evento::where('id', $id)->update($astd);
    }
}
