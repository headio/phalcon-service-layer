# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.34-log)
# Database: servicelayer_test
# Generation Time: 2021-12-06 14:31:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table Role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Role`;

CREATE TABLE `Role` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL,
  `modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_2b3845356f5921ca3a9aff3ef13b9658c6e40f96` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;

INSERT INTO `Role` (`id`, `label`, `created`, `modified`)
VALUES
	(1,'Admin',1541926960,1551814121),
	(2,'Client',1541926960,1551814121),
	(3,'Test',1541926960,1551814121),
	(4,'Student',1541926960,1551814121),
	(5,'Guest',1541926960,1541926960),
	(6,'Superuser',1541926960,1541926960),
	(7,'Marketing',1541926960,1541926960),
	(8,'Distribution',1541926960,1541926960);

/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Role_User
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Role_User`;

CREATE TABLE `Role_User` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` smallint(5) unsigned NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_141265324c666a8912ec2394dfd2099c733e5272` (`role_id`,`user_id`),
  KEY `47f1fdc221e0e07717d4061130bb71c1380339c0` (`role_id`),
  KEY `6a749103f5c001f8ac079deee869a53eccd2ed67` (`user_id`),
  CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `Role_User` (`id`, `role_id`, `user_id`)
VALUES
	(1,1,1),

UNLOCK TABLES;


# Dump of table Tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Tag`;

CREATE TABLE `Tag` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(48) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_pZvVDxrPbkL4ESLY` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `Tag` WRITE;
/*!40000 ALTER TABLE `Tag` DISABLE KEYS */;

INSERT INTO `Tag` (`id`, `label`)
VALUES
	(25,'Elixir'),
	(20,'Haskell'),
	(30,'Java'),
	(24,'JavaScript'),
	(26,'Perl'),
	(28,'PHP'),
	(32,'Python'),
	(31,'Ruby'),
	(27,'TypeScript');

/*!40000 ALTER TABLE `Tag` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table User
# ------------------------------------------------------------

DROP TABLE IF EXISTS `User`;

CREATE TABLE `User` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(84) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned DEFAULT NULL,
  `publish_from` int(10) unsigned DEFAULT NULL,
  `publish_to` int(10) unsigned DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_abf65b30ccf681c4bf021d0bc1aa6962f103b8da` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;

INSERT INTO `User` (`id`, `name`, `email`, `published`, `publish_from`, `publish_to`, `created`, `modified`)
VALUES
	(1,'John Doe','john.doe@headcrumbs.io',NULL,NULL,NULL,1541926961,1551814073),
	(2,'Jane Doe','jane.doe@headcrumbs.io',NULL,NULL,NULL,1541926963,1551814083),
	(3,'Bob Doe','bob.doe@headcrumbs.io',NULL,NULL,NULL,1541926963,1551814083),
	(4,'Rob Doe','rob.doe@headcrumbs.io',NULL,NULL,NULL,1541926963,1551814083),
	(5,'Silvia Doe','silvia.doe@headcrumbs.io',NULL,NULL,NULL,1541926963,1551814083);

/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
