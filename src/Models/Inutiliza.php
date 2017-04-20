<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Inutiliza extends Eloquent
{
    public $timestamps = true;
    protected $table = 'nfe_aenet_inuts';
    protected $fillable = [
        'id_empresa',
        'serie',
        'num_inicial',
        'num_final',
        'justificativa',
        'status',
        'motivo',
        'xml',
        'data'
    ];
}
