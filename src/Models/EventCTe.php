<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EventCTe extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_events_cte';
    protected $fillable = [
        'id_cadastro',
        'nsu',
        'cnpj',
        'chCTe',
        'tpEvento',
        'nSeqEvento',
        'xEvento',
        'dhEvento',
        'dhRecbto',
        'nProt',
        'content'
    ];
}
