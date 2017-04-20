<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cancela extends Eloquent
{
    public $timestamps = true;
    protected $table = 'nfe_aenet_cancel';
    protected $fillable = [
        'id_nfes_aenet',
        'justificativa',
        'status',
        'motivo',
        'xml',
        'data'
    ];
}
