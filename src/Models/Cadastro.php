<?php
namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cadastro extends Eloquent
{
    public $timestamps = true;
    protected $table = 'cadastros';
    protected $fillable = [
        'id_empresa',
        'cnpj',
        'uf',
        'crtpfx',
        'crtchain',
        'crtpass',
        'crtvalid_to',
        'logo',
        'contingency'
    ];
}
