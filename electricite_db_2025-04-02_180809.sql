-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 08, 2025 at 04:20 PM
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
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`anomalie_id`),
  UNIQUE KEY `consommation_id` (`consommation_id`),
  KEY `previous_consommation_id` (`previous_consommation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anomalies_consommation`
--

INSERT INTO `anomalies_consommation` (`anomalie_id`, `consommation_id`, `previous_consommation_id`, `client_name`, `compteur_id`, `entry_date`, `previous_value`, `entered_value`, `difference`, `status`, `created_at`, `image_path`) VALUES
(3, 18, 17, 'ELKIHAL YOUNESS', 1, '2025-04-08 02:54:16', 3000.00, 100.00, 2900.00, 'en_attente', '2025-04-08 02:54:16', '../../uploads/meters/67f481c80a3f3_Capture d\'écran 2024-12-22 162415.png');

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
  `statut` enum('en_attente','verifie','corrige') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'en_attente',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consommation_annuelle_id`),
  UNIQUE KEY `client_annee` (`client_id`,`annee`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consommations_annuelles`
--

INSERT INTO `consommations_annuelles` (`consommation_annuelle_id`, `client_id`, `annee`, `consommation_attendue`, `consommation_reelle`, `statut`, `notes`, `date_creation`) VALUES
(6, 1, 2025, 9000.00, 12810.00, 'en_attente', NULL, '2025-04-08 03:32:55');

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
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consommation_id`),
  KEY `client_id` (`client_id`),
  KEY `compteur_id` (`compteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consommations_mensuelles`
--

INSERT INTO `consommations_mensuelles` (`consommation_id`, `client_id`, `compteur_id`, `kw`, `image_path`, `created_at`) VALUES
(11, 1, 1, 1600.00, '../../uploads/meters/67f4816c9d531_1694014608586.jpg', '2025-04-08 02:52:44'),
(12, 1, 1, 210.00, '../../uploads/meters/67f4817fee892_1694014608586.jpg', '2025-04-08 02:53:03'),
(13, 1, 1, 1700.00, '../../uploads/meters/67f48198683fe_Capture d\'écran 2024-12-22 162415.png', '2025-04-08 02:53:28'),
(14, 1, 1, 1800.00, '../../uploads/meters/67f481a79a7ef_FB_IMG_1725566667130[1].jpg', '2025-04-08 02:53:43'),
(15, 1, 1, 2000.00, '../../uploads/meters/67f481ae76b64_Capture d\'écran 2024-12-22 162415.png', '2025-04-08 02:53:50'),
(16, 1, 1, 2400.00, '../../uploads/meters/67f481b4cca62_FB_IMG_1725566667130[1].jpg', '2025-04-08 02:53:56'),
(17, 1, 1, 3000.00, '../../uploads/meters/67f481bc135be_Capture d\'écran 2024-12-22 162415.png', '2025-04-08 02:54:04'),
(18, 1, 1, 100.00, '../../uploads/meters/67f481c80a3f3_Capture d\'écran 2024-12-22 162415.png', '2025-04-08 02:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `factures_annuelles`
--

DROP TABLE IF EXISTS `factures_annuelles`;
CREATE TABLE IF NOT EXISTS `factures_annuelles` (
  `facture_annuelle_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `consommation_annuelle_id` int NOT NULL,
  `annee` int NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `consommation_totale` decimal(10,2) NOT NULL,
  `date_emission` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('emise','payee','annulee') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'emise',
  `type` enum('credit','debit') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'debit',
  PRIMARY KEY (`facture_annuelle_id`),
  UNIQUE KEY `client_annee` (`client_id`,`annee`),
  KEY `consommation_annuelle_id` (`consommation_annuelle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factures_annuelles`
--

INSERT INTO `factures_annuelles` (`facture_annuelle_id`, `client_id`, `consommation_annuelle_id`, `annee`, `montant_total`, `consommation_totale`, `date_emission`, `statut`, `type`) VALUES
(3, 1, 6, 2025, 3667.44, 12800.00, '2025-04-08 03:33:54', 'emise', 'debit');

-- --------------------------------------------------------

--
-- Table structure for table `factures_mensuelle`
--

DROP TABLE IF EXISTS `factures_mensuelle`;
CREATE TABLE IF NOT EXISTS `factures_mensuelle` (
  `facture_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `consommation_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `consommation` decimal(10,2) NOT NULL,
  `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_emission` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`facture_id`),
  KEY `client_id` (`client_id`),
  KEY `consommation_id` (`consommation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factures_mensuelle`
--

INSERT INTO `factures_mensuelle` (`facture_id`, `client_id`, `consommation_id`, `montant`, `consommation`, `client_name`, `date_emission`) VALUES
(3, 1, 11, 2033.14, 1600.00, 'ELKIHAL YOUNESS', '2025-04-08 02:52:44'),
(4, 1, 13, 96.76, 100.00, 'ELKIHAL YOUNESS', '2025-04-08 02:53:28'),
(5, 1, 14, 96.76, 100.00, 'ELKIHAL YOUNESS', '2025-04-08 02:53:43'),
(6, 1, 15, 215.94, 200.00, 'ELKIHAL YOUNESS', '2025-04-08 02:53:50'),
(7, 1, 16, 475.54, 400.00, 'ELKIHAL YOUNESS', '2025-04-08 02:53:56'),
(8, 1, 17, 735.14, 600.00, 'ELKIHAL YOUNESS', '2025-04-08 02:54:04'),
(9, 1, 12, -2699.60, -2790.00, 'ELKIHAL YOUNESS', '2025-04-08 16:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `type` enum('saisie_consommation','facture','reclamation','autre') COLLATE utf8mb4_general_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('non_lue','lue') COLLATE utf8mb4_general_ci DEFAULT 'non_lue',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `fk_notification_client` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `client_id`, `type`, `reference`, `content`, `status`, `created_at`) VALUES
(1, 1, 'facture', NULL, 'Votre facture a été générée avec succès. Veuillez la consulter dans votre espace client.', 'non_lue', '2025-04-08 16:20:20'),
(2, 1, 'saisie_consommation', NULL, 'La période de saisie de consommation est active du 2025-04-07 au 2025-04-25,ou vous pouvez saisir votre consommation.', 'non_lue', '2025-04-08 16:50:21'),
(3, 1, 'saisie_consommation', NULL, 'La période de saisie de consommation est active du 2025-04-07 au 2025-04-25,ou vous pouvez saisir votre consommation.', 'non_lue', '2025-04-08 17:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `periode_saisie`
--

DROP TABLE IF EXISTS `periode_saisie`;
CREATE TABLE IF NOT EXISTS `periode_saisie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `periode_saisie`
--

INSERT INTO `periode_saisie` (`id`, `date_debut`, `date_fin`, `active`, `updated_at`) VALUES
(1, '2025-04-07', '2025-04-25', 0, '2025-04-08 17:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `pieces_jointes`
--

DROP TABLE IF EXISTS `pieces_jointes`;
CREATE TABLE IF NOT EXISTS `pieces_jointes` (
  `piece_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
  `type` enum('fuite_externe','fuite_interne','facture','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `statut` enum('en_attente','en_traitement','résolue','refusée') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
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
  `contenu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reponse_id`),
  KEY `reclamation_id` (`reclamation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tarification`
--

DROP TABLE IF EXISTS `tarification`;
CREATE TABLE IF NOT EXISTS `tarification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tranche1` decimal(10,2) NOT NULL,
  `tranche2` decimal(10,2) NOT NULL,
  `tranche3` decimal(10,2) NOT NULL,
  `tva` decimal(5,2) NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tarification`
--

INSERT INTO `tarification` (`id`, `tranche1`, `tranche2`, `tranche3`, `tva`, `updated_at`) VALUES
(1, 0.82, 0.92, 1.10, 18.00, '2025-04-08 15:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('client','fournisseur','agent') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `created_at`) VALUES
(3, 'kihl@mail.com', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'fournisseur', '2025-03-31 21:52:44'),
(4, 'youns@mail.com', '$2y$10$a5bQeRcigLAMYfKkiUgC1.kDy8EZzynEpYDgalEA/9I.1/2K9XZOy', 'client', '2025-04-01 16:18:51'),
(5, 'nassim@gmail.com', 'test', 'agent', '2025-04-05 16:53:20'),
(6, 'nassime@gmail.com', 'test', 'fournisseur', '2025-04-05 16:53:54'),
(7, 'agent@example.com', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'agent', '2025-04-05 18:15:32'),
(8, 'nassim@mail.xom', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'client', '2025-04-08 13:59:55');

--
-- Constraints for dumped tables
--

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
-- Constraints for table `factures_annuelles`
--
ALTER TABLE `factures_annuelles`
  ADD CONSTRAINT `factures_annuelles_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `factures_annuelles_ibfk_2` FOREIGN KEY (`consommation_annuelle_id`) REFERENCES `consommations_annuelles` (`consommation_annuelle_id`);

--
-- Constraints for table `factures_mensuelle`
--
ALTER TABLE `factures_mensuelle`
  ADD CONSTRAINT `factures_mensuelle_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `factures_mensuelle_ibfk_2` FOREIGN KEY (`consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

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
