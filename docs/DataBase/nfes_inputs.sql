--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `nfes_inputs`
--

DROP TABLE IF EXISTS `nfes_inputs`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nfes_inputs`
-- Controle do processo de envio de solicitações a SEFAZ
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
