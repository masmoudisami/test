-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 12 mars 2026 à 06:10
-- Version du serveur : 8.0.44-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mechanic_db`
--
CREATE DATABASE IF NOT EXISTS `mechanic_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mechanic_db`;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `car_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `name`, `car_model`, `phone`, `address`, `created_at`) VALUES
(1, 'Sami Masmoudi', 'xxx', '29972692', 'Route Gremda km6 Av bouzaine', '2026-03-11 18:45:04');

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `invoice_date` date NOT NULL,
  `mileage` int DEFAULT '0',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `droit_timbre` decimal(10,3) DEFAULT '0.000',
  `tax_rate` decimal(5,2) DEFAULT '19.00',
  `total_ht` decimal(10,3) DEFAULT '0.000',
  `total_tva` decimal(10,3) DEFAULT '0.000',
  `total_ttc` decimal(10,3) DEFAULT '0.000',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invoices`
--

INSERT INTO `invoices` (`id`, `client_id`, `invoice_date`, `mileage`, `comment`, `droit_timbre`, `tax_rate`, `total_ht`, `total_tva`, `total_ttc`, `created_at`) VALUES
(1, 1, '2026-03-11', 83, 'fhsf', 1.000, 19.00, 7.000, 1.330, 9.330, '2026-03-11 18:45:59'),
(2, 1, '2026-03-11', 0, '', 0.000, 19.00, 57.000, 10.830, 67.830, '2026-03-11 18:51:24'),
(4, 1, '2026-03-11', 575, 'srsr', 0.000, 19.00, 7575757.000, 1439393.830, 9015150.830, '2026-03-11 19:28:40');

-- --------------------------------------------------------

--
-- Structure de la table `invoice_lines`
--

DROP TABLE IF EXISTS `invoice_lines`;
CREATE TABLE IF NOT EXISTS `invoice_lines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `repair_type_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `price_unit` decimal(10,3) NOT NULL,
  `total_line` decimal(10,3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `repair_type_id` (`repair_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invoice_lines`
--

INSERT INTO `invoice_lines` (`id`, `invoice_id`, `repair_type_id`, `quantity`, `price_unit`, `total_line`) VALUES
(1, 1, 2, 1, 7.000, 7.000),
(2, 2, 1, 1, 57.000, 57.000),
(4, 4, 2, 1, 7575757.000, 7575757.000);

-- --------------------------------------------------------

--
-- Structure de la table `repair_types`
--

DROP TABLE IF EXISTS `repair_types`;
CREATE TABLE IF NOT EXISTS `repair_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_price` decimal(10,3) DEFAULT '0.000',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `repair_types`
--

INSERT INTO `repair_types` (`id`, `name`, `default_price`, `created_at`) VALUES
(1, 'ddq', 57.000, '2026-03-11 18:44:44'),
(2, 'cvbf', 7.000, '2026-03-11 18:44:50');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD CONSTRAINT `invoice_lines_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_lines_ibfk_2` FOREIGN KEY (`repair_type_id`) REFERENCES `repair_types` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
