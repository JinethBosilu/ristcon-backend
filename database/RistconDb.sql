CREATE DATABASE  IF NOT EXISTS `ristcon_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ristcon_db`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: ristcon_db
-- ------------------------------------------------------
-- Server version	9.2.0

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
-- Table structure for table `abstract_formats`
--

DROP TABLE IF EXISTS `abstract_formats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abstract_formats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `format_type` enum('abstract','extended_abstract') COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_title_characters` int DEFAULT NULL,
  `title_font_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_font_size` int DEFAULT NULL,
  `title_style` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_body_words` int DEFAULT NULL,
  `body_font_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_font_size` int DEFAULT NULL,
  `body_line_spacing` decimal(3,1) DEFAULT NULL,
  `max_keywords` int DEFAULT NULL,
  `keywords_font_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords_font_size` int DEFAULT NULL,
  `keywords_style` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_references` int DEFAULT NULL,
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `abstract_formats_conference_id_format_type_index` (`conference_id`,`format_type`),
  KEY `idx_edition_abstractfo` (`edition_id`),
  CONSTRAINT `abstract_formats_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `abstract_formats_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `abstract_formats_chk_1` CHECK (json_valid(`sections`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abstract_formats`
--

LOCK TABLES `abstract_formats` WRITE;
/*!40000 ALTER TABLE `abstract_formats` DISABLE KEYS */;
INSERT INTO `abstract_formats` VALUES (1,1,1,'abstract',100,'Times New Roman',14,'bold',250,'Times New Roman',12,1.5,5,'Times New Roman',11,'italic',NULL,NULL,'Abstracts exceeding the word limit will be returned.',1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'extended_abstract',NULL,NULL,NULL,NULL,NULL,'Times New Roman',12,1.5,NULL,NULL,NULL,NULL,8,'[\"Title\",\"Abstract\",\"Introduction\",\"Materials & Methods\",\"Results & Discussion\",\"Conclusions\",\"References\"]','All sections: Times New Roman 12, line spacing 1.5',2,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `abstract_formats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `author_page_config`
--

DROP TABLE IF EXISTS `author_page_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author_page_config` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `conference_format` enum('in_person','virtual','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cmt_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submission_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blind_review_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `camera_ready_required` tinyint(1) NOT NULL DEFAULT '1',
  `special_instructions` text COLLATE utf8mb4_unicode_ci,
  `acknowledgment_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `author_page_config_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_authorpage` (`edition_id`),
  CONSTRAINT `author_page_config_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `author_page_config_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author_page_config`
--

LOCK TABLES `author_page_config` WRITE;
/*!40000 ALTER TABLE `author_page_config` DISABLE KEYS */;
INSERT INTO `author_page_config` VALUES (1,1,1,'in_person','https://cmt3.research.microsoft.com/User/Login?ReturnUrl=%2FRISTCON2026','ristcon2026@sci.ruh.ac.lk',1,1,'No identifying information should be included in the abstract/extended abstract, its file name (e.g., avoid using names like \'Abstract_James\'), or in the content. File formats: .docx or .doc only. PDF allowed if created by LaTeX/TeX. No acknowledgements in initial submission. Once the abstract is approved, you can include the acknowledgement (if any) in the final submission. Only one figure and one table could be included (if any) in the extended abstract. Reviews will not be accepting.','The Microsoft CMT service was used for managing the peer-reviewing process for this conference. This service was provided for free by Microsoft and they bore all expenses, including costs for Azure cloud services as well as for software development and support.','2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `author_page_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committee_members`
--

DROP TABLE IF EXISTS `committee_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committee_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `committee_type_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_international` tinyint(1) NOT NULL DEFAULT '0',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `committee_members_conference_id_foreign` (`conference_id`),
  KEY `committee_members_committee_type_id_foreign` (`committee_type_id`),
  KEY `idx_edition_committeem` (`edition_id`),
  KEY `idx_edition_committee_type` (`edition_id`,`committee_type_id`),
  CONSTRAINT `committee_members_committee_type_id_foreign` FOREIGN KEY (`committee_type_id`) REFERENCES `committee_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `committee_members_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `committee_members_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committee_members`
--

LOCK TABLES `committee_members` WRITE;
/*!40000 ALTER TABLE `committee_members` DISABLE KEYS */;
INSERT INTO `committee_members` VALUES (1,1,1,1,'Prof. A.M. Jayasekara','Professor','Department of Physics','University of Colombo','Member',NULL,'Sri Lanka',0,NULL,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,1,'Prof. K.A.S. Abeysinghe','Professor','Department of Chemistry','University of Peradeniya','Member',NULL,'Sri Lanka',0,NULL,2,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,1,'Prof. N.D. Bulugahapitiya','Professor','Department of Mathematics','University of Kelaniya','Member',NULL,'Sri Lanka',0,NULL,3,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,1,1,'Prof. Archana Sharma','Professor','School of Physics','University of Delhi, India','Member',NULL,'India',1,NULL,4,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(5,1,1,1,'Dr. Ajith Karunaratne','Senior Lecturer','Department of Computer Science','University of Moratuwa','Member',NULL,'Sri Lanka',0,NULL,5,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(6,1,1,2,'Dr. P.G.D. Jayasinghe','Senior Lecturer','Department of Botany','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(7,1,1,2,'Dr. R.M.K. Ratnayake','Senior Lecturer','Department of Zoology','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,2,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(8,1,1,2,'Dr. W.A.N.M. Wijesundara','Senior Lecturer','Department of Mathematics','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,3,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(9,1,1,2,'Dr. H.M.T.G.A. Pitawala','Senior Lecturer','Department of Geology','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,4,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(10,1,1,2,'Dr. K.H.M. Ashoka Deepananda','Senior Lecturer','Department of Oceanography','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,5,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(11,1,1,2,'Dr. S.P. Kumara','Senior Lecturer','Department of Computer Science','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,6,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(12,1,1,2,'Dr. A.M.P. Anuruddha','Senior Lecturer','Department of Physics','University of Ruhuna','Editor',NULL,'Sri Lanka',0,NULL,7,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(13,1,1,3,'Dr. Y.M.A.L.W. Yapa','Senior Lecturer','Department of Chemistry','University of Ruhuna','Chairperson','leadership','Sri Lanka',0,NULL,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(14,1,1,3,'Dr. H.W.M.A.C. Wijayasinghe','Senior Lecturer','Department of Computer Science','University of Ruhuna','Joint Secretary','leadership','Sri Lanka',0,NULL,2,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(15,1,1,3,'Dr. K.G.S.U. Ariyawansa','Senior Lecturer','Department of Mathematics','University of Ruhuna','Joint Secretary','leadership','Sri Lanka',0,NULL,3,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(16,1,1,3,'Dr. R.P.N.P. Rajapakse','Senior Lecturer','Department of Botany','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,4,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(17,1,1,3,'Dr. W.M.G.I. Wijesinghe','Senior Lecturer','Department of Zoology','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,5,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(18,1,1,3,'Dr. A.A.D. Amarathunga','Senior Lecturer','Department of Physics','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,6,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(19,1,1,3,'Dr. M.H.F. Nawaz','Senior Lecturer','Department of Chemistry','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,7,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(20,1,1,3,'Dr. P.L.K.S. Dharmaratne','Senior Lecturer','Department of Mathematics','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,8,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(21,1,1,3,'Dr. S.M.N. Siriwardana','Lecturer','Department of Computer Science','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,9,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(22,1,1,3,'Dr. H.M.D.P. Herath','Lecturer','Department of Statistics','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,10,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(23,1,1,3,'Dr. R.A.T.M. Rajapaksha','Lecturer','Department of Geography','University of Ruhuna','Member','department_rep','Sri Lanka',0,NULL,11,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `committee_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committee_types`
--

DROP TABLE IF EXISTS `committee_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committee_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `committee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committee_types`
--

LOCK TABLES `committee_types` WRITE;
/*!40000 ALTER TABLE `committee_types` DISABLE KEYS */;
INSERT INTO `committee_types` VALUES (1,'Advisory Board',1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,'Editorial Board',2,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,'Organizing Committee',3,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `committee_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conference_assets`
--

DROP TABLE IF EXISTS `conference_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `asset_type` enum('logo','poster','banner','brochure','image','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage_context` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conference_assets_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_conference` (`edition_id`),
  KEY `idx_edition_asset_type` (`edition_id`,`asset_type`),
  CONSTRAINT `conference_assets_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conference_assets_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_assets`
--

LOCK TABLES `conference_assets` WRITE;
/*!40000 ALTER TABLE `conference_assets` DISABLE KEYS */;
INSERT INTO `conference_assets` VALUES (1,1,1,'logo','ristcon_2026_logo.png','assets/2026/ristcon_2026_logo.png','RISTCON 2026 Logo','main_logo','image/png',234567,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'poster','ristcon_2026_poster.jpg','assets/2026/ristcon_2026_poster.jpg','RISTCON 2026 Conference Poster','main_poster','image/jpeg',567890,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `conference_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conference_documents`
--

DROP TABLE IF EXISTS `conference_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `document_category` enum('abstract_template','author_form','registration_form','presentation_template','camera_ready_template','flyer','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `button_width_percent` int DEFAULT NULL,
  `display_order` int NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conference_documents_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_conference` (`edition_id`),
  KEY `idx_edition_doc_category` (`edition_id`,`document_category`,`is_active`),
  CONSTRAINT `conference_documents_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conference_documents_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_documents`
--

LOCK TABLES `conference_documents` WRITE;
/*!40000 ALTER TABLE `conference_documents` DISABLE KEYS */;
INSERT INTO `conference_documents` VALUES (1,1,1,'abstract_template','Abstract_Template_RISTCON2026.docx','documents/2026/Abstract_Template_RISTCON2026.docx','Download Abstract Template',1,70,1,'application/vnd.openxmlformats-officedocument.wordprocessingml.document',45678,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'author_form','Author_Declaration_Form_RISTCON2026.pdf','documents/2026/Author_Declaration_Form_RISTCON2026.pdf','Author Declaration Form',1,70,2,'application/pdf',123456,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'registration_form','Registration_Form_RISTCON2026.pdf','documents/2026/Registration_Form_RISTCON2026.pdf','Download Registration Form',0,70,3,'application/pdf',0,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,1,'camera_ready_template','Camera_Ready_Template_RISTCON2026.docx','documents/2026/Camera_Ready_Template_RISTCON2026.docx','Template for Camera Ready Submission',1,70,4,'application/vnd.openxmlformats-officedocument.wordprocessingml.document',52341,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(5,1,1,'flyer','RISTCON2026_Flyer.pdf','documents/2026/RISTCON2026_Flyer.pdf','Conference Flyer',1,70,5,'application/pdf',987654,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `conference_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conference_editions`
--

DROP TABLE IF EXISTS `conference_editions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_editions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` year NOT NULL COMMENT 'Conference year (e.g., 2026, 2027)',
  `edition_number` int NOT NULL COMMENT 'Sequential edition count (e.g., 13th, 14th)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Display name (e.g., "RISTCON 2027")',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL-friendly identifier (e.g., "2027", "ristcon-2027")',
  `status` enum('draft','published','archived','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Current lifecycle status',
  `is_active_edition` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Marks the default edition when no year is specified in API',
  `conference_date` date NOT NULL COMMENT 'Main conference date',
  `venue_type` enum('physical','virtual','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'physical' COMMENT 'Conference format',
  `venue_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Physical location if applicable',
  `theme` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conference theme/focus',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Detailed conference description',
  `general_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'General inquiry email',
  `availability_hours` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Support/contact availability hours',
  `copyright_year` year NOT NULL COMMENT 'Copyright year for footer',
  `site_version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0' COMMENT 'Website version identifier',
  `is_legacy_site` tinyint(1) NOT NULL DEFAULT '0',
  `legacy_website_url` text COLLATE utf8mb4_unicode_ci,
  `last_updated` datetime DEFAULT NULL COMMENT 'Custom last update timestamp for frontend display',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conference_editions_year_unique` (`year`),
  UNIQUE KEY `conference_editions_slug_unique` (`slug`),
  KEY `idx_status_active` (`status`,`is_active_edition`),
  KEY `idx_year_status` (`year`,`status`),
  KEY `conference_editions_status_index` (`status`),
  KEY `conference_editions_is_active_edition_index` (`is_active_edition`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conference_editions`
--

LOCK TABLES `conference_editions` WRITE;
/*!40000 ALTER TABLE `conference_editions` DISABLE KEYS */;
INSERT INTO `conference_editions` VALUES (1,2026,13,'RISTCON 2026','2026','published',1,'2026-01-21','physical','University of Ruhuna, Matara, Sri Lanka','Advancing Research Excellence in Science and Technology','The 13th International Research Conference organized by the Faculty of Science, University of Ruhuna, aims to provide a platform for researchers to present their innovative work and foster collaboration across various scientific disciplines.','ristcon@ruh.ac.lk','Available Mon-Fri, 9AM-5PM',2026,'3.0',0,NULL,'2025-12-27 18:32:07','2025-12-27 13:02:07','2025-12-27 13:02:07',NULL),(2,2025,12,'RISTCON 2025','2025','archived',0,'2025-01-15','physical','University of Ruhuna, Matara, Sri Lanka','Innovation in Science and Technology for Sustainable Development','The 12th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2025@ruh.ac.lk','Mon-Fri, 9AM-5PM',2025,'2.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2025','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL),(3,2024,11,'RISTCON 2024','2024','archived',0,'2024-01-20','physical','University of Ruhuna, Matara, Sri Lanka','Research and Innovation for a Better Tomorrow','The 11th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2024@ruh.ac.lk','Mon-Fri, 9AM-5PM',2024,'2.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2024','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL),(4,2023,10,'RISTCON 2023','2023','archived',0,'2023-01-18','hybrid','University of Ruhuna, Matara, Sri Lanka','Science and Technology in the Post-Pandemic Era','The 10th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2023@ruh.ac.lk','Mon-Fri, 9AM-5PM',2023,'2.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2023','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL),(5,2022,9,'RISTCON 2022','2022','archived',0,'2022-01-22','virtual',NULL,'Virtual Collaboration in Scientific Research','The 9th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2022@ruh.ac.lk','Mon-Fri, 9AM-5PM',2022,'1.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2022','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL),(6,2021,8,'RISTCON 2021','2021','archived',0,'2021-01-25','virtual',NULL,'Adapting Research to a Changing World','The 8th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2021@ruh.ac.lk','Mon-Fri, 9AM-5PM',2021,'1.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2021','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL),(7,2020,7,'RISTCON 2020','2020','archived',0,'2020-01-20','physical','University of Ruhuna, Matara, Sri Lanka','Science for Sustainable Development','The 7th International Research Conference organized by the Faculty of Science, University of Ruhuna.','ristcon2020@ruh.ac.lk','Mon-Fri, 9AM-5PM',2020,'1.0',1,'https://www.sci.ruh.ac.lk/conference/ristcon2020','2026-01-15 15:46:53','2026-01-15 10:16:53','2026-01-15 10:16:53',NULL);
/*!40000 ALTER TABLE `conference_editions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conferences`
--

DROP TABLE IF EXISTS `conferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` year NOT NULL,
  `edition_number` int NOT NULL,
  `conference_date` date NOT NULL,
  `venue_type` enum('physical','virtual','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'physical',
  `venue_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('upcoming','ongoing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `general_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability_hours` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Contact availability hours (e.g., "Available Mon-Fri, 9AM-5PM")',
  `last_updated` datetime DEFAULT NULL,
  `copyright_year` year NOT NULL,
  `site_version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conferences`
--

LOCK TABLES `conferences` WRITE;
/*!40000 ALTER TABLE `conferences` DISABLE KEYS */;
INSERT INTO `conferences` VALUES (1,2026,13,'2026-01-21','physical','University of Ruhuna, Matara, Sri Lanka','Advancing Research Excellence in Science and Technology','The 13th International Research Conference organized by the Faculty of Science, University of Ruhuna, aims to provide a platform for researchers to present their innovative work and foster collaboration across various scientific disciplines.','upcoming','ristcon@ruh.ac.lk','Available Mon-Fri, 9AM-5PM','2025-12-27 18:32:07',2026,'3.0','2025-12-27 13:02:07','2025-12-27 13:02:07',NULL);
/*!40000 ALTER TABLE `conferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_persons`
--

DROP TABLE IF EXISTS `contact_persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_persons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_persons_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_contactper` (`edition_id`),
  CONSTRAINT `contact_persons_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contact_persons_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_persons`
--

LOCK TABLES `contact_persons` WRITE;
/*!40000 ALTER TABLE `contact_persons` DISABLE KEYS */;
INSERT INTO `contact_persons` VALUES (1,1,1,'Dr. Y.M.A.L.W. Yapa','Chairperson','Department of Chemistry','+94 71 234 5678','+94 41 222 7000','yapa@che.ruh.ac.lk','Department of Chemistry, Faculty of Science, University of Ruhuna, Matara, Sri Lanka',1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'Dr. H.W.M.A.C. Wijayasinghe','Joint Secretary','Department of Computer Science','+94 77 345 6789','+94 41 222 7001','wijayasinghe@dcs.ruh.ac.lk','Department of Computer Science, Faculty of Science, University of Ruhuna, Matara, Sri Lanka',2,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `contact_persons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_locations`
--

DROP TABLE IF EXISTS `event_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `venue_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `google_maps_embed_url` text COLLATE utf8mb4_unicode_ci,
  `google_maps_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_virtual` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_locations_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_eventlocat` (`edition_id`),
  CONSTRAINT `event_locations_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_locations_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_locations`
--

LOCK TABLES `event_locations` WRITE;
/*!40000 ALTER TABLE `event_locations` DISABLE KEYS */;
INSERT INTO `event_locations` VALUES (1,1,1,'University of Ruhuna','Faculty of Science, University of Ruhuna, Matara, Sri Lanka','Matara','Sri Lanka',5.93971600,80.57613400,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.2342826543917!2d80.57376497587636!3d5.939716094273582!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae138d151937cd9%3A0x1e1e6f137d1a0d7e!2sFaculty%20of%20Science%2C%20University%20of%20Ruhuna!5e0!3m2!1sen!2slk!4v1703245600000!5m2!1sen!2slk','https://goo.gl/maps/xyz123abc',0,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `event_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `important_dates`
--

DROP TABLE IF EXISTS `important_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `important_dates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `date_type` enum('submission_deadline','notification','camera_ready','conference_date','registration_deadline','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_value` date NOT NULL,
  `is_extended` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL,
  `display_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `important_dates_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_importantd` (`edition_id`),
  KEY `idx_edition_date_type` (`edition_id`,`date_type`),
  CONSTRAINT `important_dates_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `important_dates_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `important_dates`
--

LOCK TABLES `important_dates` WRITE;
/*!40000 ALTER TABLE `important_dates` DISABLE KEYS */;
INSERT INTO `important_dates` VALUES (1,1,1,'submission_deadline','2025-10-15',0,1,'Abstract Submission Deadline','Submit via Microsoft CMT','2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'notification','2025-11-15',0,2,'Notification of Acceptance',NULL,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'camera_ready','2025-12-15',0,3,'Camera-Ready Submission',NULL,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,1,'conference_date','2026-01-21',0,4,'Conference Date',NULL,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(5,1,1,'registration_deadline','2026-01-12',0,5,'Registration Deadline','All attendees, including presenters, must complete their registration by submitting the Registration Form. Both the form and payment receipt must be uploaded to the CMT system.','2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `important_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_11_18_181440_create_conferences_table',1),(2,'2025_11_18_181440_create_important_dates_table',1),(3,'2025_11_18_181440_create_speakers_table',1),(4,'2025_11_18_181441_create_committee_types_table',1),(5,'2025_11_18_181455_create_committee_members_table',1),(6,'2025_11_18_181455_create_conference_documents_table',1),(7,'2025_11_18_181455_create_contact_persons_table',1),(8,'2025_11_18_181456_create_conference_assets_table',1),(9,'2025_11_18_181527_create_research_categories_table',1),(10,'2025_11_18_181528_create_author_page_config_table',1),(11,'2025_11_18_181528_create_event_locations_table',1),(12,'2025_11_18_181528_create_research_areas_table',1),(13,'2025_11_18_181529_create_presentation_guidelines_table',1),(14,'2025_11_18_181529_create_submission_methods_table',1),(15,'2025_11_18__01_01_000000_create_users_table',1),(16,'2025_11_18__01_01_000001_create_cache_table',1),(17,'2025_11_18__01_01_000002_create_jobs_table',1),(18,'2025_11_22_200015_create_payment_information_table',1),(19,'2025_11_22_200025_create_registration_fees_table',1),(20,'2025_11_22_200034_create_payment_policies_table',1),(21,'2025_11_22_205026_add_flyer_to_document_category_enum',1),(22,'2025_11_23_000001_create_social_media_links_table',1),(23,'2025_11_23_000002_add_availability_info_to_conferences',1),(24,'2025_12_22_000001_create_abstract_formats_table',1),(25,'2025_12_22_103542_rename_is_available_to_is_active_in_conference_documents_table',1),(26,'2025_12_27_191628_create_personal_access_tokens_table',2),(27,'2026_01_11_055814_create_system_settings_table',3),(28,'2026_01_11_135551_create_conference_editions_table',4),(29,'2026_01_11_135552_add_edition_id_to_scoped_tables',5),(30,'2026_01_11_135553_add_edition_foreign_keys_and_indexes',6),(31,'2026_01_11_110004_add_photo_path_to_committee_members_table',7),(32,'2026_01_15_145607_add_legacy_site_fields_to_conference_editions_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_information`
--

DROP TABLE IF EXISTS `payment_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_information` (
  `payment_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `payment_type` enum('local','foreign') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of payment: local or foreign',
  `beneficiary_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `swift_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_address` text COLLATE utf8mb4_unicode_ci,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_info` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `payment_information_conference_id_payment_type_index` (`conference_id`,`payment_type`),
  KEY `idx_edition_paymentinf` (`edition_id`),
  KEY `idx_edition_payment_type` (`edition_id`,`payment_type`),
  CONSTRAINT `payment_information_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_information_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_information`
--

LOCK TABLES `payment_information` WRITE;
/*!40000 ALTER TABLE `payment_information` DISABLE KEYS */;
INSERT INTO `payment_information` VALUES (1,1,1,'local','University of Ruhuna','Peoples Bank','032-1-001-1-2477589',NULL,NULL,'Uyanwatta Road, Matara','University of Ruhuna, Matara, Sri Lanka','LKR',NULL,1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'foreign','University of Ruhuna','Peoples Bank','032-1-001-1-2477589','PSBKLKLX',NULL,'Uyanwatta Road, Matara',NULL,'USD',NULL,2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `payment_information` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_policies`
--

DROP TABLE IF EXISTS `payment_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_policies` (
  `policy_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `policy_text` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Policy text/description',
  `policy_type` enum('requirement','restriction','note') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'note',
  `display_order` int NOT NULL DEFAULT '0',
  `is_highlighted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Highlight important policies',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `payment_policies_conference_id_policy_type_index` (`conference_id`,`policy_type`),
  KEY `idx_edition_paymentpol` (`edition_id`),
  KEY `idx_edition_policy_type` (`edition_id`,`policy_type`),
  CONSTRAINT `payment_policies_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_policies_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_policies`
--

LOCK TABLES `payment_policies` WRITE;
/*!40000 ALTER TABLE `payment_policies` DISABLE KEYS */;
INSERT INTO `payment_policies` VALUES (1,1,1,'Presenters who make payments outside Sri Lanka should pay in USD.','requirement',1,0,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'All bank charges should be borne by the presenters making the payment.','requirement',2,0,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'Registration fees are non-refundable.','restriction',3,1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `payment_policies` ENABLE KEYS */;
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
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (3,'App\\Models\\User',2,'admin-token','14c34e3d832cda0b3870e9f799f9c2e37c26ba43fe8c1ed3ae58d4520c141b0e','[\"*\"]','2025-12-28 04:02:24',NULL,'2025-12-28 03:52:16','2025-12-28 04:02:24'),(4,'App\\Models\\User',3,'admin-token','ce76c178ae68dbb46caff4f6a718677e20714817799b06abb66ee6788c600c5f','[\"*\"]','2026-01-12 08:57:06',NULL,'2026-01-12 08:56:53','2026-01-12 08:57:06'),(5,'App\\Models\\User',3,'admin-token','b6053a53b450c93f1f515d25390cfd963a70cbfc210d545058effe9bb123b0f5','[\"*\"]',NULL,NULL,'2026-01-15 10:09:40','2026-01-15 10:09:40');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentation_guidelines`
--

DROP TABLE IF EXISTS `presentation_guidelines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presentation_guidelines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `presentation_type` enum('oral','poster','workshop','panel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration_minutes` int DEFAULT NULL,
  `presentation_minutes` int DEFAULT NULL,
  `qa_minutes` int DEFAULT NULL,
  `poster_width` decimal(8,2) DEFAULT NULL,
  `poster_height` decimal(8,2) DEFAULT NULL,
  `poster_unit` enum('inches','cm','mm') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_orientation` enum('portrait','landscape') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_presence_required` tinyint(1) NOT NULL DEFAULT '1',
  `detailed_requirements` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `presentation_guidelines_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_presentati` (`edition_id`),
  CONSTRAINT `presentation_guidelines_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presentation_guidelines_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentation_guidelines`
--

LOCK TABLES `presentation_guidelines` WRITE;
/*!40000 ALTER TABLE `presentation_guidelines` DISABLE KEYS */;
INSERT INTO `presentation_guidelines` VALUES (1,1,1,'oral',15,10,5,NULL,NULL,NULL,NULL,1,'Oral presentations are limited to 15 minutes total (10 minutes presentation + 5 minutes Q&A). Presenters must bring their own laptops. Projector and audio system will be provided.','2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'poster',NULL,NULL,NULL,27.00,40.00,'inches','portrait',1,'Size: 27\" × 40\" (Portrait). Digitally printed. Reference number displayed at top-left. Include title, author(s), and affiliation(s) as per accepted abstract. Sections: Abstract, Introduction, Methodology, Results, Discussion/Conclusion, References. Text must be legible from 1–1.5 meters. Use enlarged figures, graphs, or photos; minimize tables. Each visual must have a descriptive title. Design should be self-explanatory for viewers.','2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `presentation_guidelines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registration_fees`
--

DROP TABLE IF EXISTS `registration_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_fees` (
  `fee_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `attendee_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of attendee: Foreign, Local, Student, etc.',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Currency code: USD, LKR, etc.',
  `amount` decimal(10,2) NOT NULL COMMENT 'Registration fee amount',
  `early_bird_amount` decimal(10,2) DEFAULT NULL COMMENT 'Early bird discount amount',
  `early_bird_deadline` date DEFAULT NULL COMMENT 'Early bird registration deadline',
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`fee_id`),
  KEY `registration_fees_conference_id_attendee_type_index` (`conference_id`,`attendee_type`),
  KEY `idx_edition_registrati` (`edition_id`),
  KEY `idx_edition_attendee_type` (`edition_id`,`attendee_type`),
  CONSTRAINT `registration_fees_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `registration_fees_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registration_fees`
--

LOCK TABLES `registration_fees` WRITE;
/*!40000 ALTER TABLE `registration_fees` DISABLE KEYS */;
INSERT INTO `registration_fees` VALUES (1,1,1,'Foreign Attendees','USD',50.00,NULL,NULL,1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'Local Attendees','LKR',2500.00,NULL,NULL,2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `registration_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `research_areas`
--

DROP TABLE IF EXISTS `research_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `research_areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `area_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `display_order` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `research_areas_category_id_foreign` (`category_id`),
  CONSTRAINT `research_areas_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `research_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `research_areas_chk_1` CHECK (json_valid(`alternate_names`))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `research_areas`
--

LOCK TABLES `research_areas` WRITE;
/*!40000 ALTER TABLE `research_areas` DISABLE KEYS */;
INSERT INTO `research_areas` VALUES (1,1,'Biochemistry','[\"Clinical Biochemistry\"]',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,'Botany','[\"Plant Biology\"]',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,'Microbiology','[]',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,'Molecular Biology','[]',4,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(5,1,'Zoology','[\"Animal Science\"]',5,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(6,1,'Environmental Biology','[]',6,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(7,2,'Chemistry','[\"Organic Chemistry\",\"Inorganic Chemistry\"]',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(8,2,'Physics','[\"Applied Physics\"]',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(9,2,'Material Science','[]',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(10,3,'Mathematics','[\"Pure Mathematics\",\"Applied Mathematics\"]',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(11,3,'Statistics','[\"Biostatistics\"]',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(12,3,'Operations Research','[]',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(13,4,'Artificial Intelligence','[\"Machine Learning\",\"Deep Learning\"]',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(14,4,'Software Engineering','[]',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(15,4,'Data Science','[\"Big Data Analytics\"]',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(16,4,'Cyber Security','[\"Information Security\"]',4,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(17,5,'Geography','[\"Human Geography\",\"Physical Geography\"]',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(18,5,'Economics','[]',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(19,5,'Social Studies','[]',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `research_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `research_categories`
--

DROP TABLE IF EXISTS `research_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `research_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `category_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `research_categories_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_researchca` (`edition_id`),
  KEY `idx_edition_category_code` (`edition_id`,`category_code`),
  CONSTRAINT `research_categories_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `research_categories_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `research_categories`
--

LOCK TABLES `research_categories` WRITE;
/*!40000 ALTER TABLE `research_categories` DISABLE KEYS */;
INSERT INTO `research_categories` VALUES (1,1,1,'A','Life Sciences','Biological and health sciences research',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'B','Physical and Chemical Sciences','Physics, Chemistry, and related disciplines',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'C','Mathematical and Statistical Sciences','Mathematics, Statistics, and Computational Sciences',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,1,'D','Computer Science and Information Technology','Computing, IT, and Digital Technologies',4,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(5,1,1,'E','Social Sciences and Humanities','Geography, Economics, and Social Studies',5,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `research_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_media_links`
--

DROP TABLE IF EXISTS `social_media_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_media_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `platform` enum('facebook','twitter','linkedin','instagram','youtube','email') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Social media platform',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL to the social media page or mailto link',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Display label for accessibility',
  `display_order` int NOT NULL DEFAULT '0' COMMENT 'Order of display',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether link should be shown',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_media_links_conference_id_is_active_display_order_index` (`conference_id`,`is_active`,`display_order`),
  KEY `idx_edition_socialmedi` (`edition_id`),
  KEY `idx_edition_social_active` (`edition_id`,`is_active`,`display_order`),
  CONSTRAINT `social_media_links_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `social_media_links_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_media_links`
--

LOCK TABLES `social_media_links` WRITE;
/*!40000 ALTER TABLE `social_media_links` DISABLE KEYS */;
INSERT INTO `social_media_links` VALUES (1,1,1,'facebook','https://www.facebook.com/ristcon','Facebook',1,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'twitter','https://www.twitter.com/ristcon','Twitter',2,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'linkedin','https://www.linkedin.com/company/ristcon','LinkedIn',3,1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(4,1,1,'email','mailto:info@ristcon2026.lk','Email',4,1,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `social_media_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `speakers`
--

DROP TABLE IF EXISTS `speakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `speakers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `speaker_type` enum('keynote','plenary','invited') COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_affiliation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `photo_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `speakers_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_speakers` (`edition_id`),
  KEY `idx_edition_speaker_type` (`edition_id`,`speaker_type`),
  CONSTRAINT `speakers_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `speakers_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `speakers`
--

LOCK TABLES `speakers` WRITE;
/*!40000 ALTER TABLE `speakers` DISABLE KEYS */;
INSERT INTO `speakers` VALUES (1,1,1,'keynote',1,'Prof. Michael Anderson','PhD, FIEEE','Department of Computer Science, Stanford University, USA','Visiting Professor, University of Cambridge','Prof. Michael Anderson is a leading expert in artificial intelligence and machine learning with over 25 years of research experience. He has published more than 200 papers in top-tier conferences and journals.','prof_anderson.jpg','https://stanford.edu/~anderson','m.anderson@stanford.edu','2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'plenary',1,'Dr. Sarah Chen','PhD, FRS','Institute of Biotechnology, National University of Singapore',NULL,'Dr. Sarah Chen specializes in molecular biology and genetic engineering. Her groundbreaking work on CRISPR applications has earned international recognition.','dr_chen.jpg','https://nus.edu.sg/~chen','s.chen@nus.edu.sg','2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'plenary',2,'Prof. Rajesh Kumar','PhD, FRSC','Department of Physics, Indian Institute of Technology Delhi, India',NULL,'Prof. Rajesh Kumar is renowned for his contributions to quantum physics and nanotechnology. He has received numerous awards including the Shanti Swarup Bhatnagar Prize.','prof_kumar.jpg','https://iitd.ac.in/~kumar','r.kumar@iitd.ac.in','2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `speakers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_methods`
--

DROP TABLE IF EXISTS `submission_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submission_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `edition_id` bigint unsigned NOT NULL,
  `conference_id` bigint unsigned NOT NULL,
  `document_type` enum('author_info','abstract','extended_abstract','camera_ready','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `submission_method` enum('email','cmt_upload','online_form','postal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `display_order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submission_methods_conference_id_foreign` (`conference_id`),
  KEY `idx_edition_submission` (`edition_id`),
  CONSTRAINT `submission_methods_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submission_methods_edition_id_foreign` FOREIGN KEY (`edition_id`) REFERENCES `conference_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_methods`
--

LOCK TABLES `submission_methods` WRITE;
/*!40000 ALTER TABLE `submission_methods` DISABLE KEYS */;
INSERT INTO `submission_methods` VALUES (1,1,1,'author_info','email','ristcon2026@sci.ruh.ac.lk','Email the Author Information Form to ristcon2026@sci.ruh.ac.lk',1,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(2,1,1,'abstract','cmt_upload',NULL,'Upload abstract via Microsoft CMT system',2,'2025-12-27 13:02:07','2025-12-27 13:02:07'),(3,1,1,'extended_abstract','cmt_upload',NULL,'Upload camera-ready extended abstract via CMT',3,'2025-12-27 13:02:07','2025-12-27 13:02:07');
/*!40000 ALTER TABLE `submission_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `category` enum('general','email','database','security','notifications') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `type` enum('string','integer','boolean','json','text') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_readonly` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`),
  KEY `system_settings_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'site_name','RISTCON Admin','general','string','Site Name','The name of the admin panel',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(2,'default_conference_year','2026','general','integer','Default Conference Year','The default year to use when loading conference data',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(3,'timezone','Asia/Colombo','general','string','Timezone','Default timezone for displaying dates',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(4,'date_format','YYYY-MM-DD','general','string','Date Format','Format for displaying dates',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(5,'smtp_host','','email','string','SMTP Host','SMTP server hostname',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(6,'smtp_port','587','email','integer','SMTP Port','SMTP server port',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(7,'smtp_username','','email','string','SMTP Username','SMTP authentication username',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(8,'smtp_encryption','tls','email','string','SMTP Encryption','SMTP encryption method (none, ssl, tls)',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(9,'from_email','noreply@ristcon.ruh.ac.lk','email','string','From Email','Default sender email address',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(10,'from_name','RISTCON Admin','email','string','From Name','Default sender name',0,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(11,'db_connection','mysql','database','string','Database Connection','Current database connection type',1,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(12,'db_host','localhost','database','string','Database Host','Database server hostname',1,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(13,'db_name','ristcon','database','string','Database Name','Database name',1,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(14,'session_lifetime','120','security','integer','Session Lifetime','Session lifetime in minutes',1,'2026-01-11 00:33:25','2026-01-11 00:33:25'),(15,'token_expiration','1440','security','integer','Token Expiration','API token expiration in minutes',1,'2026-01-11 00:33:25','2026-01-11 00:33:25');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'RISTCON Admin','admin@ristcon.ruh.ac.lk','2025-12-27 13:02:07','$2y$12$sq5Dl1mWLJsSX7YlllF1ouD38pbjQoSo.HbmjGsmPj1KmiIGxWvUu','wix6WxeQiK','2025-12-27 13:02:08','2025-12-27 13:02:08'),(2,'RISTCON Admin','admin@ristcon.org','2025-12-27 13:34:36','$2y$12$.dgJIWjc9ShpN3dtuLNz9Ozoh6amnNp2bBWchTn0n.g9MFTJ6piPa',NULL,'2025-12-27 13:34:36','2025-12-27 13:34:36'),(3,'Admin','admin@ristcon.lk',NULL,'$2y$12$8tWUR1tDyCNgRcDZptN4GeJzqFemCts/RCJFPsV3yR0TjLglTNUX6',NULL,'2026-01-12 08:56:28','2026-01-12 08:56:28');
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

-- Dump completed on 2026-01-15 21:23:18
