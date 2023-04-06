-- MySQL dump 10.13  Distrib 8.0.23, for Win64 (x86_64)
--
-- Host: 192.168.137.227    Database: makrab
-- ------------------------------------------------------
-- Server version	8.0.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acl_auth_token`
--

DROP TABLE IF EXISTS `acl_auth_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_auth_token` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `acl_user_id` int unsigned NOT NULL,
  `token` varchar(32) NOT NULL,
  `hwid` varchar(128) NOT NULL,
  `pcode` varchar(128) NOT NULL,
  `version` varchar(32) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `issued` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `expire` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`,`acl_user_id`),
  KEY `auth_token_FKIndex1` (`acl_user_id`),
  CONSTRAINT `acl_auth_token_ibfk_1` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2781 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_rule`
--

DROP TABLE IF EXISTS `acl_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_rule` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `handle` varchar(1024) NOT NULL,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user`
--

DROP TABLE IF EXISTS `acl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `fullname` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `state` tinyint unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `deleted` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user_group`
--

DROP TABLE IF EXISTS `acl_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user_group_has_rules`
--

DROP TABLE IF EXISTS `acl_user_group_has_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user_group_has_rules` (
  `acl_user_group_id` int unsigned NOT NULL,
  `acl_rule_id` int unsigned NOT NULL,
  PRIMARY KEY (`acl_user_group_id`,`acl_rule_id`),
  KEY `user_group_has_rule_FKIndex1` (`acl_user_group_id`),
  KEY `user_group_has_rule_FKIndex2` (`acl_rule_id`),
  CONSTRAINT `acl_user_group_has_rules_ibfk_1` FOREIGN KEY (`acl_user_group_id`) REFERENCES `acl_user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acl_user_group_has_rules_ibfk_2` FOREIGN KEY (`acl_rule_id`) REFERENCES `acl_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user_group_has_users`
--

DROP TABLE IF EXISTS `acl_user_group_has_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user_group_has_users` (
  `acl_user_group_id` int unsigned NOT NULL,
  `acl_user_id` int unsigned NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`acl_user_group_id`,`acl_user_id`),
  KEY `user_group_has_user_FKIndex1` (`acl_user_group_id`),
  KEY `user_group_has_user_FKIndex2` (`acl_user_id`),
  CONSTRAINT `acl_user_group_has_users_ibfk_1` FOREIGN KEY (`acl_user_group_id`) REFERENCES `acl_user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acl_user_group_has_users_ibfk_2` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user_has_rules`
--

DROP TABLE IF EXISTS `acl_user_has_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user_has_rules` (
  `acl_user_id` int unsigned NOT NULL,
  `acl_rule_id` int unsigned NOT NULL,
  PRIMARY KEY (`acl_user_id`,`acl_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_user_has_vehicle`
--

DROP TABLE IF EXISTS `acl_user_has_vehicle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acl_user_has_vehicle` (
  `acl_user_id` int unsigned NOT NULL,
  `vehicle_id` int unsigned NOT NULL,
  PRIMARY KEY (`acl_user_id`,`vehicle_id`),
  KEY `fkUHVVid_idx` (`vehicle_id`),
  CONSTRAINT `fkUHVUid` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`),
  CONSTRAINT `fkUHVVid` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(255) DEFAULT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,
  `long` float(10,6) DEFAULT NULL,
  `radius` float(10,6) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alarms`
--

DROP TABLE IF EXISTS `alarms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alarms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `color` varchar(45) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfg_group_setting`
--

DROP TABLE IF EXISTS `cfg_group_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cfg_group_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `acl_user_group_id` int unsigned NOT NULL,
  `handle` varchar(255) NOT NULL,
  `val` varchar(1024) NOT NULL DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cfg_group_setting_idx_handle` (`acl_user_group_id`,`handle`),
  KEY `cfg_group_setting_updated` (`updated`),
  KEY `cfg_group_setting_FKIndex1` (`acl_user_group_id`),
  CONSTRAINT `cfg_group_setting_ibfk_1` FOREIGN KEY (`acl_user_group_id`) REFERENCES `acl_user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfg_setting`
--

DROP TABLE IF EXISTS `cfg_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cfg_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `handle` varchar(255) NOT NULL,
  `val` varchar(1024) NOT NULL DEFAULT '',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cfg_setting_idx_handle` (`handle`),
  KEY `cfg_setting_idx_updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfg_user_setting`
--

DROP TABLE IF EXISTS `cfg_user_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cfg_user_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `acl_user_id` int unsigned NOT NULL,
  `handle` varchar(255) NOT NULL,
  `val` varchar(1024) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cfg_user_setting_idx_handle` (`acl_user_id`,`handle`),
  KEY `cfg_user_setting_idx_updated` (`updated`),
  KEY `cfg_user_setting_FKIndex1` (`acl_user_id`),
  CONSTRAINT `cfg_user_setting_ibfk_1` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_message`
--

DROP TABLE IF EXISTS `chat_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `from` int unsigned NOT NULL,
  `to` int unsigned NOT NULL,
  `sent` datetime DEFAULT NULL,
  `content` varchar(1024) DEFAULT NULL,
  `type` int DEFAULT NULL,
  `delivered` datetime DEFAULT NULL,
  `readed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkCMFUid_idx` (`from`),
  KEY `fkCMTUid_idx` (`to`),
  CONSTRAINT `fkCMFUid` FOREIGN KEY (`from`) REFERENCES `acl_user` (`id`),
  CONSTRAINT `fkCMTUid` FOREIGN KEY (`to`) REFERENCES `acl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `recipient_id` int unsigned NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`sender_id`),
  KEY `qw_idx` (`recipient_id`),
  CONSTRAINT `recipient_ID_FK` FOREIGN KEY (`recipient_id`) REFERENCES `acl_user` (`id`),
  CONSTRAINT `sender_id_FK` FOREIGN KEY (`sender_id`) REFERENCES `acl_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractors`
--

DROP TABLE IF EXISTS `contractors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contractors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(255) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `code` varchar(128) DEFAULT NULL,
  `inn` varchar(45) DEFAULT NULL,
  `comment` text,
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id_UNIQUE` (`ext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5664 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractors_has_addresses`
--

DROP TABLE IF EXISTS `contractors_has_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contractors_has_addresses` (
  `contractors_id` int unsigned NOT NULL,
  `addresses_id` int unsigned NOT NULL,
  PRIMARY KEY (`contractors_id`,`addresses_id`),
  KEY `fkCAAid_idx` (`addresses_id`),
  CONSTRAINT `fkCAAid` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fkCACid` FOREIGN KEY (`contractors_id`) REFERENCES `contractors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contractors_has_geoobjects`
--

DROP TABLE IF EXISTS `contractors_has_geoobjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contractors_has_geoobjects` (
  `contractors_id` int unsigned NOT NULL,
  `geoobjects_id` int unsigned NOT NULL,
  PRIMARY KEY (`contractors_id`,`geoobjects_id`),
  KEY `fkCGGid_idx` (`geoobjects_id`),
  CONSTRAINT `fkCGCid` FOREIGN KEY (`contractors_id`) REFERENCES `contractors` (`id`),
  CONSTRAINT `fkCGGid` FOREIGN KEY (`geoobjects_id`) REFERENCES `geoobjects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `imei` varchar(255) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices_has_user`
--

DROP TABLE IF EXISTS `devices_has_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices_has_user` (
  `acl_user_id` int unsigned NOT NULL,
  `devices_id` int unsigned NOT NULL,
  PRIMARY KEY (`acl_user_id`,`devices_id`),
  KEY `fkDDid_idx` (`devices_id`),
  CONSTRAINT `fkDDid` FOREIGN KEY (`devices_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `fkDUid` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices_has_vehicles`
--

DROP TABLE IF EXISTS `devices_has_vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices_has_vehicles` (
  `devices_id` int unsigned NOT NULL,
  `vehicles_id` int unsigned NOT NULL,
  PRIMARY KEY (`devices_id`,`vehicles_id`),
  KEY `fkDVid_idx` (`vehicles_id`),
  CONSTRAINT `fdDDid` FOREIGN KEY (`devices_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `fkDVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geoobjects`
--

DROP TABLE IF EXISTS `geoobjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `geoobjects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,
  `long` float(10,6) DEFAULT NULL,
  `radius` float(10,6) DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id_UNIQUE` (`ext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gps_location`
--

DROP TABLE IF EXISTS `gps_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gps_location` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicles_id` int unsigned DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `lattitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `speed` int NOT NULL DEFAULT '0',
  `distance` int NOT NULL DEFAULT '0',
  `direction` int NOT NULL DEFAULT '0',
  `tasks_id` int unsigned DEFAULT NULL,
  `altitude` int NOT NULL DEFAULT '0',
  `accuracy` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fkGVid_idx` (`vehicles_id`),
  CONSTRAINT `fkGVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107531 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal`
--

DROP TABLE IF EXISTS `journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `acl_user_id` int unsigned DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `action` int DEFAULT NULL,
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_indx` (`action`),
  KEY `fkJUid_idx` (`acl_user_id`),
  CONSTRAINT `fkJUid` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_requests`
--

DROP TABLE IF EXISTS `log_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `url` varchar(1024) DEFAULT NULL,
  `request` json DEFAULT NULL,
  `response` text,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`) /*!80000 INVISIBLE */,
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration_log`
--

DROP TABLE IF EXISTS `migration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `migration_datetime` varchar(255) NOT NULL,
  `classname` varchar(255) NOT NULL,
  `executed_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mq`
--

DROP TABLE IF EXISTS `mq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mq` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tm` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `subsys` varchar(45) DEFAULT NULL,
  `state` enum('queued','processing','done','failed') DEFAULT NULL,
  `request` json DEFAULT NULL,
  `tm_started` datetime DEFAULT NULL,
  `worker` varchar(45) DEFAULT NULL,
  `result` json DEFAULT NULL,
  `tm_done` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mq_locks`
--

DROP TABLE IF EXISTS `mq_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mq_locks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mq_id` int DEFAULT NULL,
  `worker` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(1024) DEFAULT NULL,
  `acl_user_id` int unsigned DEFAULT NULL,
  `vehicles_id` int unsigned DEFAULT NULL,
  `orders_id` int unsigned DEFAULT NULL,
  `alarms_id` int unsigned DEFAULT NULL,
  `location_id` int unsigned DEFAULT NULL,
  `uploaded` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkPUid_idx` (`acl_user_id`),
  KEY `fkPVid_idx` (`vehicles_id`),
  KEY `fkPTId_idx` (`orders_id`),
  KEY `fkPLid_idx` (`location_id`),
  CONSTRAINT `fkPLid` FOREIGN KEY (`location_id`) REFERENCES `gps_location` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fkPOid` FOREIGN KEY (`orders_id`) REFERENCES `tasks_orders` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fkPUid` FOREIGN KEY (`acl_user_id`) REFERENCES `acl_user` (`id`),
  CONSTRAINT `fkPVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicles_id` int unsigned DEFAULT NULL,
  `ext_id` varchar(255) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `status` enum('draft','queued','process','done') DEFAULT NULL,
  `loaded_weight` float(10,4) DEFAULT '0.0000',
  `empty_weight` float(10,4) DEFAULT '0.0000',
  `comment` text,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id_UNIQUE` (`ext_id`),
  KEY `fkTVid_idx` (`vehicles_id`),
  CONSTRAINT `fkTVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks_addresses`
--

DROP TABLE IF EXISTS `tasks_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tasks_id` int unsigned NOT NULL,
  `address_id` int unsigned DEFAULT NULL,
  `order` int DEFAULT NULL,
  `type` enum('start','middle','finish','return') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkTaAid_idx` (`address_id`),
  KEY `fkTaTid` (`tasks_id`),
  CONSTRAINT `fkTaAid` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
  CONSTRAINT `fkTaTid` FOREIGN KEY (`tasks_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks_geoobjects`
--

DROP TABLE IF EXISTS `tasks_geoobjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks_geoobjects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tasks_id` int unsigned NOT NULL,
  `geoobjects_id` int unsigned NOT NULL,
  `order` int DEFAULT NULL,
  `trip_type` enum('start','middle','finish','return') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `TGTid_idx` (`tasks_id`),
  KEY `TGGid_idx` (`geoobjects_id`),
  CONSTRAINT `TGGid` FOREIGN KEY (`geoobjects_id`) REFERENCES `geoobjects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `TGTid` FOREIGN KEY (`tasks_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=1124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks_orders`
--

DROP TABLE IF EXISTS `tasks_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_addresses_id` int unsigned NOT NULL,
  `ext_id` varchar(255) DEFAULT NULL,
  `action` enum('deliver','change','grab','delivergrab','grabdeliver','transportation') DEFAULT NULL,
  `volume` float(10,4) DEFAULT NULL,
  `weight` float(10,4) DEFAULT NULL,
  `gross_weight` float(10,4) DEFAULT NULL,
  `package_weight` float(10,4) DEFAULT NULL,
  `status` enum('draft','queued','process','done','failed') DEFAULT NULL,
  `failed_reason` varchar(255) DEFAULT NULL,
  `plan_arrival` datetime DEFAULT NULL,
  `plan_departure` datetime DEFAULT NULL,
  `fact_arrival` datetime DEFAULT NULL,
  `fact_departure` datetime DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id_UNIQUE` (`ext_id`),
  KEY `fkToTid_idx` (`task_addresses_id`)
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ext_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `description` text,
  `color` varchar(45) DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id_UNIQUE` (`ext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehicles_alarms`
--

DROP TABLE IF EXISTS `vehicles_alarms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles_alarms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicles_id` int unsigned NOT NULL,
  `created` datetime NOT NULL,
  `active` tinyint NOT NULL,
  `alarm_id` int unsigned NOT NULL DEFAULT '0',
  `alarm_text` varchar(256) NOT NULL DEFAULT '',
  `decision_time` int unsigned NOT NULL DEFAULT '0',
  `reset_time` datetime DEFAULT NULL,
  `tasks_id` int unsigned DEFAULT NULL,
  `push_sent` datetime DEFAULT NULL,
  `location_id` int unsigned DEFAULT NULL,
  `photos_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fkVaAid_idx` (`alarm_id`),
  KEY `fkVaVid_idx` (`vehicles_id`),
  CONSTRAINT `fkVaAid` FOREIGN KEY (`alarm_id`) REFERENCES `alarms` (`id`),
  CONSTRAINT `fkVaVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehicles_has_tasks`
--

DROP TABLE IF EXISTS `vehicles_has_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles_has_tasks` (
  `vehicles_id` int unsigned NOT NULL,
  `tasks_id` int unsigned NOT NULL,
  PRIMARY KEY (`vehicles_id`,`tasks_id`),
  KEY `fkTsk_idx` (`tasks_id`),
  CONSTRAINT `fkTsk` FOREIGN KEY (`tasks_id`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fkVid` FOREIGN KEY (`vehicles_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-09 16:40:42
