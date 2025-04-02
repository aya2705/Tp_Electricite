
-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS gestion_factures;
USE gestion_factures;

-- Users table - keep your friend's structure
CREATE TABLE users (
  user_id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('client', 'fournisseur', 'agent') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email)
);

-- Clients table - keep your friend's structure
CREATE TABLE clients (
  client_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  address TEXT NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (client_id),
  UNIQUE KEY (user_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Meters table - keep your friend's structure
CREATE TABLE compteurs (
  compteur_id INT NOT NULL AUTO_INCREMENT,
  client_id INT NOT NULL,
  numero_serie VARCHAR(50) NOT NULL,
  PRIMARY KEY (compteur_id),
  UNIQUE KEY (numero_serie),
  FOREIGN KEY (client_id) REFERENCES clients(client_id)
);

-- Period table for submission periods
-- CREATE TABLE periodes_saisie (
--  periode_id INT NOT NULL AUTO_INCREMENT,
 -- mois INT NOT NULL,
--  annee INT NOT NULL,
  --date_debut DATE NOT NULL,
--  date_fin DATE NOT NULL,
  --est_active BOOLEAN DEFAULT FALSE,
 -- PRIMARY KEY (periode_id),
  --INDEX idx_periode_date (mois, annee)
--); 

-- Monthly consumption table - matches your model
CREATE TABLE consommations_mensuelles (
  consommation_id INT NOT NULL AUTO_INCREMENT,
  client_id INT NOT NULL,
  compteur_id INT NOT NULL,
 -- periode_id INT NOT NULL,
  kw DECIMAL(10,2) NOT NULL,
 -- valeur_precedente DECIMAL(10,2) NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 -- est_validee BOOLEAN DEFAULT FALSE,
  est_anormale BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (consommation_id),
  FOREIGN KEY (client_id) REFERENCES clients(client_id),
  FOREIGN KEY (compteur_id) REFERENCES compteurs(compteur_id),
--  FOREIGN KEY (periode_id) REFERENCES periodes_saisie(periode_id),
--  INDEX idx_consommation_client (client_id),
--  INDEX idx_consommation_compteur (compteur_id),
 -- INDEX idx_consommation_periode (periode_id)
);
