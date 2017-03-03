<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Log extends Eloquent
{
    public $timestamps = false;
    protected $table = 'logs';
    protected $fillable = [
        'id',
        'id_cadastro',
        'operacao',
        'mensagem',
        'created_at'
    ];
}
