<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Monitor extends Eloquent
{
    public $timestamps = false;
    protected $table = 'monitor';
    protected $fillable = [
        'job',
        'comments',
        'dtInicio',
        'dtFim'
    ];
}
