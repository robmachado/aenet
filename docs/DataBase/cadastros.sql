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
  `pfx` text NOT NULL COMMENT 'Conteúdo do PFX em base64',
  `senha` varchar(30) NOT NULL COMMENT 'Senha de acesso ao certificado',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `cadastros`
--
ALTER TABLE `cadastros` ADD UNIQUE(`cnpj`);