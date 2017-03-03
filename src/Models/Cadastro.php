<?php
namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cadastro extends Eloquent
{
    public $timestamps = false;
    protected $table = 'cadastros';
    protected $fillable = [
        'id_empresa',
        'cnpj',
        'pfx',
        'senha'
    ];
    
}
