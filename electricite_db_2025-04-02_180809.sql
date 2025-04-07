DROP TABLE IF EXISTS `anomalies_consommation`;
DROP TABLE IF EXISTS `consommations_mensuelles`;
DROP TABLE IF EXISTS `compteurs`;
DROP TABLE IF EXISTS `clients`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `factures_mensuelle`;


CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('client','fournisseur') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `compteurs` (
  `compteur_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `numero_serie` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`compteur_id`),
  UNIQUE KEY `numero_serie` (`numero_serie`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `compteurs_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `consommations_mensuelles` (
  `consommation_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `compteur_id` int NOT NULL,
  `kw` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consommation_id`),
  KEY `client_id` (`client_id`),
  KEY `compteur_id` (`compteur_id`),
  CONSTRAINT `consommations_mensuelles_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  CONSTRAINT `consommations_mensuelles_ibfk_2` FOREIGN KEY (`compteur_id`) REFERENCES `compteurs` (`compteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `anomalies_consommation` (
  `anomalie_id` int NOT NULL AUTO_INCREMENT,
  `consommation_id` int NOT NULL,
  `previous_consommation_id` int DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `compteur_id` int NOT NULL,
  `entry_date` datetime NOT NULL,
  `previous_value` decimal(10,2) DEFAULT NULL,
  `entered_value` decimal(10,2) NOT NULL,
  `difference` decimal(10,2) NOT NULL,
  `status` enum('en_attente','en_traitement','résolue') COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`anomalie_id`),
  UNIQUE KEY `consommation_id` (`consommation_id`),
  KEY `previous_consommation_id` (`previous_consommation_id`),
  CONSTRAINT `anomalies_consommation_ibfk_1` FOREIGN KEY (`consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`),
  CONSTRAINT `anomalies_consommation_ibfk_2` FOREIGN KEY (`previous_consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reclamations` (
  `reclamation_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `type` enum('fuite_externe','fuite_interne','facture','autre') NOT NULL,
  `description` text NOT NULL,
  `statut` enum('en_attente','en_traitement','résolue','refusée') DEFAULT 'en_attente',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_resolution` datetime DEFAULT NULL,
  PRIMARY KEY (`reclamation_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `reclamations_fk_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pieces_jointes` (
  `piece_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`piece_id`),
  KEY `reclamation_id` (`reclamation_id`),
  CONSTRAINT `pieces_jointes_fk_reclamation` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reponses` (
  `reponse_id` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `contenu` text NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reponse_id`),
  KEY `reclamation_id` (`reclamation_id`),
  CONSTRAINT `reponses_fk_reclamation` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`reclamation_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table factures
CREATE TABLE `factures_mensuelle` (
  `facture_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `consommation_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `consommation` decimal(10,2) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `date_emission` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`facture_id`),
  KEY `client_id` (`client_id`),
  KEY `consommation_id` (`consommation_id`),
  CONSTRAINT `factures_mensuelle_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  CONSTRAINT `factures_mensuelle_ibfk_2` FOREIGN KEY (`consommation_id`) REFERENCES `consommations_mensuelles` (`consommation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table notifications
CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `client_id` INT NOT NULL,
  `type` ENUM('saisie_consommation', 'facture', 'reclamation', 'autre') NOT NULL,
  `reference` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `status` ENUM('non_lue', 'lue') DEFAULT 'non_lue',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notification_client` FOREIGN KEY (`client_id`)
    REFERENCES `clients` (`client_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `tarification`
CREATE TABLE `tarification` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tranche1` DECIMAL(10, 2) NOT NULL,
    `tranche2` DECIMAL(10, 2) NOT NULL,
    `tranche3` DECIMAL(10, 2) NOT NULL,
    `tva` DECIMAL(5, 2) NOT NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table `periode_saisie`
CREATE TABLE `periode_saisie` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `active` BOOLEAN DEFAULT TRUE,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `created_at`) VALUES 
(3, 'kihl@mail.com', '$2y$10$Tg0br9R43vKoTpkEU82pv.4IdimHVqgGXtUGXQvZP8AVYMN7M7vH6', 'fournisseur', '2025-03-31 21:52:44'),
(4, 'youns@mail.com', '$2y$10$a5bQeRcigLAMYfKkiUgC1.kDy8EZzynEpYDgalEA/9I.1/2K9XZOy', 'client', '2025-04-01 16:18:51');

INSERT INTO `clients` (`client_id`, `user_id`, `full_name`, `address`, `phone`, `created_at`) VALUES 
(1, 4, 'ELKIHAL YOUNESS', '13 RUE SALMA 2 TETOUAN', '+212638742013', '2025-01-28 23:21:14');

INSERT INTO `compteurs` (`compteur_id`, `client_id`, `numero_serie`) VALUES 
(1, 1, 'C210298'),
(2, 1, 'C213271');

INSERT INTO `consommations_mensuelles` (`consommation_id`, `client_id`, `compteur_id`, `kw`, `image_path`, `created_at`) VALUES 
(7, 1, 1, 1600.00, '../../uploads/meters/meter_1.jpg', '2025-03-01 10:00:00');

INSERT INTO `tarification` (`tranche1`, `tranche2`, `tranche3`, `tva`) 
VALUES (0.82, 0.92, 1.1, 18)
ON DUPLICATE KEY UPDATE 
    tranche1 = VALUES(tranche1), 
    tranche2 = VALUES(tranche2), 
    tranche3 = VALUES(tranche3), 
    tva = VALUES(tva);

INSERT INTO `periode_saisie` (`date_debut`, `date_fin`, `active`) 
VALUES ('2025-04-18', '2025-04-25', TRUE);    

