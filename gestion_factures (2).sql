-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 12:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `user_id`, `full_name`, `address`, `phone`) VALUES
(1, 1, 'Touicha Aya', '123 Avenue des Champs, Paris', '+33123456789'),
(2, 2, 'Kaoutar Iabakriman', '456 Rue de la République, Lyon', '+33456789123');

-- --------------------------------------------------------

--
-- Table structure for table `compteurs`
--

CREATE TABLE `compteurs` (
  `compteur_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `numero_serie` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `factures`
--

CREATE TABLE `factures` (
  `facture_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `periode` date NOT NULL,
  `consommation` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut_paiement` enum('payée','impayée') DEFAULT 'impayée',
  `date_emission` date NOT NULL,
  `tarif_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_activites`
--

CREATE TABLE `journal_activites` (
  `activite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `date_action` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periodes_saisie`
--

CREATE TABLE `periodes_saisie` (
  `periode_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pieces_jointes`
--

CREATE TABLE `pieces_jointes` (
  `piece_id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pieces_jointes`
--

INSERT INTO `pieces_jointes` (`piece_id`, `reclamation_id`, `file_path`, `type`) VALUES
(13, 38, '1743586293_Lexique et symbolique de l\'architecture traditionnelle (1) (2).pdf', 'application/pdf'),
(14, 38, '1743586293_mine.jpg', 'image/jpeg'),
(15, 39, '1743625109_WhatsApp Image 2024-12-12 at 11.08.26.jpeg', 'image/jpeg'),
(16, 44, '1743626327_Resume (1).pdf', 'application/pdf'),
(17, 45, '1743629707_WhatsApp_Image_2024-12-12_at_11.08.26-removebg-preview.png', 'image/png'),
(18, 46, '1743629710_WhatsApp_Image_2024-12-12_at_11.08.26-removebg-preview.png', 'image/png'),
(19, 47, '1743630969_Resume (1).pdf', 'application/pdf'),
(20, 48, '1743633661_Resume (1).pdf', 'application/pdf');

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

CREATE TABLE `reclamations` (
  `reclamation_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `type` enum('fuite_externe','fuite_interne','facture','autre') NOT NULL,
  `description` text NOT NULL,
  `statut` enum('en_attente','en_traitement','résolue','refusée') DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_resolution` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reclamations`
--

INSERT INTO `reclamations` (`reclamation_id`, `client_id`, `type`, `description`, `statut`, `date_creation`, `date_resolution`) VALUES
(38, 1, 'fuite_interne', 'j&#039;ai une fuite d&#039;eau', 'résolue', '2025-04-02 09:31:33', NULL),
(39, 1, 'fuite_externe', 'jjs jkjjsa', 'en_attente', '2025-04-02 20:18:29', NULL),
(40, 1, 'facture', 'jhjha jksajksajk', 'en_attente', '2025-04-02 20:20:29', NULL),
(41, 1, 'facture', 'jhjha jksajksajk', 'en_traitement', '2025-04-02 20:20:41', '2025-04-02 20:23:36'),
(42, 1, 'facture', 'ayaaa ayaa', 'résolue', '2025-04-02 20:25:12', '2025-04-02 20:25:47'),
(43, 1, 'fuite_interne', 'test derniere', 'refusée', '2025-04-02 20:35:19', NULL),
(44, 1, 'fuite_externe', 'piece jointe', 'refusée', '2025-04-02 20:38:47', '2025-04-02 20:40:50'),
(45, 1, 'fuite_interne', 'hjsahsa sajhsah', 'en_attente', '2025-04-02 21:35:07', NULL),
(46, 1, 'fuite_interne', 'hjsahsa sajhsah', 'refusée', '2025-04-02 21:35:10', NULL),
(47, 1, 'fuite_interne', 'hjash sajhshj', 'résolue', '2025-04-02 21:56:09', '2025-04-02 21:56:29'),
(48, 1, 'fuite_interne', 'test de connexion', 'en_attente', '2025-04-02 22:41:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `releves`
--

CREATE TABLE `releves` (
  `releve_id` int(11) NOT NULL,
  `compteur_id` int(11) NOT NULL,
  `valeur_precedente` int(11) NOT NULL,
  `valeur_actuelle` int(11) NOT NULL,
  `date_releve` date NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `statut` enum('soumis','validé') DEFAULT 'soumis'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reponses`
--

CREATE TABLE `reponses` (
  `reponse_id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date_reponse` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reponses`
--

INSERT INTO `reponses` (`reponse_id`, `reclamation_id`, `contenu`, `status`, `date_reponse`) VALUES
(1, 41, 'en cours de traitement', NULL, '2025-04-02 20:24:30'),
(3, 42, 'c bon maitenanat', NULL, '2025-04-02 20:25:47'),
(4, 44, 'c bon', NULL, '2025-04-02 20:40:50'),
(7, 43, 'pas logique', NULL, '2025-04-02 20:57:45'),
(8, 44, 'c bon maintenant', 'refusée', '2025-04-02 21:02:48'),
(9, 46, 'rien', 'refusée', '2025-04-02 21:35:51'),
(10, 47, 'c bon', 'résolue', '2025-04-02 21:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `tarifs`
--

CREATE TABLE `tarifs` (
  `tarif_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `tranche1_max` int(11) NOT NULL,
  `tranche1_prix` decimal(5,2) NOT NULL,
  `tranche2_max` int(11) DEFAULT NULL,
  `tranche2_prix` decimal(5,2) NOT NULL,
  `tranche3_prix` decimal(5,2) NOT NULL,
  `tva` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `template_id` int(11) NOT NULL,
  `categorie` enum('fuite_externe','fuite_interne','facture','autre') NOT NULL,
  `nom_template` varchar(255) NOT NULL,
  `contenu` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('client','fournisseur') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'aya@gmail.com', '$2y$10$A87AijOXosc0.VEizFiG8.K1FQEodwEpDz5xLdqdibR1dk6OFALDe', 'client', '2025-03-30 14:28:04'),
(2, 'kaoutar@gmmail.com', '$2y$10$EqGEdP.9MvRFhq9GvyFemOj4AoLRLuMvBZh.zhfaap3VsXARJBASK', 'client', '2025-03-30 14:28:47'),
(13, 'imane@gmail.com', '$2y$10$reMJAmszlg1XWkFH6xmewee01XM2fjnGOEIUYwkIn7HKZ.aLF8iom', 'client', '2025-04-02 22:36:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `compteurs`
--
ALTER TABLE `compteurs`
  ADD PRIMARY KEY (`compteur_id`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`facture_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_factures_statut` (`statut_paiement`),
  ADD KEY `tarif_id` (`tarif_id`);

--
-- Indexes for table `journal_activites`
--
ALTER TABLE `journal_activites`
  ADD PRIMARY KEY (`activite_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `periodes_saisie`
--
ALTER TABLE `periodes_saisie`
  ADD PRIMARY KEY (`periode_id`);

--
-- Indexes for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD PRIMARY KEY (`piece_id`),
  ADD KEY `reclamation_id` (`reclamation_id`);

--
-- Indexes for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`reclamation_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_reclamations_statut` (`statut`);

--
-- Indexes for table `releves`
--
ALTER TABLE `releves`
  ADD PRIMARY KEY (`releve_id`),
  ADD KEY `compteur_id` (`compteur_id`),
  ADD KEY `idx_releves_statut` (`statut`);

--
-- Indexes for table `reponses`
--
ALTER TABLE `reponses`
  ADD PRIMARY KEY (`reponse_id`),
  ADD KEY `reponses_ibfk_1` (`reclamation_id`);

--
-- Indexes for table `tarifs`
--
ALTER TABLE `tarifs`
  ADD PRIMARY KEY (`tarif_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `compteurs`
--
ALTER TABLE `compteurs`
  MODIFY `compteur_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `factures`
--
ALTER TABLE `factures`
  MODIFY `facture_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_activites`
--
ALTER TABLE `journal_activites`
  MODIFY `activite_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `periodes_saisie`
--
ALTER TABLE `periodes_saisie`
  MODIFY `periode_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `piece_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `reclamation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `releves`
--
ALTER TABLE `releves`
  MODIFY `releve_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reponses`
--
ALTER TABLE `reponses`
  MODIFY `reponse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `tarif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- Constraints for table `factures`
--
ALTER TABLE `factures`
  ADD CONSTRAINT `factures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `factures_ibfk_2` FOREIGN KEY (`tarif_id`) REFERENCES `tarifs` (`tarif_id`);

--
-- Constraints for table `journal_activites`
--
ALTER TABLE `journal_activites`
  ADD CONSTRAINT `journal_activites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`);

--
-- Constraints for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `reclamations_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `releves`
--
ALTER TABLE `releves`
  ADD CONSTRAINT `releves_ibfk_1` FOREIGN KEY (`compteur_id`) REFERENCES `compteurs` (`compteur_id`);

--
-- Constraints for table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `reponses_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
