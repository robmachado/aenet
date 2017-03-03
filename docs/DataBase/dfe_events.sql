--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_events`
-- Responsável : Roberto
--

CREATE TABLE `dfe_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_cadastro` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'NSU referencia (RECEITA)',
  `cnpj` varchar(14) COLLATE utf8_unicode_ci NOT NULL COMMENT 'CNPJ do emissor do evento',
  `chNFe` varchar(44) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave da NFe referente ao evento',
  `tpEvento` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código do Tipo de evento',
  `nSeqEvento` int(11) NOT NULL COMMENT 'Numero sequencial do evento',
  `xEvento` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Descrição do evento',
  `dhEvento` datetime NOT NULL COMMENT 'Data e hora de emissão do evento',
  `dhRecbto` datetime NOT NULL COMMENT 'Data e hora do recebimento do evento na SEFAZ',
  `nProt` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Numero do protocolo do evento, se houver',
  `content` longtext COLLATE utf8_unicode_ci COMMENT 'XML do evento',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `dfe_events`
--
