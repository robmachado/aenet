<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cadastro extends Eloquent
{
    public $timestamps = true;
    protected $table = 'cadastros';
    protected $fillable = [
        'id_empresa',
        'fantasia',
        'razao',
        'cnpj',
        'uf',
        'crtpfx',
        'crtchain',
        'crtpass',
        'crtvalid_to',
        'tpAmb',
        'schema',
        'version',
        'logo',
        'contingency',
        'emailfrom',
        'error',
        'created_at',
        'updated_at'
    ];
}
