--
-- Database: `aenet_nfe`
--

--
-- Remover se existir tabela anterior `nfes_aenet`
--

DROP TABLE IF EXISTS `nfes_aenet`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nfes_aenet`
-- Responsável: Flávio Caporali
--
CREATE TABLE `nfes_aenet` (
  `id_nfes_aenet` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Nota Fiscal (VEM DO SISTEMA AENET )',
  `id_empresa` int(11) NOT NULL DEFAULT '0' COMMENT '(VEM DO AENET)',
  `tipo_nfe` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tipo da Nota Fiscal E ou S',
  `nome_destinatario` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nome do destinatário da NFe (VEM DO SISTEMA AENET )',
  `data_emissao` datetime DEFAULT NULL COMMENT 'Data de emissão da NFe (VEM DO SISTEMA AENET )',
  `cod_uf` char(2) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Código numérico da UF (VEM DO SISTEMA AENET )',
  `cnpj` int(11) NOT NULL COMMENT 'CNPJ do destinatário (não formatado, só numeros - VEM DO SISTEMA AENET )',
  `email_destinatario` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email do destinatrio (VEM DO SISTEMA AENET )',
  `modelo` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Modelo da NFe (VEM DO SISTEMA AENET )',
  `serie` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Série da NFe (VEM DO SISTEMA AENET )',
  `nr_nota_fiscal` int(11) NOT NULL COMMENT 'Número da NFe (VEM DO SISTEMA AENET )',
  `cd_nr_control` int(11) NOT NULL COMMENT 'Nr. de controle da NFe - é unico e sequencial (VEM DO SISTEMA AENET )',
  `arquivo_nfe_txt` text COLLATE utf8_unicode_ci COMMENT 'Arquivo TXT da NFe  (VEM DO SISTEMA AENET )',
  `lote` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'Lote de NFes (opcional - a princípio não será usado)',
  `protocolo` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'Protocolo da NFe (VEM DO SISTEMA AENET_NFE)',
  `recibo` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT 'Recido da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_chave_acesso` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave de Acesso - USADO para TUDO - (NFE ID )  (VEM DO SISTEMA AENET_NFE)',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Status da NFe código do retorno do serviço SEFAZ (VEM DO SISTEMA AENET_NFE)',
  `motivo` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Motivo da rejeição da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_pdf` text COLLATE utf8_unicode_ci COMMENT 'PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_xml` text COLLATE utf8_unicode_ci COMMENT 'XML retornado da NFe (VEM DO SISTEMA AENET_NFE)',
  `status_nfe` int(11) DEFAULT '0' COMMENT '0 - Nfe a ser processada(VEM DO SISTEMA AENET ); ( 1 - Nfe Pendente de Consulta; 2- Nfe Aprovada; 3 - Denegada    (VEM DO SISTEMA AENET_NFE) )',
  `nfe_danfe_impressa` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'NúmEro de impressões da DANFE (VEM DO SISTEMA AENET_NFE)',
  `nfe_pdf_gerado` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de gerações do PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_email_enviado` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de envios do e-mail da NFe (VEM DO SISTEMA AENET_NFE)',
  `alfa` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Campo Alfa  de apoio',
  `data_envio` datetime DEFAULT NULL COMMENT 'Data de envio a SEFAZ(VEM DO SISTEMA AENET_NFE)',
  `data_recebimento` datetime DEFAULT NULL COMMENT 'Data do recebimento(VEM DO SISTEMA AENET_NFE)',
  `data_email` datetime DEFAULT NULL COMMENT 'Data do Email(VEM DO SISTEMA AENET_NFE)',
  `data_danfe` datetime DEFAULT NULL COMMENT 'Data da Danfe(VEM DO SISTEMA AENET_NFE)',
  `cod_ope_d` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Destinatário Correio (VEM DO SISTEMA AENET )',
  `cod_ope_r` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Remetente  Correio  (VEM DO SISTEMA AENET )',
  `cnpj_emi` int(11) NOT NULL COMMENT 'CNPJ Emitente  (VEM DO SISTEMA AENET)',
  `nro_evento` int(11) NOT NULL COMMENT 'Numero do evento  (VEM DO SISTEMA AENET)',
  `tempo_consulta` int(11) NOT NULL COMMENT 'Tempo da consulta ? (SEM USO)',
  `txt_edi` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '(SEM USO)',
  PRIMARY KEY (`id_nfes_aenet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `nfes_aenet`
--
