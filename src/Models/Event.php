<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Event extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_events';
    protected $fillable = [
        'id_cadastro',
        'nsu',
        'cnpj',
        'chNFe',
        'tpEvento',
        'nSeqEvento',
        'xEvento',
        'dhEvento',
        'dhRecbto',
        'nProt',
        'content'
    ];
}
