-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: web_sem
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `date` date NOT NULL DEFAULT curdate(),
  `abstract` text NOT NULL,
  `file` text NOT NULL,
  `author` int(11) NOT NULL,
  `status` enum('waiting','accepted','denied') NOT NULL DEFAULT 'waiting',
  PRIMARY KEY (`id`),
  KEY `con_author` (`author`),
  CONSTRAINT `con_author` FOREIGN KEY (`author`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (1,'<script>alert(\"i am too in this episode\")</script>','2025-10-13','<p>Test</p>','test.pdf',3,'waiting'),(2,'Test article 2','2025-10-19','<p>Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> Test <strong>testing</strong> <em>Test </em> </p>','test.pdf',3,'accepted');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article` int(11) NOT NULL,
  `editor` int(11) NOT NULL,
  `quality` float DEFAULT -1,
  `language` float DEFAULT -1,
  `relevancy` float DEFAULT -1,
  PRIMARY KEY (`id`),
  KEY `con_article` (`article`),
  KEY `con_editor` (`editor`),
  CONSTRAINT `con_article` FOREIGN KEY (`article`) REFERENCES `articles` (`id`),
  CONSTRAINT `con_editor` FOREIGN KEY (`editor`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (97,2,172,5,2.5,3),(109,2,173,4,4,4),(110,2,174,5,5,5),(113,1,172,2,2,2);
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'a','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(6,'admin','$2y$10$xrhQCIlNHd3fv.Fm5E/atu6PidGeHuAs3Smgl37UNLawrpvlEp2ry','admin',3,1),(115,'713765','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(116,'46912','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(117,'93265','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(118,'325586','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(119,'348135','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(120,'763918','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(121,'775187','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(122,'584179','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(123,'595337','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(124,'224145','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(125,'334714','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(129,'16661','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(130,'70550','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(131,'302770','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(132,'302197','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(133,'602675','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(135,'725903','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(136,'309161','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(137,'368095','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(138,'912990','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(139,'460669','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(140,'564374','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(141,'439863','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(142,'506193','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(143,'211375','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(144,'538294','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(145,'57346','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(146,'671848','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(147,'187201','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(148,'920462','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(149,'40709','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(150,'442157','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(151,'88658','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(152,'116817','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(153,'318113','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(154,'240114','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(155,'246231','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(156,'510811','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(157,'815362','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(158,'544378','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(159,'275806','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(160,'745891','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(161,'902042','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(162,'272537','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(163,'656556','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(164,'465173','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(165,'356194','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(166,'385450','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(167,'858667','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(168,'136989','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(169,'108940','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(170,'133731','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(171,'341838','$2y$10$dyMhg1A7PCnUqPAMAjkzRu/HOzQju/T9e8X2fDxmXvzcpzWQ6lzQK','<script>alert(\'hi\')</script>',1,1),(172,'editor1','$2y$10$1x4WruIl79zXCtB4HsHq/uK9zyjkUNSFezjZD2PiitwQK6fkwHIM6','Editor 1',2,1),(173,'editor2','$2y$10$jNIjAj5VyptWlOm3Axd0KeE.iMts.3NbAgoLHb00rL6Kd.CCROq66','Editor 2',2,1),(174,'editor3','$2y$10$G9oslpeKYIF9ZFsqu5idc.IvwGwdiZ90sz6qKGuQZjB0YJXPfpNHi','Editor 3',2,1),(175,'super','$2y$10$NDTY7UgQrgoX6PNaL/gagODPxKmqPl50B4irfRSpWNtGnpx6TXY7a','SUPERADMIN',4,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-01  1:12:34
