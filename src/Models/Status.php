<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Status extends Eloquent
{
    public $timestamps = false;
    protected $table = 'sefaz_status';
    protected $fillable = [
        'uf',
        'status_1',
        'error_msg_1',
        'updated_at_1',
        'status_2',
        'error_msg_2',
        'updated_at_2'
    ];
}
