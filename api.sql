-- MySQL dump 10.13  Distrib 8.0.29, for Linux (x86_64)
--
-- Host: localhost    Database: api2
-- ------------------------------------------------------
-- Server version	8.0.29-0ubuntu0.21.10.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` int unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` int unsigned DEFAULT NULL,
  `request_amount` int DEFAULT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `cart_owner_id_index` (`owner_id`),
  KEY `cart_model_id_index` (`model_id`),
  CONSTRAINT `cart_model_id_foreign` FOREIGN KEY (`model_id`) REFERENCES `wifi_router_model` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES ('cart_23_franchise_fee',23,'franchise_fee',NULL,1000,0,'2022-05-26 20:19:02','2022-05-26 20:19:02');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '0',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_template_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `email_template_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template`
--

LOCK TABLES `email_template` WRITE;
/*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internet_plans`
--

DROP TABLE IF EXISTS `internet_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `internet_plans` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int NOT NULL,
  `validity` int NOT NULL,
  `bandwidth` int NOT NULL,
  `data_limit` int NOT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `internet_plans_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `internet_plans_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internet_plans`
--

LOCK TABLES `internet_plans` WRITE;
/*!40000 ALTER TABLE `internet_plans` DISABLE KEYS */;
INSERT INTO `internet_plans` VALUES (1,'Demo','Demo',200,720,20480,4096,'pulsewifi_2022-05-24_09-24-28','2022-05-26 19:13:21','2022-05-30 02:43:52'),(2,'Other Plan','Other Plan',500,30,1024,100,'pulsewifi_2022-05-24_09-24-28','2022-05-26 19:13:38','2022-05-26 19:13:38');
/*!40000 ALTER TABLE `internet_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `location` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` int unsigned NOT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` int NOT NULL,
  `latitude` double(255,2) NOT NULL DEFAULT '0.00',
  `longitude` double(255,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `location_owner_id_index` (`owner_id`),
  KEY `location_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `location_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `location_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'testlocation1',3,'pulse_1_2022-05-24_08-41-34','Kamalpur','Mongalkote','West Bengal','India',713147,23.52,87.89,NULL,'2022-05-29 17:58:41'),(2,'testlocation2',3,'pulse_1_2022-05-24_08-41-34','Kamalpur','Mongalkote','West Bengal','India',713147,23.52,87.89,NULL,'2022-05-29 17:58:45'),(3,'Home',20,'pulsewifi_2022-05-24_09-24-28','26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'2022-05-26 01:49:49','2022-05-26 01:49:49'),(4,'Home',20,'pulsewifi_2022-05-24_09-24-28','26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'2022-05-26 01:49:55','2022-05-26 01:49:55'),(5,'Home',20,'pulsewifi_2022-05-24_09-24-28','26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'2022-05-26 01:50:09','2022-05-26 01:55:23'),(6,'Home',20,'pulsewifi_2022-05-24_09-24-28','26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'2022-05-26 01:53:02','2022-05-29 17:27:09'),(7,'Chandigarh',3,'pulse_1_2022-05-24_08-41-34','Chandigarh','Chandigarh','Chhattisgarh','India',123456,30.73,76.78,'2022-05-30 14:05:40','2022-05-30 14:08:15'),(9,'Chandigarh',24,'pulse_1_2022-05-24_08-41-34','Chandigarh','Chandigarh','Assam','India',700000,30.73,76.78,'2022-05-30 14:17:16','2022-05-30 14:54:05'),(10,'Chandigarh Test',24,'pulse_1_2022-05-24_08-41-34','Chandigarh','Chandigarh','Kerala','India',701000,30.73,76.78,'2022-05-30 14:57:45','2022-05-30 16:58:29');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_server`
--

DROP TABLE IF EXISTS `mail_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_server` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mail_server_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `mail_server_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_server`
--

LOCK TABLES `mail_server` WRITE;
/*!40000 ALTER TABLE `mail_server` DISABLE KEYS */;
INSERT INTO `mail_server` VALUES (1,'MainServer','SuperAdmin','pulsesuper@gmail.com','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',1,'pulse_1_2022-05-24_08-41-34',NULL,NULL),(2,'SendGrid','Santhosh Kumar','thinkofsan@gmail.com','tempKey',0,'pulsewifi_2022-05-24_08-42-25','2022-05-24 08:42:25','2022-05-24 08:42:25'),(3,'SendGrid','Home Kumar','bliss.glee.916@gmail.com','tempKey',0,'pulsewifi_2022-05-24_08-49-11','2022-05-24 08:49:12','2022-05-24 08:49:12'),(4,'SendGrid','Test PDOA','kumar@cnctdwifi.com','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',0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 09:24:29','2022-05-24 09:24:29'),(5,'SendGrid','BIplob Mandal','simplifon@gmail.com','tempKey',0,'pulsewifi_2022-05-24_19-14-32','2022-05-24 19:14:32','2022-05-24 19:14:32');
/*!40000 ALTER TABLE `mail_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2022_03_10_052601_create_roles_table',1),(3,'2022_03_10_054431_create_pdoa_plans_table',1),(4,'2022_03_11_054433_create_pdoas_table',1),(5,'2022_03_11_072907_create_wifi_router_model_table',1),(6,'2022_03_11_182526_create_users_table',1),(7,'2022_03_11_232529_create_locations_table',1),(8,'2022_03_11_235342_create_wifi_routers_table',1),(9,'2022_03_12_000531_create_internet_plans_table',1),(10,'2022_03_17_150139_create_wi_fi_users_table',1),(11,'2022_03_17_150549_create_payment_orders_table',1),(12,'2022_03_18_191356_create_payment_setting_table',1),(13,'2022_03_18_191548_create_payments_table',1),(14,'2022_03_18_191613_create_payout_setting_table',1),(15,'2022_03_18_191625_create_payouts_table',1),(16,'2022_03_22_151331_create_sms_gateway_table',1),(17,'2022_03_22_161332_create_sms_template_table',1),(18,'2022_03_22_162332_create_mail_server_table',1),(19,'2022_03_22_163229_create_email_template_table',1),(20,'2022_03_22_163903_create_network_setting_table',1),(21,'2022_04_09_211929_create_order_table',1),(22,'2022_04_11_062533_create_carts_table',1),(23,'2022_04_22_084723_create_wi_fi_user_verifies_table',1),(24,'2022_04_23_005459_create_wi_fi_devices_table',1),(25,'2022_04_23_045527_create_nas_lists_table',1),(26,'2022_04_26_003412_create_wi_fi_orders_table',1),(27,'2022_05_15_122614_add_secret_to_wifi_router_',1),(28,'2022_05_23_133717_create_payment_references_table',1),(29,'2022_05_24_142240_add_key_columns_to_wifi_routers_table',2),(30,'2022_05_25_185153_add_favicon_to_pdoas_table',3),(32,'2022_05_25_185817_rename_stuff_to_staff_role_table',4),(33,'2022_05_29_154855_change_name_wi_fi_user',5),(34,'2022_05_29_155015_change_email_wi_fi_user',6),(35,'2022_05_29_155206_change_pdoa_wi_fi_user',7),(36,'2022_05_31_132753_add_location_id_to_wi_fi_orders',8),(37,'2022_05_31_073945_remove_payout_setting_table',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nas_lists`
--

DROP TABLE IF EXISTS `nas_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nas_lists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pdoa` int NOT NULL,
  `enabled` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nas_lists`
--

LOCK TABLES `nas_lists` WRITE;
/*!40000 ALTER TABLE `nas_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `nas_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `network_setting`
--

DROP TABLE IF EXISTS `network_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `network_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guestEssid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `freeWiFi` int NOT NULL DEFAULT '0',
  `freeBandwidth` int NOT NULL,
  `freeDailySession` int NOT NULL,
  `freeDataLimit` int NOT NULL,
  `serverWhitelist` text COLLATE utf8mb4_unicode_ci,
  `domainWhitelist` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `network_setting_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `network_setting_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `network_setting`
--

LOCK TABLES `network_setting` WRITE;
/*!40000 ALTER TABLE `network_setting` DISABLE KEYS */;
INSERT INTO `network_setting` VALUES (1,'pulse_1_2022-05-24_08-41-34','Pulse WiFi',1,150,60,500,'google.com,login.cnctdwifi.com,www.yahoo.com','.google.com,.cnctdwifi.com,.yahoo.com',NULL,NULL),(2,'pulsewifi_2022-05-24_08-42-25','Home',1,150,60,500,'google.com,login.cnctdwifi.com,www.yahoo.com','.google.com,.cnctdwifi.com,.yahoo.com','2022-05-24 08:42:25','2022-05-24 08:42:25'),(3,'pulsewifi_2022-05-24_08-49-11','Home',1,150,60,500,'google.com,login.cnctdwifi.com,www.yahoo.com','.google.com,.cnctdwifi.com,.yahoo.com','2022-05-24 08:49:12','2022-05-24 08:49:12'),(4,'pulsewifi_2022-05-24_09-24-28','Pulse WiFi - Test',1,5120,60,500,'console.pulsewifi.com','.pulsewifi.net','2022-05-24 09:24:29','2022-05-26 01:04:25'),(5,'pulsewifi_2022-05-24_19-14-32','PocketWiFi',1,150,60,500,'google.com,login.cnctdwifi.com,www.yahoo.com','.google.com,.cnctdwifi.com,.yahoo.com','2022-05-24 19:14:32','2022-05-24 19:14:32');
/*!40000 ALTER TABLE `network_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` int unsigned DEFAULT NULL,
  `model_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fee_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fee_amount` bigint DEFAULT NULL,
  `total_amount` bigint DEFAULT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_processed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_owner_id_index` (`owner_id`),
  KEY `orders_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `orders_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ('10_2022.05.24_11.32.24',10,'1','distributor_fee',1000,1,'1','0','1',0,'pulse_1_2022-05-24_08-41-34','2022-05-24 11:32:24','2022-05-24 11:32:24'),('11_2022.05.24_11.34.04',11,'1','franchise_fee',1000,2,'2','1','1',2,'pulse_1_2022-05-24_08-41-34','2022-05-24 11:34:04','2022-05-24 15:57:57'),('12_2022.05.24_11.42.37',12,'1','distributor_fee',1000,1,'1','0','1',0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 11:42:37','2022-05-24 11:42:37'),('13_2022.05.24_11.48.23',13,'1','franchise_fee',1000,1,'1','0','1',0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 11:48:23','2022-05-24 11:48:23'),('14_2022.05.24_01.46.29',14,'1','franchise_fee',1000,1,'1','1','0',3,'pulsewifi_2022-05-24_09-24-28','2022-05-24 13:46:29','2022-05-24 15:47:45'),('15_2022.05.24_03.38.24',15,'1','franchise_fee',1000,1,'1','1','0',3,'pulsewifi_2022-05-24_09-24-28','2022-05-24 15:38:24','2022-05-24 15:58:36'),('16_2022.05.24_07.14.50',16,'','pdoa_license_price',23576,0,'','','',3,'pulse_1_2022-05-24_08-41-34','2022-05-24 19:14:50','2022-05-24 19:18:28'),('20_2022.05.26_01.46.18',20,'4','franchise_fee',1000,1,'1','0,NaN','1,NaN',2,'pulsewifi_2022-05-24_09-24-28','2022-05-26 01:46:18','2022-05-26 01:47:58'),('20_2022.05.26_07.10.10',20,'4','',0,1,'1','0,NaN','1,NaN',2,'pulsewifi_2022-05-24_09-24-28','2022-05-26 19:10:10','2022-05-26 19:15:14'),('21_2022.05.26_10.07.02',21,'4','distributor_fee',1000,1,'1','0','1',1,'pulse_1_2022-05-24_08-41-34','2022-05-26 10:07:02','2022-05-26 10:08:30'),('24_2022.05.30_02.12.30',24,'4','franchise_fee',1000,1,'1','0,NaN','1,NaN',2,'pulse_1_2022-05-24_08-41-34','2022-05-30 14:12:30','2022-05-30 14:16:00'),('24_2022.05.30_02.54.48',24,'4','',0,1,'1','0,NaN','1,NaN',2,'pulse_1_2022-05-24_08-41-34','2022-05-30 14:54:48','2022-05-30 14:57:10'),('5_2022.05.24_08.48.09',5,'','distributor_fee',1000,0,'','','',0,'pulse_1_2022-05-24_08-41-34','2022-05-24 08:48:09','2022-05-24 08:48:09'),('6_2022.05.24_08.49.17',6,'','pdoa_license_price',3000,0,'','','',2,'pulse_1_2022-05-24_08-41-34','2022-05-24 08:49:17','2022-05-24 09:01:16'),('7_2022.05.24_09.04.30',7,'1','distributor_fee',1000,2,'2','2','0',3,'pulse_1_2022-05-24_08-41-34','2022-05-24 09:04:30','2022-05-24 15:58:08'),('8_2022.05.24_09.26.54',8,'','pdoa_license_price',3000,0,'','','',2,'pulse_1_2022-05-24_08-41-34','2022-05-24 09:26:54','2022-05-24 10:11:22'),('9_2022.05.24_10.38.18',9,'1','distributor_fee',1000,2,'2','0','2',0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 10:38:18','2022-05-24 10:38:18');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_orders`
--

DROP TABLE IF EXISTS `payment_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `internet_plan_id` int unsigned NOT NULL,
  `wifi_user_id` int unsigned NOT NULL,
  `franchise_id` int unsigned NOT NULL,
  `status` int NOT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_orders_internet_plan_id_index` (`internet_plan_id`),
  KEY `payment_orders_wifi_user_id_index` (`wifi_user_id`),
  KEY `payment_orders_franchise_id_index` (`franchise_id`),
  KEY `payment_orders_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `payment_orders_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_orders_internet_plan_id_foreign` FOREIGN KEY (`internet_plan_id`) REFERENCES `internet_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_orders_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_orders_wifi_user_id_foreign` FOREIGN KEY (`wifi_user_id`) REFERENCES `wi_fi_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_orders`
--

LOCK TABLES `payment_orders` WRITE;
/*!40000 ALTER TABLE `payment_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_references`
--

DROP TABLE IF EXISTS `payment_references`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_references` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay_payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_response` json DEFAULT NULL,
  `updated_via` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_references`
--

LOCK TABLES `payment_references` WRITE;
/*!40000 ALTER TABLE `payment_references` DISABLE KEYS */;
INSERT INTO `payment_references` VALUES (1,'6_2022.05.24_08.49.17','order_JZ0Xl2d2Iesu3w','pay_JZ0XsoialtPix1','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ0Xl2d2Iesu3w\", \"razorpay_signature\": \"ce140ef430b02cd67f039f7d38c249ec32017e737deb0cf09f1899d3a1fcd95e\", \"razorpay_payment_id\": \"pay_JZ0XsoialtPix1\"}}',NULL,'2022-05-24 09:01:05','2022-05-24 09:01:16'),(2,'7_2022.05.24_09.04.30','order_JZ0bxmGaiOfdTK','pay_JZ0c5O1Dh4da7m','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ0bxmGaiOfdTK\", \"razorpay_signature\": \"dac93d9ab29c995b94b19f135ec3a9d0055c614d93356a585e7ad2c8cd4bfc2e\", \"razorpay_payment_id\": \"pay_JZ0c5O1Dh4da7m\"}}',NULL,'2022-05-24 09:05:04','2022-05-24 09:05:15'),(3,'8_2022.05.24_09.26.54','order_JZ10JTtdteHKT3',NULL,NULL,NULL,NULL,'2022-05-24 09:28:07','2022-05-24 09:28:07'),(4,'8_2022.05.24_09.26.54','order_JZ11OP83gsKDEu',NULL,NULL,NULL,NULL,'2022-05-24 09:29:08','2022-05-24 09:29:08'),(5,'8_2022.05.24_09.26.54','order_JZ1J8GIdBz92Ro',NULL,NULL,NULL,NULL,'2022-05-24 09:45:56','2022-05-24 09:45:56'),(6,'8_2022.05.24_09.26.54','order_JZ1avSwYpeN94t',NULL,NULL,NULL,NULL,'2022-05-24 10:02:47','2022-05-24 10:02:47'),(7,'8_2022.05.24_09.26.54','order_JZ1dfB2V5R5LWL',NULL,NULL,NULL,NULL,'2022-05-24 10:05:22','2022-05-24 10:05:22'),(8,'8_2022.05.24_09.26.54','order_JZ1fT2IFfRzn15',NULL,NULL,NULL,NULL,'2022-05-24 10:07:05','2022-05-24 10:07:05'),(9,'8_2022.05.24_09.26.54','order_JZ1g6GKBF86LNz',NULL,NULL,NULL,NULL,'2022-05-24 10:07:41','2022-05-24 10:07:41'),(10,'8_2022.05.24_09.26.54','order_JZ1hZvM084eUjy','pay_JZ1jsG06bPtch5','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ1hZvM084eUjy\", \"razorpay_signature\": \"f7c5d58025853e7261aea56347e71d3a4efe38ec31cbd7d93c8d005ce49651fd\", \"razorpay_payment_id\": \"pay_JZ1jsG06bPtch5\"}}',NULL,'2022-05-24 10:09:05','2022-05-24 10:11:22'),(11,'11_2022.05.24_11.34.04','order_JZ4BD6pitC92Dq',NULL,NULL,NULL,NULL,'2022-05-24 12:34:31','2022-05-24 12:34:31'),(12,'11_2022.05.24_11.34.04','order_JZ4Y3cBBYTJqoF','pay_JZ4YWHw1BqEWVN','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ4Y3cBBYTJqoF\", \"razorpay_signature\": \"9b31c35d26f5f83d036508ae0ff1b1c066926aff85cf3f95fe55de98a413f444\", \"razorpay_payment_id\": \"pay_JZ4YWHw1BqEWVN\"}}',NULL,'2022-05-24 12:56:09','2022-05-24 12:56:43'),(13,'14_2022.05.24_01.46.29','order_JZ5YXICtqcLoif','pay_JZ5Yu8dsiNE9SW','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ5YXICtqcLoif\", \"razorpay_signature\": \"aa75b4a5dbcba5476e779e0ce44415530ccb8ccf525e2dae280197994e0f8ac6\", \"razorpay_payment_id\": \"pay_JZ5Yu8dsiNE9SW\"}}',NULL,'2022-05-24 13:55:18','2022-05-24 13:55:49'),(14,'15_2022.05.24_03.38.24','order_JZ7MXrhrGYKPcH','pay_JZ7Mq2MRpClVxR','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZ7MXrhrGYKPcH\", \"razorpay_signature\": \"5d7e9db640b633307c0f0eef03310c646f7099eb71f8921d1f87ff7dd8539d24\", \"razorpay_payment_id\": \"pay_JZ7Mq2MRpClVxR\"}}',NULL,'2022-05-24 15:41:20','2022-05-24 15:41:43'),(15,'13_2022.05.24_11.48.23','order_JZ9JigX9oLxtWj',NULL,NULL,NULL,NULL,'2022-05-24 17:36:02','2022-05-24 17:36:02'),(16,'16_2022.05.24_07.14.50','order_JZB1GAflcn2GtH','pay_JZB3lAGXUYh52X','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZB1GAflcn2GtH\", \"razorpay_signature\": \"9b2a5e576ae8c95a79c340abe4d4a43c11f33a5d55619511308be5a6a65ea8ba\", \"razorpay_payment_id\": \"pay_JZB3lAGXUYh52X\"}}',NULL,'2022-05-24 19:15:57','2022-05-24 19:18:28'),(17,'19_2022.05.26_01.33.53','order_JZg8mp1lUiTN3P','pay_JZg9PBfQ1XqjuB','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZg8mp1lUiTN3P\", \"razorpay_signature\": \"5a9e0604b73d49c40a7ab9d696e9d7fba92553ba08f9d7e8f0e1c8d2fcc2cd45\", \"razorpay_payment_id\": \"pay_JZg9PBfQ1XqjuB\"}}',NULL,'2022-05-26 01:42:35','2022-05-26 01:43:17'),(18,'20_2022.05.26_01.46.18','order_JZgDAIOvk8txpB','pay_JZgDUWsX8l2Ot4','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZgDAIOvk8txpB\", \"razorpay_signature\": \"4114445127d431c4bf7955ef1594413766b00282ace2f89a2cf0719d6c902b1f\", \"razorpay_payment_id\": \"pay_JZgDUWsX8l2Ot4\"}}',NULL,'2022-05-26 01:46:43','2022-05-26 01:47:10'),(19,'21_2022.05.26_10.07.02','order_JZokX2uLOSmy3O','pay_JZol4zVKF4o6Kr','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZokX2uLOSmy3O\", \"razorpay_signature\": \"e6e9dea7813c521dbe4c3bf9e1991abca7f62ab5347e770bd0438f63c15a8379\", \"razorpay_payment_id\": \"pay_JZol4zVKF4o6Kr\"}}',NULL,'2022-05-26 10:07:52','2022-05-26 10:08:30'),(20,'20_2022.05.26_07.10.10','order_JZy0LmHFccTtzy','pay_JZy0d3EgFWuExn','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JZy0LmHFccTtzy\", \"razorpay_signature\": \"77af9092cbd6a28b8ebac3a3941838890aecd0b9c37ff7d7c962c848d30dfade\", \"razorpay_payment_id\": \"pay_JZy0d3EgFWuExn\"}}',NULL,'2022-05-26 19:11:05','2022-05-26 19:11:29'),(21,'24_2022.05.30_02.12.30','order_JbT5mOH5z9OkLE','pay_JbT6JW0CJDft1I','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JbT5mOH5z9OkLE\", \"razorpay_signature\": \"c4a3f212396c8aa6662ac5f64d1f0b475f38c68dbc518d47546420f7ec26948b\", \"razorpay_payment_id\": \"pay_JbT6JW0CJDft1I\"}}',NULL,'2022-05-30 14:14:43','2022-05-30 14:15:18'),(22,'24_2022.05.30_02.54.48','order_JbTntXiMnJkhLO','pay_JbTo9k0aUXIaul','success','{\"status\": \"success\", \"response\": {\"razorpay_order_id\": \"order_JbTntXiMnJkhLO\", \"razorpay_signature\": \"8674ea089a775c0f63fdf7472a6e449b0e1e6adabd73a7ae3351558105b95d85\", \"razorpay_payment_id\": \"pay_JbTo9k0aUXIaul\"}}',NULL,'2022-05-30 14:56:29','2022-05-30 14:56:47');
/*!40000 ALTER TABLE `payment_references` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_setting`
--

DROP TABLE IF EXISTS `payment_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_setting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `callback_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_setting_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `payment_setting_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_setting`
--

LOCK TABLES `payment_setting` WRITE;
/*!40000 ALTER TABLE `payment_setting` DISABLE KEYS */;
INSERT INTO `payment_setting` VALUES (1,'MainServerPaymentGateway','rzp_test_j6GuvBBqvdXJVC','rbdyKTaZP7IWw6Xi7DTfXdcY','/api/wifi/order/process-payment',1,1,'pulse_1_2022-05-24_08-41-34',NULL,NULL),(2,'Razorpay','tempKey','tempSecret','tempCallBackURL',0,0,'pulsewifi_2022-05-24_08-42-25','2022-05-24 08:42:25','2022-05-24 08:42:25'),(3,'Razorpay','tempKey','tempSecret','tempCallBackURL',0,0,'pulsewifi_2022-05-24_08-49-11','2022-05-24 08:49:12','2022-05-24 08:49:12'),(4,'Razorpay','rzp_test_j6GuvBBqvdXJVC','rbdyKTaZP7IWw6Xi7DTfXdcY','/api/wifi/order/process-payment',0,0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 09:24:29','2022-05-24 09:24:29'),(5,'Razorpay','tempKey','tempSecret','tempCallBackURL',0,0,'pulsewifi_2022-05-24_19-14-32','2022-05-24 19:14:32','2022-05-24 19:14:32');
/*!40000 ALTER TABLE `payment_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wifi_user_id` int unsigned NOT NULL,
  `wifi_user_phone` bigint unsigned NOT NULL,
  `amount` double(8,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `payment_status` int NOT NULL,
  `payment_details` varchar(72) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_wifi_user_id_index` (`wifi_user_id`),
  KEY `payments_wifi_user_phone_index` (`wifi_user_phone`),
  KEY `payments_location_id_index` (`location_id`),
  KEY `payments_order_id_index` (`order_id`),
  KEY `payments_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `payments_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `payment_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_wifi_user_id_foreign` FOREIGN KEY (`wifi_user_id`) REFERENCES `wi_fi_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payouts`
--

DROP TABLE IF EXISTS `payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payouts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `wifi_user_id` int unsigned NOT NULL,
  `amount` double(8,2) NOT NULL,
  `payment_method` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `franchise_id` int unsigned NOT NULL,
  `distributor_id` int unsigned NOT NULL,
  `payout_status` int NOT NULL,
  `payout_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payouts_wifi_user_id_index` (`wifi_user_id`),
  KEY `payouts_franchise_id_index` (`franchise_id`),
  KEY `payouts_distributor_id_index` (`distributor_id`),
  KEY `payouts_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `payouts_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payouts_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payouts_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payouts_wifi_user_id_foreign` FOREIGN KEY (`wifi_user_id`) REFERENCES `wi_fi_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payouts`
--

LOCK TABLES `payouts` WRITE;
/*!40000 ALTER TABLE `payouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdoa_plan`
--

DROP TABLE IF EXISTS `pdoa_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdoa_plan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `max_wifi_router_count` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdoa_plan`
--

LOCK TABLES `pdoa_plan` WRITE;
/*!40000 ALTER TABLE `pdoa_plan` DISABLE KEYS */;
INSERT INTO `pdoa_plan` VALUES (1,'Demo',3000.00,100,1,NULL,NULL),(2,'PDOA20',23576.00,20,1,'2022-05-24 18:56:57','2022-05-24 18:56:57');
/*!40000 ALTER TABLE `pdoa_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdoas`
--

DROP TABLE IF EXISTS `pdoas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdoas` (
  `id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform_bg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `franchise_fee` int NOT NULL,
  `distributor_fee` int NOT NULL,
  `pdoa_status` int NOT NULL DEFAULT '0',
  `pdoa_plan_id` int unsigned NOT NULL,
  `domain_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cin_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporation_cert` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `favicon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pdoas_pdoa_plan_id_index` (`pdoa_plan_id`),
  CONSTRAINT `pdoas_pdoa_plan_id_foreign` FOREIGN KEY (`pdoa_plan_id`) REFERENCES `pdoa_plan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdoas`
--

LOCK TABLES `pdoas` WRITE;
/*!40000 ALTER TABLE `pdoas` DISABLE KEYS */;
INSERT INTO `pdoas` VALUES ('pulse_1_2022-05-24_08-41-34','Pulse WiFi','default_logo.png','',1000,1000,1,1,'console.pulsewifi.net','SuperAdmin','Super','Admin','pulsesuper@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kamalpur','Mongalkote','West Bengal','India','713147',NULL,1,NULL,NULL,''),('pulsewifi_2022-05-24_08-42-25','Home','public/PDOA/pulsewifi_2022-05-24_08-42-25_logo.jpg','public/PDOA/pulsewifi_2022-05-24_08-42-25_bg.jpg',1000,1000,0,1,'Home','thinkofsan+1@gmail.com','Santhosh','Kumar','thinkofsan+1@gmail.com','9003300303','2323232','public/incorporation_cert/incorporation_cert_4.pdf','Demo','Demo','Aadhaar','232323232',NULL,NULL,'Sundar Nagar','Trichy','Tamil Nadu','India','620021','232222332',0,'2022-05-24 08:42:25','2022-05-24 08:45:10',''),('pulsewifi_2022-05-24_08-49-11','Home','public/PDOA/pulsewifi_2022-05-24_08-49-11_logo.jpg','public/PDOA/pulsewifi_2022-05-24_08-49-11_bg.jpg',1000,1000,0,1,'home.com','bliss.glee.916+1@gmail.com','Home','Kumar','bliss.glee.916+1@gmail.com','9003300301','232323','public/incorporation_cert/incorporation_cert_6.jpg','Home','home','Aadhaar','23232323',NULL,NULL,'1st Avenue','Iowa','Andhra Pradesh','India','232222','23232323',0,'2022-05-24 08:49:11','2022-05-24 11:28:59',''),('pulsewifi_2022-05-24_09-24-28','Test PDOA','public/PDOA/pulsewifi_2022-05-24_09-24-28_logo.png','',1000,1000,0,1,'pulsetest.cnctdwifi.com','kumar@cnctdwifi.com','Test','PDOA','kumar@cnctdwifi.com','8447578754','191020aa','public/incorporation_cert/incorporation_cert_8.jpg','Test PDOA','CEO','Aadhaar','12211aaa',NULL,NULL,'26. D14, Ardee City','Gurgaon','Haryana','India','122003','191001aa',0,'2022-05-24 09:24:28','2022-05-24 09:24:29',''),('pulsewifi_2022-05-24_19-14-32','PocketWiFi','public/PDOA/pulsewifi_2022-05-24_19-14-32_logo.png','',1499,17700,1,2,'login.pocketwifi.in','simplifon@gmail.com','BIplob','Mandal','simplifon@gmail.com','8926364756','U72100WB2016PTC218401','public/incorporation_cert/incorporation_cert_16.png','Sundarban Forest Travel Private Limited','Director','Aadhaar','831677017516',NULL,NULL,'Pathan Khal, Gosaba','South 24 Parganas','West Bengal','India','743611','19AAXCS8602A1Z2',0,'2022-05-24 19:14:32','2022-05-24 19:14:32','');
/*!40000 ALTER TABLE `pdoas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Wifi_Users` int NOT NULL DEFAULT '0',
  `Wifi_Router` int NOT NULL DEFAULT '0',
  `Location` int NOT NULL DEFAULT '0',
  `Distributor` int NOT NULL DEFAULT '0',
  `Franchise` int NOT NULL DEFAULT '0',
  `Internet_Plan_Setting` int NOT NULL DEFAULT '0',
  `Internet_Plan_View` int NOT NULL DEFAULT '0',
  `Payout_Setting` int NOT NULL DEFAULT '0',
  `Payout_Log` int NOT NULL DEFAULT '0',
  `Payment_Setting` int NOT NULL DEFAULT '0',
  `Payment_Log` int NOT NULL DEFAULT '0',
  `Payout_Log_Process` int NOT NULL DEFAULT '0',
  `Leads` int NOT NULL DEFAULT '0',
  `Add_Leads` int NOT NULL DEFAULT '0',
  `SMS_Gateway` int NOT NULL DEFAULT '0',
  `SMS_Template` int NOT NULL DEFAULT '0',
  `Mail_Server` int NOT NULL DEFAULT '0',
  `Email_Template` int NOT NULL DEFAULT '0',
  `Network_Setting` int NOT NULL DEFAULT '0',
  `role_management` int NOT NULL DEFAULT '0',
  `Add_PDOA` int NOT NULL DEFAULT '0',
  `PDOA_Management` int NOT NULL DEFAULT '0',
  `PDOA_Plan` int NOT NULL DEFAULT '0',
  `Staff_Management` int NOT NULL DEFAULT '0',
  `WiFi_Router_Models` int NOT NULL DEFAULT '0',
  `Products` int NOT NULL DEFAULT '1',
  `Product_Management` int NOT NULL DEFAULT '0',
  `Process_Product` int NOT NULL DEFAULT '0',
  `Cart` int NOT NULL DEFAULT '0',
  `required` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'superadmin','SuperAdmin',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,NULL,NULL),(2,'admin','Admin',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,1,1,0,1,NULL,'2022-05-26 05:10:04'),(3,'support','Support',1,1,1,1,1,0,1,0,1,0,1,1,1,0,0,1,0,1,0,0,0,0,0,0,0,0,1,1,0,1,NULL,NULL),(4,'Distributor','Distributor',0,0,1,0,1,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,1,1,NULL,NULL),(5,'Franchise','Franchise',0,0,1,0,0,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,1,1,NULL,NULL),(6,'Demo Super Admin','Demo Super Admin',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,1,1,1,0,'2022-05-24 19:44:38','2022-05-24 19:46:00');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_gateway`
--

DROP TABLE IF EXISTS `sms_gateway`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_gateway` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_gateway_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `sms_gateway_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_gateway`
--

LOCK TABLES `sms_gateway` WRITE;
/*!40000 ALTER TABLE `sms_gateway` DISABLE KEYS */;
INSERT INTO `sms_gateway` VALUES (1,'Msg91','113856AcmCCQZro6219f042P1','tempSecret',0,'pulsewifi_2022-05-24_08-42-25','2022-05-24 08:42:25','2022-05-24 08:42:25'),(2,'Msg91','113856AcmCCQZro6219f042P1','tempSecret',0,'pulsewifi_2022-05-24_08-49-11','2022-05-24 08:49:12','2022-05-24 08:49:12'),(3,'Msg91','113856AcmCCQZro6219f042P1','tempSecret',0,'pulsewifi_2022-05-24_09-24-28','2022-05-24 09:24:29','2022-05-24 09:24:29'),(4,'Msg91','113856AcmCCQZro6219f042P1','tempSecret',0,'pulsewifi_2022-05-24_19-14-32','2022-05-24 19:14:32','2022-05-24 19:14:32'),(5,'Msg91','113856AcmCCQZro6219f042P1','tempSecret',0,'pulse_1_2022-05-24_08-41-34','2022-05-30 16:20:45','2022-05-30 16:20:45');
/*!40000 ALTER TABLE `sms_gateway` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_template`
--

DROP TABLE IF EXISTS `sms_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dlt_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '0',
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sms_template_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `sms_template_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_template`
--

LOCK TABLES `sms_template` WRITE;
/*!40000 ALTER TABLE `sms_template` DISABLE KEYS */;
INSERT INTO `sms_template` VALUES (1,'Register SMS','622726c014074f713b220d25','PLSNET','Hello World!',1,'pulsewifi_2022-05-24_09-24-28','2022-05-29 23:23:54','2022-05-29 23:24:03'),(2,'Register SMS','622726c014074f713b220d25','PLSNET','Hello World!',1,'pulse_1_2022-05-24_08-41-34','2022-05-31 00:04:04','2022-05-31 00:04:04');
/*!40000 ALTER TABLE `sms_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role` int unsigned NOT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified` int NOT NULL DEFAULT '0',
  `email_verification_code` int DEFAULT NULL,
  `profile_img_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` int NOT NULL DEFAULT '0',
  `belongs_to` int DEFAULT NULL,
  `lead_process` int NOT NULL DEFAULT '0',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` int NOT NULL,
  `latitude` double(255,2) NOT NULL DEFAULT '0.00',
  `longitude` double(255,2) NOT NULL DEFAULT '0.00',
  `phone_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_proof_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revenue_model` int DEFAULT NULL,
  `revenue_sharing_ratio` int DEFAULT NULL,
  `beneficiary_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifsc_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ac_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passbook_cheque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` int DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id_index` (`id`),
  KEY `users_role_index` (`role`),
  KEY `users_pdoa_id_index` (`pdoa_id`),
  CONSTRAINT `users_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_role_foreign` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'pulse_1_2022-05-24_08-41-34','pulsesuper@gmail.com','Pulse','WiFi','pulsesuper@gmail.com',1,NULL,'public/profile_img/avatar_1.png','$2y$10$tHCOzrJuqYvv5VdNJJfAgu3PV2hLIHwgCBIjiZN8srt7FGltn4Ylq',1,NULL,2,'Kamalpur','Mongalkote','West Bengal','India',713147,0.00,0.00,'8447578754',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-25 09:09:18'),(2,4,'pulse_1_2022-05-24_08-41-34','testdistributor','test','distributor','testdistributor@gmail.com',1,NULL,NULL,'$2y$10$vP1k8/3QkhmDYaYYkLSvheNcdcHp5MM27z/XqotCwRKQENNTS18I.',1,NULL,2,'Kamalpur','Mongalkote','West Bengal','India',123123,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,5,'pulse_1_2022-05-24_08-41-34','testfranchise','test','franchise','testfranchise@gmail.com',1,NULL,NULL,'$2y$10$3WGnoOqO5nxPmUZnV2jFCOASuEwpXwsRYzdq3f18p.XpQNqcnMyoO',1,2,2,'Kamalpur','Mongalkote','West Bengal','India',713147,0.00,0.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,2,'pulsewifi_2022-05-24_08-42-25','thinkofsana@gmail.com','Santhosh','Kumar','thinkofsana@gmail.com',0,2487,NULL,'abcd1234',1,NULL,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300303','Demo','Demo','Aadhaar','232323232',NULL,NULL,'232222332',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-24 08:42:25','2022-05-25 20:54:21'),(5,4,'pulse_1_2022-05-24_08-41-34','thinkofsan+2@gmail.com','Santhosh','Kumar','thinkofsan+2@gmail.com',0,7262,NULL,'$2y$10$uQzK7FKNNZXAw8psUnxU8e/.UjTupq0I3bVHkMx7BtrXWDR6zF7pW',1,NULL,2,'Sundar Nagar','Trichy','Andhra Pradesh','India',620021,10.77,78.68,'9003300303','Home','Home','Aadhaar','23232323',NULL,NULL,'2323232',5,5,'San','23232','232323232','public/passbook_cheque/passbook_cheque_5.jpg',NULL,NULL,'2022-05-24 08:47:47','2022-05-24 09:02:43'),(6,2,'pulsewifi_2022-05-24_08-49-11','bliss.glee.916+1@gmail.com','Home','Kumar','bliss.glee.916+1@gmail.com',1,1287,NULL,'$2y$10$ejYkczlC8eNR11lCVFQ4MeOJbhqyBmdaHtOtNJ8DLfD.9K8uEqLRu',1,NULL,2,'1st Avenue','Iowa','Andhra Pradesh','India',232222,15.91,79.74,'9003300301','Home','home','Aadhaar','23232323',NULL,NULL,'23232323',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-24 08:49:12','2022-05-24 11:29:36'),(7,4,'pulse_1_2022-05-24_08-41-34','thinkofsan+3@gmail.com','Santhosh','Kumar','thinkofsan+3@gmail.com',1,7137,NULL,'$2y$10$I70YFCVJyjt5TleiPcQNM.c1uUN9N9nhQ4CBzlNk0XPHXJlZFr8dO',1,NULL,2,'Sunar Nagar','Trichy','Tamil Nadu','India',620021,10.76,78.69,'9003300304','Demo','demo','Aadhaar','2332323',NULL,NULL,'232323323',10,10,'Home','232323','2323323232','public/passbook_cheque/passbook_cheque_7.jpg',NULL,NULL,'2022-05-24 09:04:21','2022-05-24 11:30:17'),(8,2,'pulsewifi_2022-05-24_09-24-28','kumar@cnctdwifi.com','Test','PDOA','kumar@cnctdwifi.com',1,7174,NULL,'$2y$10$ZnM3Qgosp8QBuhmoWpD3lOZVi4AcfTuPhG1NIzsLYnt93P.6xe52u',1,NULL,2,'26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'8447578754','Test PDOA','CEO','Aadhaar','12211aaa',NULL,NULL,'191001aa',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-24 09:24:29','2022-05-24 09:28:02'),(9,4,'pulsewifi_2022-05-24_09-24-28','kr.mrinal@gmail.com','Test','Distributor','kr.mrinal@gmail.com',0,1135,NULL,'$2y$10$/h6oRuDK1N/nergPJjrY/OME2U60ThyHSzto5X0D/23cv42Um4arG',1,NULL,2,'26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'8447578765','Test D','CEO','Aadhaar','122aa',NULL,NULL,'1910aa',5,10,'KM','1110aa','919101010','public/passbook_cheque/passbook_cheque_9.jpg',NULL,NULL,'2022-05-24 10:38:09','2022-05-24 10:38:09'),(10,4,'pulse_1_2022-05-24_08-41-34','thinkofsan+4@gmail.com','Santhosh','Kumar','thinkofsan+4@gmail.com',0,7768,NULL,'$2y$10$lPNt3HTi9w/nm5VVyhWvee4MJJRwEZBd66Q5gGiqhC1NtkepcZ98i',1,NULL,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300310','Demo','Demo','Aadhaar','23232323',NULL,NULL,'23232323',5,10,'Santhsoh','23232','232323232','public/passbook_cheque/passbook_cheque_10.jpg',NULL,NULL,'2022-05-24 11:32:04','2022-05-24 11:46:13'),(11,5,'pulse_1_2022-05-24_08-41-34','bliss.glee.916+2@gmail.com','Franchise','User','bliss.glee.916+2@gmail.com',0,9479,NULL,'$2y$10$QmYSgN1IUF7SoKgkNI88k.g6cG1Ogbl8HOzGq1QrjSBPO1UzBcYJ.',1,2,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300311','Franchise','Franchise','Aadhaar','232323232','public/upload_id_proof/upload_id_proof_11.jpg','23232232','2323233',10,10,'Home','33434','343434343','public/passbook_cheque/passbook_cheque_11.jpg',NULL,NULL,'2022-05-24 11:33:47','2022-05-24 11:46:37'),(12,4,'pulsewifi_2022-05-24_09-24-28','newoj77729@roxoas.com','Kumar','Test','newoj77729@roxoas.com',0,3085,NULL,'$2y$10$0aT7PYCGhXhsnab5LfMAJ.uIn5CJmvZk/nJp7ffn7LZcnvhAP93Ki',1,NULL,2,'Sundar Nagar','Trichy','Andhra Pradesh','India',620021,10.77,78.68,'9003300313','Home','Home','Aadhaar','2233232323',NULL,NULL,'23232323',5,5,'Santhosh','23232','3232323','public/passbook_cheque/passbook_cheque_12.jpg',NULL,NULL,'2022-05-24 11:42:25','2022-05-24 11:42:25'),(13,5,'pulsewifi_2022-05-24_09-24-28','bliss.glee.916@gmail.com','Fresh','Franchise','bliss.glee.916@gmail.com',0,6590,NULL,'$2y$10$LdpvOLXUce9DsS.w88Z/f.nKsmmcqRlZJm41ZUklnnmYD0FZTRQXa',1,9,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300312','Demo','demo','Aadhaar','12233322','public/upload_id_proof/upload_id_proof_13.jpg','23232','2323232',5,5,'User','2323232','23232232','public/passbook_cheque/passbook_cheque_13.jpg',NULL,NULL,'2022-05-24 11:48:15','2022-05-24 11:48:15'),(14,5,'pulsewifi_2022-05-24_09-24-28','bliss.glee.916+4@gmail.com','Santhosh','Kumar','bliss.glee.916+4@gmail.com',1,5947,NULL,'$2y$10$.TQO/Cmks9PHnpqBz3UCuOWIyr9gv5CwHK8l7.2kJEppNC4Oo5NMi',1,9,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300343','Home','Home','Aadhaar','23232323','public/upload_id_proof/upload_id_proof_14.jpg','2322323223','232323232',5,10,'Home','23232323','232323232','public/passbook_cheque/passbook_cheque_14.jpg',NULL,NULL,'2022-05-24 13:46:15','2022-05-24 15:36:37'),(15,5,'pulsewifi_2022-05-24_09-24-28','thinkofsan@gmail.com','New','Franchise','thinkofsan@gmail.com',1,9822,NULL,'$2y$10$lFqSYqdTktPfz8tz7kCVHe9nNK7jxBerf5XdqsGoWu/l2URkp4WaG',1,9,2,'Sundar Nagar','Trichy','Tamil Nadu','India',620021,10.77,78.68,'9003300344','Home','home','Aadhaar','23232323','public/upload_id_proof/upload_id_proof_15.jpg','232323','23243434',5,10,'San','2232323','232323','public/passbook_cheque/passbook_cheque_15.jpg',NULL,NULL,'2022-05-24 15:38:14','2022-05-24 15:41:11'),(16,2,'pulsewifi_2022-05-24_19-14-32','biplob892636@gmail.com','BIplob','Mandal','biplob892636@gmail.com',1,9971,NULL,'$2y$10$ZnM3Qgosp8QBuhmoWpD3lOZVi4AcfTuPhG1NIzsLYnt93P.6xe52u',1,NULL,2,'Pathan Khal, Gosaba','South 24 Parganas','West Bengal','India',743611,22.17,88.81,'8926364756','Sundarban Forest Travel Private Limited','Director','Aadhaar','831677017516',NULL,NULL,'19AAXCS8602A1Z2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-24 19:14:32','2022-05-24 19:28:16'),(17,6,'pulse_1_2022-05-24_08-41-34','hello@mijan.com','Mijanur','Rahaman','hello@mijan.com',1,5235,NULL,'$2y$10$ZnM3Qgosp8QBuhmoWpD3lOZVi4AcfTuPhG1NIzsLYnt93P.6xe52u',1,NULL,2,'Kolkata','Kolkata','West Bengal','India',700099,22.50,88.41,'9999999999',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-05-24 19:43:52','2022-05-24 19:47:51'),(20,5,'pulsewifi_2022-05-24_09-24-28','mrinal@hasthcraft.com','Pulse WiFi','Cafe','mrinal@hasthcraft.com',1,1295,NULL,'$2y$10$rJXRh2E6q6tZNenBYsmbTu2II9Gb3Y1.77Ow9mwqMuTydfwnOscAy',1,9,2,'26. D14, Ardee City','Gurgaon','Haryana','India',122003,28.44,77.07,'8447578754','Pulse WiFi Cafe','CEO','Aadhaar','11001aa','public/upload_id_proof/upload_id_proof_20.jpg','10011a','1910aa0',5,10,'Test Cafe','101001aa','01010100101','public/passbook_cheque/passbook_cheque_20.jpg',NULL,NULL,'2022-05-26 01:46:03','2022-05-26 01:46:40'),(21,4,'pulse_1_2022-05-24_08-41-34','simplifon@gmail.com','Muhammad Akram','Sheikh','simplifon@gmail.com',1,4984,NULL,'$2y$10$Mr9F.PJm8G1PyUsSGY2is.30VyZna8c8REg.13BeRxK3/kx.VgDqa',1,NULL,2,'Kamalpur, Mongalkote','Purba Barddhaman','West Bengal','India',713147,23.52,87.89,'9475753520','Simplifon Technologies Private Limited','Director','Aadhaar','1234567890',NULL,NULL,'19ABACS2203B2ZZ',20,20,'MD AKRAM SK','SBIN0012388','31955534323','public/passbook_cheque/passbook_cheque_21.png',NULL,NULL,'2022-05-26 10:05:58','2022-05-26 10:07:48'),(23,5,'pulse_1_2022-05-24_08-41-34','hello.hostsect@gmail.com','Subir','Roy','hello.hostsect@gmail.com',0,1740,NULL,'$2y$10$gAlyHk.Ur9DynuVB5OD5QudZAFtr095uRE6cW0BKlMOcwUAzB0PF6',1,21,2,'Putunda','Purba Barddhaman','West Bengal','India',713149,23.22,87.98,'9153681869','CSC Center','Manger','Aadhaar','708114090092','public/upload_id_proof/upload_id_proof_23.jpeg','BGDPR4522E','19AAXXXXXXXXXXXX2',20,70,'SUBIR ROY','SBIN0006116','32188872312','public/passbook_cheque/passbook_cheque_23.jpeg',NULL,NULL,'2022-05-26 20:19:01','2022-05-26 20:19:01'),(24,5,'pulse_1_2022-05-24_08-41-34','md.akram.sk.10@gmail.com','Muhammad Akram','Sheikh','md.akram.sk.10@gmail.com',1,4827,NULL,'$2y$10$ReOONR4/mMi7R.ECqdljG.s4J0t7qmV.gGWVUL95XVn4ISXu45oue',1,21,2,'Kamalpur','Mongalkote','West Bengal','India',713147,23.52,87.89,'9475753520','STPL','Tester','Aadhaar','1234567890','public/upload_id_proof/upload_id_proof_24.png','123456','AHDFHSFFG',20,70,'MD AKRAM SK','SBIN0012388','31955534323','public/passbook_cheque/passbook_cheque_24.png',NULL,NULL,'2022-05-30 14:12:19','2022-05-30 14:14:40');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_fi_devices`
--

DROP TABLE IF EXISTS `wi_fi_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wi_fi_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `challenge` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usermac` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int NOT NULL,
  `pdoa` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_generate_time` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_fi_devices`
--

LOCK TABLES `wi_fi_devices` WRITE;
/*!40000 ALTER TABLE `wi_fi_devices` DISABLE KEYS */;
INSERT INTO `wi_fi_devices` VALUES (1,'8447578754','8511','111','98-12-AB-C1-D8-11','KX9wPG','Unknown','Unknown',2,'pulsewifi_2022-05-24_09-24-28','Not Verified','2022-05-31 00:06:56','2022-05-29 15:53:33','2022-05-31 00:06:56'),(2,'8447578754','7140','cf80bac673520ca92c0aafabb8ba7796','1E-CF-F7-EB-52-8D','aSJUNg','AndroidOS','Unknown',6,'pulsewifi_2022-05-24_09-24-28','Verified','2022-05-29 17:45:53','2022-05-29 17:45:53','2022-05-29 17:45:59'),(3,'9933711955','9507','52bae369bf18ac7ce6d1271b039cec79','DA-63-08-23-27-7D','13rP2V','AndroidOS','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 07:39:35','2022-05-29 18:11:38','2022-05-31 07:39:49'),(4,'9475753520','8374','036d8816c8a72d51365b4c54e0eb2774','F0-18-98-78-6A-7D','O8kctf','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 09:40:52','2022-05-29 18:19:48','2022-05-31 09:41:00'),(5,'9475753520','3717','a1a301e1172e2251f7bc384482816f8d','62-25-BB-30-D4-C7','EaPdSG','iOS','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 07:35:20','2022-05-29 18:30:30','2022-05-31 07:35:38'),(6,'8447578754','5947','4515f349aec3f6e88737242f598d06b8','E6-45-45-B7-8B-45','W3SrVU','AndroidOS','Unknown',6,'pulsewifi_2022-05-24_09-24-28','Verified','2022-05-31 08:04:38','2022-05-29 23:58:44','2022-05-31 08:05:02'),(7,'8447578754','4047','31bef5ca8b0685422406e5457a88181b','50-E0-85-C2-52-77','fRiftX','Unknown','Unknown',6,'pulsewifi_2022-05-24_09-24-28','Verified','2022-05-31 08:01:11','2022-05-30 00:05:34','2022-05-31 08:01:25'),(8,'9475753520','5562','d4b058a2a5c4ada4b5cf96f1d4b5265a','DA-63-08-23-27-7D','YcEEqt','AndroidOS','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 17:56:16','2022-05-30 13:49:26','2022-05-31 17:56:25'),(9,'6260157700','3305','74a8199a62bb9ad697770e2d7c1356ae','E4-BE-ED-05-E8-10','EnzEKc','Unknown','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:16:46','2022-05-30 15:02:41','2022-05-30 15:16:47'),(10,'7987820887','2603','5d0f31e8199deba204188b18ba0a0145','1E-80-4D-78-6A-50','D7kquX','AndroidOS','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:15:14','2022-05-30 15:03:44','2022-05-30 15:15:14'),(11,'9111005327','7165','5d0f31e8199deba204188b18ba0a0145','1E-80-4D-78-6A-50','GZCFNf','AndroidOS','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:09:22','2022-05-30 15:07:21','2022-05-30 15:09:22'),(12,'8888888888','3486','111','98-12-AB-C9-D8-11','q7i9Mw','Unknown','Unknown',2,'pulsewifi_2022-05-24_09-24-28','Verified','2022-05-30 15:10:35','2022-05-30 15:10:35','2022-05-30 15:10:42'),(13,'9111005328','5575','74a8199a62bb9ad697770e2d7c1356ae','E4-BE-ED-05-E8-10','P7u4Re','Unknown','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:11:28','2022-05-30 15:11:28','2022-05-30 15:11:28'),(14,'6260157700','9601','01e1130750033b7af3b01cde0c50ad74','FE-6E-7C-57-61-CA','5mAU62','AndroidOS','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:15:30','2022-05-30 15:15:30','2022-05-30 15:15:30'),(15,'9933711955','3772','d965ce9e1d2fe30348133ee220e7e5e6','F0-18-98-78-6A-7D','Ic4kOR','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 18:08:24','2022-05-30 15:18:14','2022-05-31 18:08:37'),(16,'8888899999','9700','d965ce9e1d2fe30348133ee220e7e5e6','F0-18-98-78-6A-7D','jbyNol','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 15:27:38','2022-05-30 15:27:38','2022-05-30 15:27:38'),(17,'9111005328','5020','9504758ff040857bbad1baf69b9254fd','FE-6E-7C-57-61-CA','9ZFTPr','AndroidOS','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-31 01:42:00','2022-05-30 15:38:28','2022-05-31 01:42:00'),(18,'8899889988','2397','d965ce9e1d2fe30348133ee220e7e5e6','F0-18-98-78-6A-7D','TFC277','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 16:13:36','2022-05-30 16:04:42','2022-05-30 16:13:36'),(19,'8447578754','9607','d965ce9e1d2fe30348133ee220e7e5e6','F0-18-98-78-6A-7D','XxWBp0','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 16:11:40','2022-05-30 16:10:34','2022-05-30 16:11:40'),(20,'8447578754','3846','d965ce9e1d2fe30348133ee220e7e5e6','F0-18-98-78-60-7D','Odu55e','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 16:35:03','2022-05-30 16:34:02','2022-05-30 16:35:03'),(21,'9111005428','9104','5e699cbfef1ee9e699a6fcb9f2f61825','FE-6E-7C-57-61-CA','nwq7PO','AndroidOS','Unknown',10,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 16:35:03','2022-05-30 16:34:28','2022-05-30 16:35:03'),(22,'8447578754','2169','d965ce9e1d2fe30348133ee220e7e5e6','F9-18-98-78-6A-7D','Fzwkz8','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-30 23:14:03','2022-05-30 23:14:03','2022-05-30 23:14:03'),(23,'8447578754','9078','d965ce9e1d2fe30348133ee220e7e5e6','F0-21-98-78-6A-7D','XeCtdC','Unknown','Unknown',6,'pulse_1_2022-05-24_08-41-34','Not Verified','2022-05-31 00:20:57','2022-05-30 23:54:52','2022-05-31 00:20:57'),(24,'9091224622','1139','52fcc8dc0c952882eab1850b90206d9d','6E-DF-FC-2F-B6-E2','hQ3HjZ','AndroidOS','Unknown',6,'pulse_1_2022-05-24_08-41-34','Verified','2022-05-31 06:59:21','2022-05-31 06:57:35','2022-05-31 06:59:38');
/*!40000 ALTER TABLE `wi_fi_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_fi_orders`
--

DROP TABLE IF EXISTS `wi_fi_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wi_fi_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internet_plan_id` int NOT NULL,
  `amount` int NOT NULL,
  `franchise_id` int DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_fi_orders`
--

LOCK TABLES `wi_fi_orders` WRITE;
/*!40000 ALTER TABLE `wi_fi_orders` DISABLE KEYS */;
INSERT INTO `wi_fi_orders` VALUES (1,'8447578754',1,200,NULL,0,NULL,'2022-05-30 01:51:57','2022-05-30 01:51:57',NULL),(2,'8447578754',1,200,NULL,1,'pay_JbGdB2Gp9AOrvs','2022-05-30 02:02:51','2022-05-30 02:03:24',NULL),(3,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:14','2022-05-30 02:08:14',NULL),(4,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:15','2022-05-30 02:08:15',NULL),(5,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:28','2022-05-30 02:08:28',NULL),(6,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:29','2022-05-30 02:08:29',NULL),(7,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:30','2022-05-30 02:08:30',NULL),(8,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:35','2022-05-30 02:08:35',NULL),(9,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:36','2022-05-30 02:08:36',NULL),(10,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:36','2022-05-30 02:08:36',NULL),(11,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:36','2022-05-30 02:08:36',NULL),(12,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:39','2022-05-30 02:08:39',NULL),(13,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:42','2022-05-30 02:08:42',NULL),(14,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:43','2022-05-30 02:08:43',NULL),(15,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:08:43','2022-05-30 02:08:43',NULL),(16,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:09:27','2022-05-30 02:09:27',NULL),(17,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:10:06','2022-05-30 02:10:06',NULL),(18,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:10:42','2022-05-30 02:10:42',NULL),(19,'8447578754',2,500,NULL,0,NULL,'2022-05-30 02:11:11','2022-05-30 02:11:11',NULL),(20,'8447578754',2,500,NULL,0,NULL,'2022-05-30 02:14:22','2022-05-30 02:14:22',NULL),(21,'8447578754',2,500,NULL,0,NULL,'2022-05-30 02:18:04','2022-05-30 02:18:04',NULL),(22,'8447578754',2,500,NULL,0,NULL,'2022-05-30 02:19:21','2022-05-30 02:19:21',NULL),(23,'8447578754',1,200,NULL,0,NULL,'2022-05-30 02:20:07','2022-05-30 02:20:07',NULL),(24,'8447578754',2,500,NULL,0,NULL,'2022-05-30 02:20:15','2022-05-30 02:20:15',NULL),(25,'8447578754',1,200,NULL,1,'pay_JbHKpu7zfSIERl','2022-05-30 02:44:09','2022-05-30 02:44:45',NULL);
/*!40000 ALTER TABLE `wi_fi_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_fi_user_verifies`
--

DROP TABLE IF EXISTS `wi_fi_user_verifies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wi_fi_user_verifies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `challenge` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usermac` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int NOT NULL,
  `group_id` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wi_fi_user_verifies_url_code_unique` (`url_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_fi_user_verifies`
--

LOCK TABLES `wi_fi_user_verifies` WRITE;
/*!40000 ALTER TABLE `wi_fi_user_verifies` DISABLE KEYS */;
/*!40000 ALTER TABLE `wi_fi_user_verifies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wi_fi_users`
--

DROP TABLE IF EXISTS `wi_fi_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wi_fi_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdoa` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wi_fi_users_pdoa_index` (`pdoa`),
  CONSTRAINT `wi_fi_users_pdoa_foreign` FOREIGN KEY (`pdoa`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wi_fi_users`
--

LOCK TABLES `wi_fi_users` WRITE;
/*!40000 ALTER TABLE `wi_fi_users` DISABLE KEYS */;
INSERT INTO `wi_fi_users` VALUES (1,NULL,'8447578754',NULL,'$2y$10$E3MvHpR.j8hI8UNCt6SWZeM30l5e/WRle7wSJXW1b833CG9J1AOkW','pulsewifi_2022-05-24_09-24-28','2022-05-29 15:51:03','2022-05-29 15:51:03'),(2,NULL,'9933711955',NULL,'$2y$10$hruoeH8m8sV7c.kW6VUqhOa1WlRRgHQaLWUxUZmAZKl3h2YZU1TuW','pulse_1_2022-05-24_08-41-34','2022-05-29 18:11:38','2022-05-29 18:11:38'),(3,NULL,'9475753520',NULL,'$2y$10$cKV52/MuMr32iecKgiWULe8YkvqHFvlQPmsVhULqMaNgkEcd5WkRa','pulse_1_2022-05-24_08-41-34','2022-05-29 18:19:48','2022-05-29 18:19:48'),(4,NULL,'6260157700',NULL,'$2y$10$lY6WFf8NnuTis1nEEB5C7OJqu3fp84nVlWat1we23.B2miPY.b2QS','pulse_1_2022-05-24_08-41-34','2022-05-30 15:02:41','2022-05-30 15:02:41'),(5,NULL,'7987820887',NULL,'$2y$10$JEBbOu0Q4fS4ms2QdIX4xOZy2ebuPSWoce9Zpww8y8ADGJTQjiaLa','pulse_1_2022-05-24_08-41-34','2022-05-30 15:03:44','2022-05-30 15:03:44'),(6,NULL,'9111005327',NULL,'$2y$10$K9KwumZUP0kmf8oO.nF7eO7wVnXhW46gLZ7J/5yRWqqPIt4F54wH6','pulse_1_2022-05-24_08-41-34','2022-05-30 15:07:21','2022-05-30 15:07:21'),(7,NULL,'8888888888',NULL,'$2y$10$GorPiKoS8OHFLxtTiNVCpO67sC2ilUZ6WGAz1qU3JinXGt.NNWGdG','pulsewifi_2022-05-24_09-24-28','2022-05-30 15:10:35','2022-05-30 15:10:35'),(8,NULL,'9111005328',NULL,'$2y$10$HPMci6KDrvQqUdzsEfVpzeb.ETXheKisEaRD063On8qmhSJ9nRhdG','pulse_1_2022-05-24_08-41-34','2022-05-30 15:11:28','2022-05-30 15:11:28'),(9,NULL,'8888899999',NULL,'$2y$10$SsYZut/M7ruWFudo3WunQ.GBK4qRILHEP2uYkVI9Tw0zObJvf63nm','pulse_1_2022-05-24_08-41-34','2022-05-30 15:27:38','2022-05-30 15:27:38'),(10,NULL,'8899889988',NULL,'$2y$10$ZJf2tIOOtFvFx5Ayj.NMlupA7xeYElETic87RSqBztGfS4vonWZVm','pulse_1_2022-05-24_08-41-34','2022-05-30 16:04:42','2022-05-30 16:04:42'),(11,NULL,'8447578754',NULL,'$2y$10$dsv5CXSxEriKjvpvVADBhugFDaKwxXPKbSgIxa.rt.IzoYOb2FTq2','pulse_1_2022-05-24_08-41-34','2022-05-30 16:10:34','2022-05-30 16:10:34'),(12,NULL,'9111005428',NULL,'$2y$10$Fugqz02bbCM9CsFoj4yTVeOm19YR/p2Gs9yE30GbkLS83vkf0v7Qq','pulse_1_2022-05-24_08-41-34','2022-05-30 16:34:28','2022-05-30 16:34:28'),(13,NULL,'9091224622',NULL,'$2y$10$v6K6h1nKcbH1R2//6k1lm.znzi8.eCveT7DuASTKSx794Ma2DeXKe','pulse_1_2022-05-24_08-41-34','2022-05-31 06:57:35','2022-05-31 06:57:35');
/*!40000 ALTER TABLE `wi_fi_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wifi_router`
--

DROP TABLE IF EXISTS `wifi_router`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wifi_router` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_version` int DEFAULT NULL,
  `last_online` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location_id` int unsigned DEFAULT NULL,
  `pdoa_id` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` int unsigned DEFAULT NULL,
  `model_id` int unsigned NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wifi_router_location_id_index` (`location_id`),
  KEY `wifi_router_pdoa_id_index` (`pdoa_id`),
  KEY `wifi_router_owner_id_index` (`owner_id`),
  KEY `wifi_router_model_id_index` (`model_id`),
  CONSTRAINT `wifi_router_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wifi_router_model_id_foreign` FOREIGN KEY (`model_id`) REFERENCES `wifi_router_model` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wifi_router_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wifi_router_pdoa_id_foreign` FOREIGN KEY (`pdoa_id`) REFERENCES `pdoas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wifi_router`
--

LOCK TABLES `wifi_router` WRITE;
/*!40000 ALTER TABLE `wifi_router` DISABLE KEYS */;
INSERT INTO `wifi_router` VALUES (1,'testrouter','AA-BB-CC-11',NULL,'2022-05-24 08:41:34',1,'pulse_1_2022-05-24_08-41-34',3,1,1,NULL,NULL,NULL,NULL),(2,'testrouter2','AA-BB-CC-22',NULL,'2022-05-24 08:41:34',1,'pulse_1_2022-05-24_08-41-34',3,1,1,NULL,NULL,NULL,NULL),(3,'Router1','12-12-12-12-12-12',0,'2022-05-24 15:57:57',NULL,'pulse_1_2022-05-24_08-41-34',11,1,0,'2022-05-24 08:49:48','2022-05-24 15:57:57','dyUk8QjEnMSyuFWUcn2isIybuuQlom',NULL),(4,'Router 2','13-13-13-13-13-13',0,'2022-05-24 15:58:08',NULL,'pulse_1_2022-05-24_08-41-34',7,1,0,'2022-05-24 09:03:28','2022-05-24 15:58:08','CNfsQnobagU3DfZpnwYhoMHxopNvlM',NULL),(5,'Test - 1','AA-99-11-00-11-98',0,'2022-05-24 15:47:45',NULL,'pulsewifi_2022-05-24_09-24-28',14,1,0,'2022-05-24 10:25:27','2022-05-24 15:47:45','UR53ogfWuifvnxwBFybOPMxeYXnr9X',NULL),(6,'Test - 2','AA-99-11-00-11-22',0,'2022-05-24 15:58:08',NULL,'pulsewifi_2022-05-24_09-24-28',7,1,0,'2022-05-24 10:25:49','2022-05-24 15:58:08','YthXbn1yNsQImrymdwwvpmxg89S3ps',NULL),(7,'Router 1B','1a-2b-3c-12-14-12',0,'2022-05-24 15:58:36',NULL,'pulsewifi_2022-05-24_09-24-28',15,1,0,'2022-05-24 14:21:38','2022-05-24 15:58:36','1kUVrzqiTVXOCFbsy0WQBK9fFa7BIX',NULL),(8,'Router 1C','ff-aa-bb-11-22-33',0,'2022-05-24 15:34:48',NULL,'pulsewifi_2022-05-24_09-24-28',NULL,1,0,'2022-05-24 15:34:48','2022-05-24 15:34:48','Fshca1pjeJb7eU9F2sUA2TZA4g2ndq','TwWdFHRSWGb1YFSGM2Am'),(9,'TP Link Test Router','98-1A-B2-56-DE-1D',0,'2022-05-25 09:21:42',NULL,'pulse_1_2022-05-24_08-41-34',NULL,4,0,'2022-05-25 09:21:42','2022-05-25 09:21:42','k8E3KdLWnFCE1mihqumyswnilmPDIn','uwvklIW3BnHkevRkixRs'),(10,'TP Link Test','14-EB-B6-79-2B-22',1,'2022-05-31 08:08:01',6,'pulsewifi_2022-05-24_09-24-28',20,4,0,'2022-05-26 01:16:23','2022-05-31 08:08:01','dNHtEkNrL7RyE1fu0dGsIETNPD2RBi','XkjORTfaezpSvcaMIRAQ'),(11,'Other A6','74-A1-98-91-2A-B7',1,'2022-05-29 18:05:31',6,'pulsewifi_2022-05-24_09-24-28',20,4,0,'2022-05-26 19:14:19','2022-05-29 17:08:06','vO4GWj04mszMlsQb1FcKB92nZrDUic','66XfKQf6r5ipppHUxB5d'),(14,'Archer A6','5C-A6-E6-78-F0-BB',1,'2022-05-31 18:30:00',6,'pulse_1_2022-05-24_08-41-34',NULL,4,0,'2022-05-28 19:28:39','2022-05-31 18:30:00','RFMDSNI0QqSctHfQ6lUKSUO8Oq2IUX','DjSpdLaiFI7PHG4UoH9X'),(15,'Archer A6 Chandigarh','5C-A6-E6-1E-00-1A',0,'2022-05-30 14:53:11',NULL,'pulse_1_2022-05-24_08-41-34',24,4,0,'2022-05-30 14:02:00','2022-05-30 14:53:11','KAQUrLyiE8biuWdVekU25PCMX7OTiH','5mER05XTmK6qR3nmBcXB'),(16,'Archer A6 Chandigarh 2','54-AF-97-B2-A2-78',0,'2022-05-31 04:30:01',10,'pulse_1_2022-05-24_08-41-34',24,4,0,'2022-05-30 14:55:41','2022-05-31 04:30:01','bsSyEWNudJSkg4SeFUZlDE4we7kSfQ','akWgSPNOV94zEMLE1aVt');
/*!40000 ALTER TABLE `wifi_router` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wifi_router_model`
--

DROP TABLE IF EXISTS `wifi_router_model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wifi_router_model` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `price` double(24,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wifi_router_model`
--

LOCK TABLES `wifi_router_model` WRITE;
/*!40000 ALTER TABLE `wifi_router_model` DISABLE KEYS */;
INSERT INTO `wifi_router_model` VALUES (1,'testmodel','testmodel','',1,1000.00,NULL,NULL),(4,'TP-Link Archer A6','The Archer A6 creates a reliable and blazing-fast network powered by 802.11ac Wi-Fi technology.','public/WiFiRouter_img/WiFiRouterModel_4_0.jpg',1,3999.00,'2022-05-25 09:06:06','2022-05-26 20:10:41');
/*!40000 ALTER TABLE `wifi_router_model` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-31 18:30:55
