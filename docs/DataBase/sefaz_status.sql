--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `sefaz_status`
--

DROP TABLE IF EXISTS `sefaz_status`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sefaz_status`
-- Controle de status das autorizadoras da SEFAZ e Contingência SVC
-- Responsável : Roberto
--

CREATE TABLE `sefaz_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da tabela',
  `uf` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Sigla do estado ou SVC',
  `status_1` tinyint(4) NOT NULL COMMENT 'Status da SEFAZ Produção 1-Online ou 0-Offline',
  `error_msg_1` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mensagem de erro, normalmente vinculada a serviço fora do ar',
  `updated_at_1` datetime NOT NULL COMMENT 'Data e hora da última atualização',
  `status_2` tinyint(4) NOT NULL COMMENT 'Status da SEFAZ Homologação 1-Online ou 0-Offline',
  `error_msg_2` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mensagem de erro, normalmente vinculada a serviço fora do ar',
  `updated_at_2` datetime NOT NULL COMMENT 'Data e hora da última atualização',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Registra o status da SEFAZ';

--
-- Extraindo dados da tabela `sefaz_status`
--

INSERT INTO `sefaz_status` (`uf`, `status_1`, `error_msg_1`, `updated_at_1`, `status_2`, `error_msg_2`, `updated_at_2`) VALUES
('AC', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('AL', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('AM', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('AP', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('BA', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('CE', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('DF', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('ES', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('GO', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('MA', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('MG', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('MS', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('MT', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('PA', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('PB', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('PE', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('PI', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('PR', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('RJ', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('RN', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('RO', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('RR', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('RS', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('SC', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('SE', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('SP', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('TO', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('SVCAN', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10'),
('SVCRS', 0, '', '2017-03-05 10:55:10', 0, '', '2017-03-05 10:55:10');

--
-- Indexes for dumped tables
--
ALTER TABLE `sefaz_status` ADD UNIQUE(`uf`);