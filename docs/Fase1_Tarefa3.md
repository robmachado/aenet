#Fase 1

###Objetivo:

Emissão automática de NFe, a partir de TXT gravado em base de dados

##Tarefa 3 Definir banco de dados e tabelas necessárias

Para essa função estão especificados 3 tabelas na base de dados:

###aenet_nfe.nfes_aenet

Esta tabela é de uso excusivo do sistema AENET, tanto para inclusão, alteração e leitura. 
Essa tabela interage com o frontend do sistema AENET.

```mysql
--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `nfes_aenet`
-- Responsável: Flávio Caporali
--

CREATE TABLE `nfes_aenet` (
  `id_nfes_aenet` int(11) NOT NULL,
  `id_dados_nfe` int(11) NOT NULL COMMENT 'Id da Nota Fiscal (VEM DO SISTEMA AENET)',
  `tipo_nfe` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tipo da Nota Fiscal',
  `nome_destinatario` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nome do destinatário da NFe (VEM DO SISTEMA AENET)',
  `data_emissao` datetime DEFAULT NULL COMMENT 'Data de emissão da NFe (VEM DO SISTEMA AENET)',
  `cod_uf` char(2) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Código numérico da UF (VEM DO SISTEMA AENET)',
  `cnpj` int(11) NOT NULL COMMENT 'CNPJ do destinatário (não formatado, só numeros - VEM DO SISTEMA AENET)',
  `email_destinatario` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email do destinatrio (VEM DO SISTEMA AENET)',
  `modelo` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Modelo da NFe (VEM DO SISTEMA AENET)',
  `serie` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Série da NFe (VEM DO SISTEMA AENET)',
  `nr_nota_fiscal` int(11) NOT NULL COMMENT 'Número da NFe (VEM DO SISTEMA AENET)',
  `cd_nr_control` int(11) NOT NULL COMMENT 'Nr. de controle da NFe - é unico e sequencial (VEM DO SISTEMA AENET)',
  `arquivo_nfe_txt` text COLLATE utf8_unicode_ci COMMENT 'Arquivo TXT da NFe  (VEM DO SISTEMA AENET)',
  `justificativa` text COLLATE utf8_unicode_ci COMMENT 'Justificativa do cancelamento da NFe (VEM DO SISTEMA AENET)',
  `lote` int(11) NOT NULL COMMENT 'Lote de NFes (opcional - a princípio não será usado)',
  `protocolo` int(11) NOT NULL COMMENT 'Protocolo da NFe (VEM DO SISTEMA AENET_NFE)',
  `recibo` int(11) NOT NULL COMMENT 'Recido da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_chave_acesso` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave de Acesso - USADO para TUDO - (NFE ID )  (VEM DO SISTEMA AENET_NFE)',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Status da NFe código do retorno do serviço SEFAZ (VEM DO SISTEMA AENET_NFE)',
  `motivo` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Motivo da rejeição da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_pdf` text COLLATE utf8_unicode_ci COMMENT 'PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_xml` text COLLATE utf8_unicode_ci COMMENT 'XML retornado da NFe (VEM DO SISTEMA AENET_NFE)',
  `status_nfe` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '0 - Nfe a ser processada(VEM DO SISTEMA AENET); ( 1 - Nfe Pendente de Consulta; 2- Nfe Aprovada; 3 - Denegada    (VEM DO SISTEMA AENET_NFE) )',
  `cancelamento_chave_acesso` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave de Acesso - USADO para TUDO - (NFE ID ) (VEM DO SISTEMA AENET_NFE)',
  `cancelamento_protocolo` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Protocolo de cancelamento da NFe  (VEM DO SISTEMA AENET_NFE)',
  `nfe_cancelada` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Check box indicando que a nota foi cancelada  (VEM DO SISTEMA AENET_NFE)',
  `nfe_danfe_impressa` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'NúmEro de impressões da DANFE (VEM DO SISTEMA AENET_NFE)',
  `nfe_pdf_gerado` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de gerações do PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_email_enviado` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de envios do e-mail da NFe (VEM DO SISTEMA AENET_NFE)',
  `alfa` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Campo Alfa  de apoio',
  `solicita_cancelamento` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Solicitado o cancelamento (VEM DO SISTEMA AENET)',
  `data_cancelamento` datetime DEFAULT NULL COMMENT 'Data do cancelamento(VEM DO SISTEMA AENET_NFE)',
  `data_envio` datetime DEFAULT NULL COMMENT 'Data de envio a SEFAZ(VEM DO SISTEMA AENET_NFE)',
  `data_recebimento` datetime DEFAULT NULL COMMENT 'Data do recebimento(VEM DO SISTEMA AENET_NFE)',
  `data_email` datetime DEFAULT NULL COMMENT 'Data do Email(VEM DO SISTEMA AENET_NFE)',
  `data_danfe` datetime DEFAULT NULL COMMENT 'Data da Danfe(VEM DO SISTEMA AENET_NFE)',
  `cod_ope_d` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Destinatário Correio (VEM DO SISTEMA AENET)',
  `cod_ope_r` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Remetente  Correio  (VEM DO SISTEMA AENET)',
  `cnpj_emi` int(11) NOT NULL COMMENT 'CNPJ Emitente  (VEM DO SISTEMA AENET)',
  `nro_evento` int(11) NOT NULL COMMENT 'Numero do evento  (VEM DO SISTEMA AENET)',
  `tempo_consulta` int(11) NOT NULL COMMENT 'Tempo da consulta ? (SEM USO)',
  `txt_edi` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'SEM USO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `nfes_aenet`
--

```

###aenet_nfe.cadastros

É de responsabilidade do sistema AENET carregar e manter atualizados os dados desta tabela.
O sistema AENET deve carregar todos os campos obrigatórios.
Exceto o "sefazstatus" que é uma função do aplicativo e ao incluir um registro esse campo deve permanecer como ZERO.
Esse campo será usado para gerenciar a situação da SEFAZ e caso a mesma esteja "OFF LINE" nenhum envio será feito, a menos que a contingência seja ativada e ativa ou seja com "sefazstatus" = 1.
A busca do status da SEFAZ irá ocorrer sempre que o compo contingência for alterado ou a cada 5 minutos através do cronjob

A contingência é ativada EMPRESA por EMPRESA e não por unidade da SEFAZ e essa ativação deverá ser feita de forma MANUAL pois implica em mudanças no TXT.

```mysql
--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cadastros`
-- Responsável : Roberto
--

CREATE TABLE `cadastros` (
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `cnpj` varchar(14) NOT NULL COMMENT 'CNPJ da empresa',
  `crtpfx` text NOT NULL COMMENT 'Conteúdo do PFX em base64',
  `crtchain` text COMMENT 'Certificados da cadeia de certificação em PEM',
  `crtpass` varchar(30) NOT NULL COMMENT 'Senha de acesso ao certificado',
  `logo` text COMMENT 'Logo marca JPG ou PNG em base64 para uso nos PDFs',
  `contingency` text COMMENT 'Dados de contingência json base64',
  `sefazstatus` text COMMENT 'Dados referentes ao staus da SEFAZ json base64',
  `created_at` datetime NOT NULL COMMENT 'Data e hora da criação do registro',
  `updated_at` datetime NOT NULL COMMENT 'Data e hora da última alteração do registro',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `cadastros`
--
ALTER TABLE `cadastros` ADD UNIQUE(`cnpj`);
```

###aenet_nfe.nfes_inputs

Esta tabela é usada para gerenciar os processos de comunicação com a SEFAZ.
Quando é criada uma NFe, Carta de Correção, Cancelamento, Inutilização de numero, ou manifestação de destinatário, esta tabela receberá uma inclusão por parte do sistema AENET.

```mysql
--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `nfes_inputs`
-- Responsável : Roberto
--

CREATE TABLE `nfes_inputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `id_nfe_aenet` int(11) UNSIGNED NOT NULL COMMENT 'Id da NFe (AENET)',
  `txt` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'TXT referente ao documento a enviar (AENET)',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Flag de resultado do processamento',
  `xml` longtext COLLATE utf8_unicode_ci COMMENT 'XML com o protocolo da mensagem enviada',
  `pdf` longtext COLLATE utf8_unicode_ci COMMENT 'PDF do documento em base64',
  `error_cod` int(11) NOT NULL DEFAULT '0' COMMENT 'Código de erro retornado pela SEFAZ',
  `error_msg` text COLLATE utf8_unicode_ci COMMENT 'Descrição de erro retornado pela SEFAZ',
  `created_at` datetime NOT NULL COMMENT 'Data e hora da criação do registro',
  `updated_at` datetime NOT NULL COMMENT 'Data e hora da última alteração do registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Inputs de notas em txt a serem processadas';

--
-- Indexes for table `nfes_inputs`
--

```

O aplicativo irá varrer esta tabela em busca de novos campos ainda não tratados com status = 0 
Todos os registros com status = 0 de um mesmo id_empresa, serão processados em sequencia. 
O aplicativo irá obter o certificado referente ao id_empresa (tabela cadastros) e processará o TXT, para gerar as NFe, Cartas de correção, Manifestação, Cancelamento ou Inutilização
- converter TXT em XML (apenas para as NFe)
- assinar o XML com o certificado digital
- validar o XML com o respectivo XSD, caso haja erro o status = 2 e são alterados os campos error_cod, error_msg e updated_at

Registros com status = 2 (com ERRO) não serão removidos da tabela e ficarão no aguardo de novo registro. Quando um novo registro com o mesmo id_nfe_aenet para um mesmo id_empresa for criado, o registro indicando erro será removido e o processamento continuará com o próximo registro.

- em caso de sucesso, o aplicativo irá protocolar o XML assinado ou corrigir seu dados em caso de cancelamento
- gerar o PDF relativo ao documento (DANFE, DACCE ou DANFE com cancelamento), outros elevneto não geram PDF 
- caso seja um NFe o aplicativo irá enviar os emails aos destinatários indicados no TXT