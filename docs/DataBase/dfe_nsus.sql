--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_nsus`
-- Responsável : Roberto
--

CREATE TABLE `dfe_nsus` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'Id do registro (RECEITA)',
  `tipo` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tipo de registro NSU (RECEITA)',
  `manifestar` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Pendencia de manifestação tipo RESUMO',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Conteúdo do NSU, já descompactado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `dfe_nsus`
--