<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MailLog extends Eloquent
{
    public $timestamps = false;
    protected $table = 'nfes_email_log';
    protected $fillable = [
        'id_nfes_aenet',
        'data',
        'ip'
    ];
}
