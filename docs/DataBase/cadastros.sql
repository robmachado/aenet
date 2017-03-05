--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Remover se existir tabela anterior `cadastros`
--

DROP TABLE IF EXISTS `cadastros`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cadastros`
-- Cadastro de emitentes do sistema
-- Responsável : Roberto
--

CREATE TABLE `cadastros` (
  `id_empresa` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresa (AENET)',
  `cnpj` varchar(14) NOT NULL COMMENT 'CNPJ da empresa',
  `crtpfx` text NOT NULL COMMENT 'Conteúdo do PFX em base64',
  `crtchain` text COMMENT 'Certificados da cadeia de certificação em PEM',
  `crtpass` varchar(30) NOT NULL COMMENT 'Senha de acesso ao certificado',
  `logo` text COMMENT 'Logo marca JPG ou PNG em base64 para uso nos PDFs',
  `contingency` text COMMENT 'Dados de contingência json base64',
  `created_at` datetime NOT NULL COMMENT 'Data e hora da criação do registro',
  `updated_at` datetime NOT NULL COMMENT 'Data e hora da última alteração do registro',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `cadastros`
--
ALTER TABLE `cadastros` ADD UNIQUE(`cnpj`);