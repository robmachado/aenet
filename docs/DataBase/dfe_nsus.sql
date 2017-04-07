--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `dfe_nsus`
--

DROP TABLE IF EXISTS `dfe_nsus`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_nsus`
-- Controle de dados recebidos do webservice de DFe
-- Responsável : Roberto
--

CREATE TABLE `dfe_nsus` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'Id do registro (RECEITA)',
  `tipo` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tipo de registro NSU (RECEITA)',
  `manifestar` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Pendencia de manifestação tipo RESUMO',
  `cnpj` varchar(14) COLLATE utf8_unicode_ci NOT NULL COMMENT 'CNPJ do emissor da NFe',
  `xNome` varchar(150) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Razão do emissor da NFe',
  `chNFe` varchar(44) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave da NFe',
  `dhEmi` datetime NOT NULL COMMENT 'Data e hora de emissão da NFe',
  `nProt` varchar(150) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Protocolo da NFe',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Conteúdo do NSU, já descompactado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `dfe_nsus`
--
