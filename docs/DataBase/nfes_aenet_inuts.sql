-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 19-Abr-2017 às 17:19
-- Versão do servidor: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.17-2+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aenet_nfe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `nfes_aenet_inuts`
--

CREATE TABLE `nfes_aenet_inuts` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id da tabela',
  `id_empresa` int(11) NOT NULL,
  `serie` int(11) NOT NULL COMMENT 'Numero de serie da NFe',
  `num_inicial` int(11) NOT NULL,
  `num_final` int(11) NOT NULL,
  `justificativa` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '''''',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `motivo` text COLLATE utf8_unicode_ci NOT NULL,
  `xml` text COLLATE utf8_unicode_ci NOT NULL,
  `data` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Controle de inutilização de faixa de numeros';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nfes_aenet_inuts`
--
ALTER TABLE `nfes_aenet_inuts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nfes_aenet_inuts`
--
ALTER TABLE `nfes_aenet_inuts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id da tabela';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
