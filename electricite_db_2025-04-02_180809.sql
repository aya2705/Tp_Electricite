-- MySQL dump 10.13  Distrib 8.0.23, for macos10.15 (x86_64)
--
-- Host: 127.0.0.1    Database: electricite_db
-- ------------------------------------------------------
-- Server version	8.0.23

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
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,4,'ELKIHAL YOUNESS','13 RUE SALMA 2 TETOUAN','+212638742013','2025-01-28 23:21:14');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;

--
-- Table structure for table `compteurs`
--

DROP TABLE IF EXISTS `compteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compteurs` (
  `compteur_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `numero_serie` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`compteur_id`),
  UNIQUE KEY `numero_serie` (`numero_serie`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `compteurs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compteurs`
--

/*!40000 ALTER TABLE `compteurs` DISABLE KEYS */;
INSERT INTO `compteurs` VALUES (1,1,'C210298'),(2,1,'C213271');
/*!40000 ALTER TABLE `compteurs` ENABLE KEYS */;

--
-- Table structure for table `consommations_mensuelles`
--

DROP TABLE IF EXISTS `consommations_mensuelles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consommations_mensuelles` (
  `consommation_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `compteur_id` int NOT NULL,
  `kw` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `est_anormale` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`consommation_id`),
  KEY `client_id` (`client_id`),
  KEY `compteur_id` (`compteur_id`),
  CONSTRAINT `consommations_mensuelles_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  CONSTRAINT `consommations_mensuelles_ibfk_2` FOREIGN KEY (`compteur_id`) REFERENCES `compteurs` (`compteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consommations_mensuelles`
--

/*!40000 ALTER TABLE `consommations_mensuelles` DISABLE KEYS */;
INSERT INTO `consommations_mensuelles` VALUES (7,1,1,1588.00,'/uploads/meters/meter_1.jpg','2025-03-01 10:00:00',0),(8,1,1,16347.00,'/uploads/meters/meter_2.jpg','2025-03-15 10:00:00',0),(9,1,2,47073.00,'/uploads/meters/meter_4.jpg','2025-02-01 10:00:00',1);
/*!40000 ALTER TABLE `consommations_mensuelles` ENABLE KEYS */;

--
-- Table structure for table `factures`
--

DROP TABLE IF EXISTS `factures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factures` (
  `facture_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `periode` date NOT NULL,
  `consommation` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut_paiement` enum('payée','impayée') COLLATE utf8mb4_general_ci DEFAULT 'impayée',
  `date_emission` date NOT NULL,
  `tarif_id` int NOT NULL,
  PRIMARY KEY (`facture_id`),
  KEY `client_id` (`client_id`),
  KEY `idx_factures_statut` (`statut_paiement`),
  KEY `tarif_id` (`tarif_id`),
  CONSTRAINT `factures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  CONSTRAINT `factures_ibfk_2` FOREIGN KEY (`tarif_id`) REFERENCES `tarifs` (`tarif_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factures`
--

/*!40000 ALTER TABLE `factures` DISABLE KEYS */;
/*!40000 ALTER TABLE `factures` ENABLE KEYS */;

--
-- Table structure for table `journal_activites`
--

DROP TABLE IF EXISTS `journal_activites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_activites` (
  `activite_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type_action` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activite_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `journal_activites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_activites`
--

/*!40000 ALTER TABLE `journal_activites` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_activites` ENABLE KEYS */;

--
-- Table structure for table `periodes_saisie`
--

DROP TABLE IF EXISTS `periodes_saisie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodes_saisie` (
  `periode_id` int NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  PRIMARY KEY (`periode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodes_saisie`
--

/*!40000 ALTER TABLE `periodes_saisie` DISABLE KEYS */;
/*!40000 ALTER TABLE `periodes_saisie` ENABLE KEYS */;

--
-- Table structure for table `pieces_jointes`
--

DROP TABLE IF EXISTS `pieces_jointes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pieces_jointes` (
  `piece_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`piece_id`),
  KEY `reclamation_id` (`reclamation_id`),
  CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pieces_jointes`
--

/*!40000 ALTER TABLE `pieces_jointes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pieces_jointes` ENABLE KEYS */;

--
-- Table structure for table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamations` (
  `reclamation_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `type` enum('fuite_externe','fuite_interne','facture','autre') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `statut` enum('en_attente','en_traitement','résolue') COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_resolution` datetime DEFAULT NULL,
  PRIMARY KEY (`reclamation_id`),
  KEY `client_id` (`client_id`),
  KEY `idx_reclamations_statut` (`statut`),
  CONSTRAINT `reclamations_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamations`
--

/*!40000 ALTER TABLE `reclamations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reclamations` ENABLE KEYS */;

--
-- Table structure for table `releves`
--

DROP TABLE IF EXISTS `releves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `releves` (
  `releve_id` int NOT NULL AUTO_INCREMENT,
  `compteur_id` int NOT NULL,
  `valeur_precedente` int NOT NULL,
  `valeur_actuelle` int NOT NULL,
  `date_releve` date NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` enum('soumis','validé') COLLATE utf8mb4_general_ci DEFAULT 'soumis',
  PRIMARY KEY (`releve_id`),
  KEY `compteur_id` (`compteur_id`),
  KEY `idx_releves_statut` (`statut`),
  CONSTRAINT `releves_ibfk_1` FOREIGN KEY (`compteur_id`) REFERENCES `compteurs` (`compteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `releves`
--

/*!40000 ALTER TABLE `releves` DISABLE KEYS */;
/*!40000 ALTER TABLE `releves` ENABLE KEYS */;

--
-- Table structure for table `reponses`
--

DROP TABLE IF EXISTS `reponses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reponses` (
  `reponse_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `contenu` text COLLATE utf8mb4_general_ci NOT NULL,
  `template_utilise` int DEFAULT NULL,
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reponse_id`),
  UNIQUE KEY `reclamation_id` (`reclamation_id`),
  CONSTRAINT `reponses_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reponses`
--

/*!40000 ALTER TABLE `reponses` DISABLE KEYS */;
/*!40000 ALTER TABLE `reponses` ENABLE KEYS */;

--
-- Table structure for table `tarifs`
--

DROP TABLE IF EXISTS `tarifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarifs` (
  `tarif_id` int NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `tranche1_max` int NOT NULL,
  `tranche1_prix` decimal(5,2) NOT NULL,
  `tranche2_max` int DEFAULT NULL,
  `tranche2_prix` decimal(5,2) NOT NULL,
  `tranche3_prix` decimal(5,2) NOT NULL,
  `tva` decimal(4,2) NOT NULL,
  PRIMARY KEY (`tarif_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarifs`
--

/*!40000 ALTER TABLE `tarifs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tarifs` ENABLE KEYS */;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `templates` (
  `template_id` int NOT NULL AUTO_INCREMENT,
  `categorie` enum('fuite_externe','fuite_interne','facture','autre') COLLATE utf8mb4_general_ci NOT NULL,
  `nom_template` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('client','fournisseur') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'kihl@mail.com','$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6','fournisseur','2025-03-31 21:52:44'),(4,'youns@mail.com','$2y$10$a5bQeRcigLAMYfKkiUgC1.kDy8EZzynEpYDgalEA/9I.1/2K9XZOy','client','2025-04-01 16:18:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

--
-- Dumping routines for database 'electricite_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-02 18:08:18
