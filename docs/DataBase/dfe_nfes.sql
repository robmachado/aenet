--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `dfe_nfes`
--

DROP TABLE IF EXISTS `dfe_nfes`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_nfes`
-- Registro das NFe baixadas do DFe
-- Responsável : Roberto
--

CREATE TABLE `dfe_nfes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_cadastro` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresa (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'NSU referencia (RECEITA)',
  `cnpj` varchar(14) COLLATE utf8_unicode_ci NOT NULL COMMENT 'CNPJ do emissor da NFe',
  `xNome` varchar(150) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Razão do emissor da NFe',
  `chNFe` varchar(44) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave da NFe',
  `dhEmi` datetime NOT NULL COMMENT 'Data e hora de emissão da NFe',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'XML da NFe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `dfe_nfes`
--
