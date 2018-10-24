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
    
    public function dacceAll()
    {
        return Evento::whereNull('nfes_aenet_evento.pdf_gerado')
            ->where('nfes_aenet_evento.tipo', '=', 110110)
            ->where('nfes_aenet_evento.status', '=', '1')
            ->whereNotNull('xml')
            ->join('nfes_aenet', 'nfes_aenet.id_nfes_aenet', '=', 'nfes_aenet_evento.id_nfes_aenet')
            ->select(
                'nfes_aenet_evento.id',
                'nfes_aenet_evento.xml',
                'nfes_aenet.id_empresa',
                'nfes_aenet.arquivo_nfe_xml'
            )
            ->orderBy('nfes_aenet.id_empresa', 'asc')
            ->orderBy('nfes_aenet_evento.id_nfes_aenet', 'asc')
            ->orderBy('nfes_aenet_evento.sequencial', 'asc')
            ->get()
            ->toArray();
    }
    
    public function emailAll()
    {
        return Evento::whereNull('evento_email_enviado')
            ->where('nfes_aenet_evento.tipo', '=', 110110)
            ->where('pdf_gerado', '=', '1')
            ->join('nfes_aenet', 'nfes_aenet.id_nfes_aenet', '=', 'nfes_aenet_evento.id_nfes_aenet')
            ->select(
                'nfes_aenet_evento.id',
                'nfes_aenet_evento.xml',
                'nfes_aenet_evento.arquivo_evento_pdf',
                'nfes_aenet.email_destinatario',
                'nfes_aenet.id_empresa'
            )
            ->orderBy('nfes_aenet.id_empresa', 'asc')
            ->orderBy('nfes_aenet_evento.id_nfes_aenet', 'asc')
            ->orderBy('nfes_aenet_evento.sequencial', 'asc')
            ->get()
            ->toArray();
    }
}
