<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Smtp extends Eloquent
{
    public $timestamps = false;
    protected $table = 'smtp';
    protected $fillable = [
        'user',
        'pass',
        'host',
        'security',
        'port'
    ];
}
