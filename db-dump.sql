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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (31,'Kdo je friend?','2025-12-14','<p>Pokud&nbsp;jste&nbsp;ve&nbsp;fandomu&nbsp;<strong>DELTARUNE</strong>&nbsp;dostatečně&nbsp;dlouho,&nbsp;pravděpodobně&nbsp;víte&nbsp;o&nbsp;existenci&nbsp;záhadné&nbsp;postavy&nbsp;s&nbsp;očima&nbsp;barvy&nbsp;Spamtonu.&nbsp;V&nbsp;souborech&nbsp;je&nbsp;známá&nbsp;jako&nbsp;<em>„IMAGE_FRIEND“</em>&nbsp;a&nbsp;v&nbsp;kódu&nbsp;jako&nbsp;<em>„DEVICE_FRIEND“</em>.&nbsp;Kdo&nbsp;to&nbsp;je?&nbsp;Jak&nbsp;často&nbsp;se&nbsp;objevuje&nbsp;ve&nbsp;hře?&nbsp;Je&nbsp;to&nbsp;<strong>kočka</strong>?&nbsp;Proč&nbsp;je&nbsp;to&nbsp;kočka?&nbsp;Tyto&nbsp;otázky&nbsp;stále&nbsp;zůstávají&nbsp;nezodpovězeny,&nbsp;protože&nbsp;se&nbsp;zdá,&nbsp;že&nbsp;<strong>FRIEND</strong>&nbsp;a&nbsp;příběh&nbsp;s&nbsp;ním&nbsp;spojený&nbsp;budou&nbsp;středem&nbsp;pozornosti&nbsp;<strong>5.&nbsp;kapitoly</strong>.&nbsp;Nicméně&nbsp;po&nbsp;vydání&nbsp;nových&nbsp;kapitol&nbsp;máme&nbsp;s&nbsp;čím&nbsp;pracovat,&nbsp;abychom&nbsp;se&nbsp;pokusili&nbsp;získat&nbsp;alespoň&nbsp;malou&nbsp;představu&nbsp;o&nbsp;tom,&nbsp;co&nbsp;se&nbsp;může&nbsp;dít.</p>','98d713340805d845bce4b837f90a438e.pdf',189,'accepted'),(32,'Motivy v hudbě','2025-12-14','<h2>Tohle&nbsp;jsem&nbsp;našel&nbsp;na&nbsp;internetu&nbsp;snad&nbsp;to&nbsp;je&nbsp;ok</h2>','fa49fd3c079e5903dd67559ea9e48d2e.pdf',190,'waiting');
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
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (118,31,186,4,5,5),(119,31,187,5,5,5),(120,32,186,1.5,2,1),(121,32,187,1,1,1),(123,31,188,5,4.5,5);
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
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (183,'super','$2y$10$VDsj5YtOhhmqFD/XxXjY5OKtn5jUJ0vnN1fjEk6Jnnhlx.DoaxWia','SUPERADMIN',4,1),(184,'jnovak','$2y$10$olWSr4qqoIUODE/9xR7LhOkTd34PSWpGgvm.B5To0d0gwOn7nlX2G','Jan Novák',3,1),(185,'pepadepo','$2y$10$1zIxB1B.Y1MUhg34hLxuhe2TCviUA9u5WRQ2vQfrwnGUPiLrX4IMG','Josef Kratochvíl',3,1),(186,'janazitna','$2y$10$ZZ9Mh7wb9/srHxQ.It4zNu3qKoC4sK.S2wpZX6.NSvW1NpG4MSiBG','Jana Žitná',2,1),(187,'katamama','$2y$10$6TwNoLGNt9TB510S2nDk3uiblF4QaMlS9edsk6dqXP.GAwvY0cdQ6','Kateřina Šťastná',2,1),(188,'malazuza','$2y$10$mIanQmCLy4gUTfF5qJt7Vu4OwPuMEBNw8fgM7L45ihoDogd8OmxYW','Zuzana Malá',2,1),(189,'igork','$2y$10$WEvzG31c8kD5Ah/ZY.7iBOzZ2wuF.TQFod1kSWBnhYeadNognCGji','Igor Koště',1,1),(190,'alexik','$2y$10$8YfbVuY.B3yaam8IsbF1WOobRs.l7cHZNkigTe3eYUCxsIwuZ/TF.','Alex Hora',1,1);
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

-- Dump completed on 2025-12-14 15:59:46
