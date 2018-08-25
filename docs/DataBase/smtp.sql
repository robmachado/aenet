--
-- Database: `aenet_nfe`
--

--
-- Remover se existir tabela anterior `smtp`
--
DROP TABLE IF EXISTS `smtp`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `smtp`
-- Responsável: Flávio Caporali
--
CREATE TABLE `smtp` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id da tabela', 
  `user` varchar(50) NOT NULL COLLATE utf8_unicode_ci COMMENT 'email address',
  `pass` varchar(50) NOT NULL COLLATE utf8_unicode_ci COMMENT 'email password',
  `host` varchar(100) NOT NULL COLLATE utf8_unicode_ci COMMENT 'SMTP host',
  `security` varchar(10) COLLATE utf8_unicode_ci COMMENT 'Security encription algorithm',
  `port` int(11) NOT NULL COMMENT 'SMTP port',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `parametros`
--
