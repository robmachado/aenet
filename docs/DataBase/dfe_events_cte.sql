--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `dfe_events`
--

DROP TABLE IF EXISTS `dfe_events_cte`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `dfe_events_cte`
-- Registro dos eventos baixados do DFe
-- Responsável : Roberto
--

CREATE TABLE `dfe_events_cte` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `nsu` int(11) NOT NULL COMMENT 'NSU referencia (RECEITA)',
  `chCTe` varchar(44) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave da CTe referente ao evento',
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
