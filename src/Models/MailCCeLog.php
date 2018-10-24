<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MailCCeLog extends Eloquent
{
    public $timestamps = false;
    protected $table = 'eventos_email_log';
    protected $fillable = [
        'id_aenet_evento',
        'data',
        'ip'
    ];
}
