<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Evento extends Eloquent
{
    public $timestamps = false;
    protected $table = 'nfes_aenet_evento';
    protected $fillable = [
        'id_nfes_aenet',
        'tipo',
        'justificativa',
        'sequencial',
        'status',
        'motivo',
        'xml',
        'data'
    ];
}
