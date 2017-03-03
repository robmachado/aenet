<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class NFe extends Eloquent
{
    public $timestamps = false;
    protected $table = 'nfes';
    protected $fillable = [
        'id',
        'id_cadastro',
        'nsu',
        'cnpj',
        'chNFe',
        'content'
    ];
}
