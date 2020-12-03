<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class NsuCTe extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_nsus_cte';
    protected $fillable = [
        'id_empresa',
        'nsu',
        'tipo',
        'manifestar',
        'chCTe',
        'cnpj',
        'xNome',
        'dhEmi',
        'nProt',
        'content'
    ];
}
