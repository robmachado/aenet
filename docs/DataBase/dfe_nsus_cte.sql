--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `dfe_nsus`
--

DROP TABLE IF EXISTS `dfe_nsus_cte`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_nsus`
-- Controle de dados recebidos do webservice de DFe
-- Responsável : Roberto
--

CREATE TABLE `dfe_nsus_cte` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'Id do registro (RECEITA)',
  `tipo` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Tipo de registro NSU (RECEITA)',
  `chCTe` varchar(44) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave da CTe',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Conteúdo do NSU, já descompactado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `aenet_nfe`.`dfe_nsus_cte` ADD INDEX `k_nsu` (`nsu`, `id_empresa`);

--
-- Indexes for table `dfe_nsus`
--
