<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * txt contem o txt gerado pelo ERP
 * status situação da emissão
 *        0 nada
 *        1 convertido em xml com sucesso
 *        2 assinado com sucesso
 *        3 transmitido com sucesso
 *        4 pdf gerado com sucesso
 *        5 emails enviados com sucesso
 *       99 erro nas operações anteriores ou erro SOAP com o webservice
 * 
 *      
 *        
 */
class Input extends Eloquent
{
    protected $table = 'nfes_inputs';
    protected $fillable = [
        'id',
        'id_empresa',
        'id_nfe_aenet',
        'txt',
        'status',
        'xml',
        'pdf',
        'error_cod',
        'error_msg',
        'created_at',
        'updated_at'
    ];
}
