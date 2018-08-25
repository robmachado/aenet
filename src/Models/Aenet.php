<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Aenet extends Eloquent
{
    public $timestamps = false;
    protected $table = 'nfes_aenet';
    protected $fillable = [
        'id_nfes_aenet',
        'id_empresa',
        'tipo_nfe',
        'nome_destinatario',
        'data_emissao',
        'data_emissao_h',
        'cod_uf',
        'cnpj',
        'email_destinatario',
        'modelo',
        'serie',
        'nr_nota_fiscal',
        'cd_nr_control',
        'arquivo_nfe_txt',
        'justificativa',
        'lote',
        'protocolo',
        'recibo',
        'nfe_chave_acesso',
        'status',
        'motivo',
        'arquivo_nfe_pdf',
        'arquivo_nfe_xml',
        'status_nfe',
        'nfe_danfe_impressa',
        'nfe_pdf_gerado',
        'nfe_email_enviado',
        'alfa',
        'data_envio',
        'data_envio_h',
        'data_recebimento',
        'data_recebimento_h',
        'data_email',
        'data_email_h',
        'data_danfe',
        'data_danfe_h',
        'cod_op_d',
        'cod_op_r',
        'cnpj_emi',
        'nro_evento',
        'tempo_consulta',
        'txt_edi',
        'origem'
    ];
}
