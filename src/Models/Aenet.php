<?php

namespace Aenet\NFe\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Aenet extends Eloquent
{
    public $timestamps = false;
    protected $table = 'nfes_aenet';
    protected $fillable = [
        'id_nfes_aenet',
        'id_dados_nfe',
        'tipo_nfe',
        'nome_destinatario',
        'data_emissao',
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
        'cancelamento_chave_acesso',
        'cancelamento_protocolo',
        'cancelamento_xml',
        'nfe_cancelada',
        'nfe_danfe_impressa',
        'nfe_pdf_gerado',
        'nfe_email_enviado',
        'alfa',
        'solicita_cancelamento',
        'data_cancelamento',
        'data_envio',
        'data_recebimento',
        'data_email',
        'data_danfe',
        'cod_ope_d',
        'cod_ope_r',
        'cnpj_emi',
        'nro_evento',
        'tempo_consulta',
        'txt_edi'
    ];
}
