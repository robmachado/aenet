-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: aenet_nfe
-- ------------------------------------------------------
-- Server version	5.5.54-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cadastro`
--

DROP TABLE IF EXISTS `cadastro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cadastro` (
  `id` int(11) NOT NULL,
  `cnpj` int(11) NOT NULL,
  `pfx` int(11) NOT NULL,
  `senha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cadastro`
--

LOCK TABLES `cadastro` WRITE;
/*!40000 ALTER TABLE `cadastro` DISABLE KEYS */;
/*!40000 ALTER TABLE `cadastro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `xx` int(11) NOT NULL,
  `xxx` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inputs`
--

DROP TABLE IF EXISTS `inputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txt` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `xml` text COLLATE utf8_unicode_ci,
  `pdf` text COLLATE utf8_unicode_ci,
  `error_cod` int(11) DEFAULT NULL,
  `error_msg` text COLLATE utf8_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_nfe_aenet` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `K_id_empresa` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Inputs de notas em txt a serem processadas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inputs`
--

LOCK TABLES `inputs` WRITE;
/*!40000 ALTER TABLE `inputs` DISABLE KEYS */;
INSERT INTO `inputs` VALUES (1,'NOTAFISCAL|1\r\nA|3.10|NFe35160615071307000150550010000001451000000000|\r\nB|35|00000000|RETORNO DE COMODATO|0|55|1|145|2016-06-30T14:42:00-03:00|2016-06-30T14:42:00-03:00|1|2|3539301|1|1|0|1|1|0|0|3|3.10.86|||\r\nC|VIA MASTER ANTENA DIGITAL E TELEFONIA EIRELI EPP||536052246119||||3|\r\nC02|15071307000150|\r\nC05|RUA AMADOR BUENO|394|LOJA CLARO|CENTRO|3539301|Pirassununga|SP|13631080|1058|BRASIL|01921340134|\r\nE|EMBRATEL TVSAT TELECOMUNICAÇÕES SA|1|78387548||||\r\nE02|09132659000176|\r\nE05|RUA EMBAU|2207|MODULOS 1 E 2 PARTE|PARQUE COLUMBIA|3304557|Rio de Janeiro|RJ|21535000|1058|BRASIL||\r\nH|1||\r\nI|70079231||RECPTR DECOD IRD KU C/ SMART CARD - N5166|85287119||6909|PC|79.0000|114.0000000000|9006.00||PC|79.0000|114.0000000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|2||\r\nI|70076632||RECPTR N8102TH (SBTVD-T HD) C/ SMART CARD|85287119||6909|PC|1.0000|258.1600000000|258.16||PC|1.0000|258.1600000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|3||\r\nI|70079230||RECPTR DEC IRD BANDA KU+C C/ SMART CARD|85287119||6909|PC|23.0000|185.0000000000|4255.00||PC|23.0000|185.0000000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|4||\r\nI|70082840||RECPTR DECD IRD STB HDKU PVB500GB|85287119||6909|PC|10.0000|383.8100000000|3838.10||PC|10.0000|383.8100000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|5||\r\nI|70087240||REC DECOD IRD N5266S DVB-S C/ SMART CARD|85287119||6909|PC|94.0000|114.0000000000|10716.00||PC|94.0000|114.0000000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|6||\r\nI|70090145||REC DECOD IRD DVB-S C/ SMART CARD - DS220|85287119||6909|PC|112.0000|127.6900000000|14301.28||PC|112.0000|127.6900000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|7||\r\nI|70090147||REC DC IRD DVB-S C/ SMART CRD-DSR2231/78|85287119||6909|PC|3.0000|129.0500000000|387.15||PC|3.0000|129.0500000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|8||\r\nI|70095807||REC DECODF IRD DVB-S C/ SMART CRD - N5366S|85287119||6909|PC|55.0000|130.0000000000|7150.00||PC|55.0000|130.0000000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|9||\r\nI|70095808||REC DECDF IRD DVB-S C/ SMART CRD - DS222|85287119||6909|PC|50.0000|133.8500000000|6692.50||PC|50.0000|133.8500000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|10||\r\nI|70097672||REC DEC IRC C/ SMART CRD N8760H|85287119||6909|PC|21.0000|246.6600000000|5179.86||PC|21.0000|246.6600000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|11||\r\nI|70099182||REC DEC HD ISDBT IRD C/ SC - DST722EBT|85287119||6909|PC|7.0000|221.4400000000|1550.08||PC|7.0000|221.4400000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|12||\r\nI|70099183||REC IRD ISDBT IRD C/SC - DST 810 EBT|85287119||6909|PC|14.0000|538.8300000000|7543.62||PC|14.0000|538.8300000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|13||\r\nI|70107795||REC DEC HD ISDBT IRD C/SC - DSTI74|85287119||6909|PC|3.0000|207.9000000000|623.70||PC|3.0000|207.9000000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nH|14||\r\nI|70114292||REC DEC HD ISDBT IRD C/SC DST722EBT2|85287119||6909|PC|6.0000|209.1100000000|1254.66||PC|6.0000|209.1100000000|||||1||||\r\nM||\r\nN|\r\nN06|0|41|||\r\nO|||||999|\r\nO08|53|\r\nQ|\r\nQ04|08|\r\nS|\r\nS04|08|\r\nW|\r\nW02|0.00|0.00|0.00|0.00|0.00|72756.11|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|72756.11|0.00|\r\nW04c|0.00|\r\nW04e|0.00|\r\nW04g|0.00|\r\nX|1|\r\nX03|CELISTICS TRANSATLANTIC SP|79757136|RUA EMABAU, 2207, PAVUNA|Rio de Janeiro|RJ|\r\nX04|15163296000210|\r\nX26|28|CAIXAS|VÁRIAS||573.600|573.600|\r\nZ||NÃO INCIDENCIA AO ICMS COM REFERENCIA ART 7º INCISO IX DO IRCMS/SP. CELISTICS TRANSATLANTIC SÃO PAULO ARMAZEM GERAL E OPERADORES LOGISTICOS LTDA - ENDEREÇO: RUA EMBAU, Nº 2207 - PAVUNA - CEP 21335-000 - RIO DE JANEIRO - CNPJ 15.163.296/0002-10 IE 79757136 ******** DMT 913761 / 913767 - FAXTR: 9784|',0,NULL,NULL,NULL,NULL,'2017-02-22 14:01:34',NULL,NULL,NULL);
/*!40000 ALTER TABLE `inputs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `input_id` int(11) NOT NULL,
  `operation` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nfes`
--

DROP TABLE IF EXISTS `nfes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nfes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `xx` int(11) NOT NULL,
  `xxx` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nfes`
--

LOCK TABLES `nfes` WRITE;
/*!40000 ALTER TABLE `nfes` DISABLE KEYS */;
/*!40000 ALTER TABLE `nfes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nfes_aenet`
--

DROP TABLE IF EXISTS `nfes_aenet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nfes_aenet` (
  `id_nfes_aenet` int(11) NOT NULL,
  `id_dados_nfe` int(11) NOT NULL COMMENT 'Id da Nota Fiscal (VEM DO SISTEMA AENET )',
  `tipo_nfe` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tipo da Nota Fiscal',
  `nome_destinatario` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nome do destinatário da NFe (VEM DO SISTEMA AENET )',
  `data_emissao` datetime DEFAULT NULL COMMENT 'Data de emissão da NFe (VEM DO SISTEMA AENET )',
  `cod_uf` char(2) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Código numérico da UF (VEM DO SISTEMA AENET )',
  `cnpj` int(11) NOT NULL COMMENT 'CNPJ do destinatário (não formatado, só numeros - VEM DO SISTEMA AENET )',
  `email_destinatario` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Email do destinatrio (VEM DO SISTEMA AENET )',
  `modelo` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Modelo da NFe (VEM DO SISTEMA AENET )',
  `serie` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Série da NFe (VEM DO SISTEMA AENET )',
  `nr_nota_fiscal` int(11) NOT NULL COMMENT 'Número da NFe (VEM DO SISTEMA AENET )',
  `cd_nr_control` int(11) NOT NULL COMMENT 'Nr. de controle da NFe - é unico e sequencial (VEM DO SISTEMA AENET )',
  `arquivo_nfe_txt` text COLLATE utf8_unicode_ci COMMENT 'Arquivo TXT da NFe  (VEM DO SISTEMA AENET )',
  `justificativa` text COLLATE utf8_unicode_ci COMMENT 'Justificativa do cancelamento da NFe (VEM DO SISTEMA AENET )',
  `lote` int(11) NOT NULL COMMENT 'Lote de NFes (opcional - a princípio não será usado)',
  `protocolo` int(11) NOT NULL COMMENT 'Protocolo da NFe (VEM DO SISTEMA AENET_NFE)',
  `recibo` int(11) NOT NULL COMMENT 'Recido da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_chave_acesso` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave de Acesso - USADO para TUDO - (NFE ID )  (VEM DO SISTEMA AENET_NFE)',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Status da NFe código do retorno do serviço SEFAZ (VEM DO SISTEMA AENET_NFE)',
  `motivo` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Motivo da rejeição da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_pdf` text COLLATE utf8_unicode_ci COMMENT 'PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `arquivo_nfe_xml` text COLLATE utf8_unicode_ci COMMENT 'XML retornado da NFe (VEM DO SISTEMA AENET_NFE)',
  `status_nfe` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '0 - Nfe a ser processada(VEM DO SISTEMA AENET ); ( 1 - Nfe Pendente de Consulta; 2- Nfe Aprovada; 3 - Denegada    (VEM DO SISTEMA AENET_NFE) )',
  `cancelamento_chave_acesso` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Chave de Acesso - USADO para TUDO - (NFE ID ) (VEM DO SISTEMA AENET_NFE)',
  `cancelamento_protocolo` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Protocolo de cancelamento da NFe  (VEM DO SISTEMA AENET_NFE)',
  `nfe_cancelada` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Check box indicando que a nota foi cancelada  (VEM DO SISTEMA AENET_NFE)',
  `nfe_danfe_impressa` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'NúmEro de impressões da DANFE (VEM DO SISTEMA AENET_NFE)',
  `nfe_pdf_gerado` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de gerações do PDF da NFe (VEM DO SISTEMA AENET_NFE)',
  `nfe_email_enviado` char(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nr. de envios do e-mail da NFe (VEM DO SISTEMA AENET_NFE)',
  `alfa` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Campo Alfa  de apoio',
  `solicita_cancelamento` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Solicitado o cancelamento (VEM DO SISTEMA AENET )',
  `data_cancelamento` datetime DEFAULT NULL COMMENT 'Data do cancelamento(VEM DO SISTEMA AENET_NFE)',
  `data_envio` datetime DEFAULT NULL COMMENT 'Data de envio a SEFAZ(VEM DO SISTEMA AENET_NFE)',
  `data_recebimento` datetime DEFAULT NULL COMMENT 'Data do recebimento(VEM DO SISTEMA AENET_NFE)',
  `data_email` datetime DEFAULT NULL COMMENT 'Data do Email(VEM DO SISTEMA AENET_NFE)',
  `data_danfe` datetime DEFAULT NULL COMMENT 'Data da Danfe(VEM DO SISTEMA AENET_NFE)',
  `cod_ope_d` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Destinatário Correio (VEM DO SISTEMA AENET )',
  `cod_ope_r` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Código Operador Remetente  Correio  (VEM DO SISTEMA AENET )',
  `cnpj_emi` int(11) NOT NULL COMMENT 'CNPJ Emitente  (VEM DO SISTEMA AENET )',
  `nro_evento` int(11) NOT NULL COMMENT 'Numero do evento  (VEM DO SISTEMA AENET )',
  `tempo_consulta` int(11) NOT NULL COMMENT 'Tempo da consulta ? (SEM USO)',
  `txt_edi` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'SEM USO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nfes_aenet`
--

LOCK TABLES `nfes_aenet` WRITE;
/*!40000 ALTER TABLE `nfes_aenet` DISABLE KEYS */;
/*!40000 ALTER TABLE `nfes_aenet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nsus`
--

DROP TABLE IF EXISTS `nsus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsus` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `xx` int(11) NOT NULL,
  `xxx` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsus`
--

LOCK TABLES `nsus` WRITE;
/*!40000 ALTER TABLE `nsus` DISABLE KEYS */;
/*!40000 ALTER TABLE `nsus` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-03  7:56:08
