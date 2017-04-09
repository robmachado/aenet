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
  `fantasia` varchar(50) NOT NULL COMMENT 'Nome da empresa',
  `razao` varchar(50) NOT NULL COMMENT 'Razão social da empresa', 
  `cnpj` varchar(14) NOT NULL COMMENT 'CNPJ da empresa',
  `uf` varchar(2) NOT NULL COMMENT 'UF da empresa',
  `crtpfx` text NOT NULL COMMENT 'Conteúdo do PFX em base64',
  `crtchain` text DEFAULT NULL COMMENT 'Certificados da cadeia de certificação em PEM',
  `crtpass` varchar(30) NOT NULL COMMENT 'Senha de acesso ao certificado',
  `crtvalid_to` datetime DEFAULT NULL COMMENT 'Data e hora da validade do certificado',
  `tpAmb` int(11) NOT NULL DEFAULT '2' COMMENT 'Força o tipo de ambiente a ser usado 2-homologação ou 1-produção',
  `logo` text DEFAULT NULL COMMENT 'Logo marca JPG ou PNG em base64 para uso nos PDFs',
  `contingency` text COMMENT 'Dados de contingência json base64',
  `emailfrom` varchar(50) NOT NULL COMMENT 'Email do emitente para NFe',
  `error` text NOT NULL COMMENT 'Mensagens de erro do cadastro', 
  `created_at` datetime DEFAULT NULL COMMENT 'Data e hora da criação do registro',
  `updated_at` datetime DEFAULT NULL COMMENT 'Data e hora da última alteração do registro',
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `cadastros`
--
ALTER TABLE `cadastros` ADD UNIQUE(`cnpj`);

