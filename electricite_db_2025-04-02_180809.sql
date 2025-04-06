-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 06, 2025 at 01:02 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_factures`
--

-- --------------------------------------------------------

--
-- Table structure for table `anomalies_consommation`
--

DROP TABLE IF EXISTS `anomalies_consommation`;
CREATE TABLE IF NOT EXISTS `anomalies_consommation` (
  `anomalie_id` int NOT NULL AUTO_INCREMENT,
  `consommation_id` int NOT NULL,
  `previous_consommation_id` int DEFAULT NULL,
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `compteur_id` int NOT NULL,
  `entry_date` datetime NOT NULL,
  `previous_value` decimal(10,2) DEFAULT NULL,
  `entered_value` decimal(10,2) NOT NULL,
  `difference` decimal(10,2) NOT NULL,
  `status` enum('en_attente','en_traitement','résolue') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`anomalie_id`),
  UNIQUE KEY `consommation_id` (`consommation_id`),
  KEY `previous_consommation_id` (`previous_consommation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `user_id`, `full_name`, `address`, `phone`, `created_at`) VALUES
(1, 4, 'ELKIHAL YOUNESS', '13 RUE SALMA 2 TETOUAN', '+212638742013', '2025-01-28 23:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `compteurs`
--

DROP TABLE IF EXISTS `compteurs`;
CREATE TABLE IF NOT EXISTS `compteurs` (
  `compteur_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `numero_serie` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`compteur_id`),
  UNIQUE KEY `numero_serie` (`numero_serie`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compteurs`
--

INSERT INTO `compteurs` (`compteur_id`, `client_id`, `numero_serie`) VALUES
(1, 1, 'C210298'),
(2, 1, 'C213271');

-- --------------------------------------------------------

--
-- Table structure for table `consommations_annuelles`
--

DROP TABLE IF EXISTS `consommations_annuelles`;
CREATE TABLE IF NOT EXISTS `consommations_annuelles` (
  `consommation_annuelle_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `annee` int NOT NULL,
  `consommation_attendue` decimal(10,2) NOT NULL,
  `consommation_reelle` decimal(10,2) DEFAULT NULL,
  `ecart` decimal(10,2) GENERATED ALWAYS AS ((`consommation_attendue` - `consommation_reelle`)) STORED,
  `statut` enum('en_attente','verifie','corrige') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'en_attente',
  `notes` text COLLATE utf8mb4_general_ci,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consommation_annuelle_id`),
  UNIQUE KEY `client_annee` (`client_id`,`annee`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consommations_mensuelles`
--

DROP TABLE IF EXISTS `consommations_mensuelles`;
CREATE TABLE IF NOT EXISTS `consommations_mensuelles` (
  `consommation_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `compteur_id` int NOT NULL,
  `kw` decimal(10,2) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consommation_id`),
  KEY `client_id` (`client_id`),
  KEY `compteur_id` (`compteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consommations_mensuelles`
--

INSERT INTO `consommations_mensuelles` (`consommation_id`, `client_id`, `compteur_id`, `kw`, `image_path`, `created_at`) VALUES
(7, 1, 1, 1600.00, '../../uploads/meters/meter_1.jpg', '2025-03-01 10:00:00'),
(8, 1, 1, 2000.00, '../../uploads/meters/67f1b87c684e1_firstpage.jpg', '2025-04-05 23:10:52');

-- --------------------------------------------------------

--
-- Table structure for table `pieces_jointes`
--

DROP TABLE IF EXISTS `pieces_jointes`;
CREATE TABLE IF NOT EXISTS `pieces_jointes` (
  `piece_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`piece_id`),
  KEY `reclamation_id` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
CREATE TABLE IF NOT EXISTS `reclamations` (
  `reclamation_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `type` enum('fuite_externe','fuite_interne','facture','autre') COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `statut` enum('en_attente','en_traitement','résolue','refusée') COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_resolution` datetime DEFAULT NULL,
  PRIMARY KEY (`reclamation_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reponses`
--

DROP TABLE IF EXISTS `reponses`;
CREATE TABLE IF NOT EXISTS `reponses` (
  `reponse_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `contenu` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reponse_id`),
  KEY `reclamation_id` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('client','fournisseur','agent') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `created_at`) VALUES
(3, 'kihl@mail.com', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'fournisseur', '2025-03-31 21:52:44'),
(4, 'youns@mail.com', '$2y$10$a5bQeRcigLAMYfKkiUgC1.kDy8EZzynEpYDgalEA/9I.1/2K9XZOy', 'client', '2025-04-01 16:18:51'),
(5, 'nassim@gmail.com', 'test', 'agent', '2025-04-05 16:53:20'),
(6, 'nassime@gmail.com', 'test', 'fournisseur', '2025-04-05 16:53:54'),
(7, 'agent@example.com', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'agent', '2025-04-05 18:15:32');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anomalies_consommation`
--
ALTER TABLE `anomalies_consommation`
  ADD CONSTRAINT `anomalies_consommation_ibfk_1` FOREIGN KEY (`consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`),
  ADD CONSTRAINT `anomalies_consommation_ibfk_2` FOREIGN KEY (`previous_consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `compteurs`
--
ALTER TABLE `compteurs`
  ADD CONSTRAINT `compteurs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `consommations_annuelles`
--
ALTER TABLE `consommations_annuelles`
  ADD CONSTRAINT `consommations_annuelles_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `consommations_mensuelles`
--
ALTER TABLE `consommations_mensuelles`
  ADD CONSTRAINT `consommations_mensuelles_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `consommations_mensuelles_ibfk_2` FOREIGN KEY (`compteur_id`) REFERENCES `compteurs` (`compteur_id`);

--
-- Constraints for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_fk_reclamation` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`) ON DELETE CASCADE;

--
-- Constraints for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `reclamations_fk_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `reponses_fk_reclamation` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
