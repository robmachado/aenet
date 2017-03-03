<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Event extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_events';
    protected $fillable = [
        'id',
        'id_cadastro',
        'nsu',
        'cnp',
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
