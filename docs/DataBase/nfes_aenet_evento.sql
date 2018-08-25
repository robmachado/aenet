-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 19-Abr-2017 às 17:18
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
-- Estrutura da tabela `nfes_aenet_evento`
--

CREATE TABLE `nfes_aenet_evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id da tabela',
  `id_nfes_aenet` int(11) NOT NULL COMMENT 'id da tabela nfes_aenet',
  `tipo` int(11) NOT NULL COMMENT 'tipo de evento, 110110 para CCe',
  `justificativa` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Justificativa da correção ',
  `sequencial` int(11) NOT NULL DEFAULT '1' COMMENT 'numero sequencial',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Status do envio',
  `motivo` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'motivo de erros',
  `xml` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'XML protocolado',
  `data` datetime DEFAULT NULL COMMENT 'Data do evento',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Controle de eventos de NFe como Carta de Correção';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nfes_aenet_evento`
--
ALTER TABLE `nfes_aenet_evento`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nfes_aenet_evento`
--
ALTER TABLE `nfes_aenet_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id da tabela';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
