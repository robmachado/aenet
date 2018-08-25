<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Errors extends Eloquent
{
    public $timestamps = false;
    protected $table = 'errors';
    protected $fillable = [
        'cstat',
        'descricao',
        'sucesso',
        'causa',
        'correcao'
    ];
}
