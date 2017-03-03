--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
-- Responsável : Roberto
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da Tabela',
  `id_cadastro` int(11) UNSIGNED NOT NULL COMMENT 'Id da Empresas (AENET)',
  `operacao` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Operação executada',
  `mensagem` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Mensagem de log',
  `created_at` datetime NOT NULL COMMENT 'Data e hora da criação do log',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `logs`
--
