<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class CTe extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_ctes';
    protected $fillable = [
        'id_empresa',
        'nsu',
        'chCTe',
        'cnpj',
        'xNome',
        'dhEmi',
        'content'
    ];
}
