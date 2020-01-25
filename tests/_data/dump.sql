

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `Role`;

CREATE TABLE `Role` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL,
  `modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_2b3845356f5921ca3a9aff3ef13b9658c6e40f96` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;

INSERT INTO `Role` (`id`, `label`, `created`, `modified`)
VALUES
	(1,'Admin',1541926960,1551814121),
	(2,'Client',1541926960,1551814121),
	(3,'Test',1541926960,1551814121),
	(4,'Student',1541926960,1551814121);

/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;


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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;

INSERT INTO `User` (`id`, `name`, `email`, `published`, `publish_from`, `publish_to`, `created`, `modified`)
VALUES
	(1,'John Doe','john.doe@headcrumbs.io',null,null,null,1541926961,1551814073),
	(2,'Jane Doe','jane.doe@headcrumbs.io',null,null,null,1541926963,1551814083);

/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `Role_User` WRITE;
/*!40000 ALTER TABLE `Role_User` DISABLE KEYS */;

INSERT INTO `Role_User` (`id`, `role_id`, `user_id`)
VALUES
	(1,1,1);

/*!40000 ALTER TABLE `Role_User` ENABLE KEYS */;
UNLOCK TABLES;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
