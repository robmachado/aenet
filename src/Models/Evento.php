<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Evento extends Eloquent
{
    public $timestamps = true;
    protected $table = 'nfe_aenet_evento';
    protected $fillable = [
        'id_nfes_aenet',
        'justificativa',
        'sequencial',
        'status',
        'motivo',
        'xml',
        'data'
    ];
}
