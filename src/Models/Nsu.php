<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Nsu extends Eloquent
{
    public $timestamps = false;
    protected $table = 'dfe_nsus';
    protected $fillable = [
        'id',
        'id_empresa',
        'nsu',
        'tipo',
        'manifestar',
        'content'
    ];
}
