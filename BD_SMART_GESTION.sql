-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 14 Février 2023 à 16:47
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `bd_smart_gestion`
--
CREATE DATABASE IF NOT EXISTS `bd_smart_gestion` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `bd_smart_gestion`;

-- --------------------------------------------------------

--
-- Structure de la table `fac`
--

CREATE TABLE IF NOT EXISTS `FAC` (
  `FAC` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `fac`
--

INSERT INTO `FAC` (`FAC`) VALUES
(7),
(7);

-- --------------------------------------------------------

--
-- Structure de la table `plus`
--

CREATE TABLE IF NOT EXISTS `PLUS` (
  `TITRE` varchar(255) NOT NULL,
  `ADRESSE` varchar(255) NOT NULL,
  `PHONE` varchar(255) NOT NULL,
  `NUM_NATIONAL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `plus`
--

INSERT INTO `plus` (`TITRE`, `ADRESSE`, `PHONE`, `NUM_NATIONAL`) VALUES
('LynGold', 'Nyawera, av. PE Lumumba, numÃ©ro 0020', '+243995892530, +243973458095', 'RCCM/Cd/BKV/20-A-00234'),
('LynGold', 'Nyawera, av. PE Lumumba, numÃ©ro 0020', '+243995892530, +243973458095', 'RCCM/Cd/BKV/20-A-00234');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE IF NOT EXISTS `PRODUIT` (
  `ID_PRODUIT` int(11) NOT NULL AUTO_INCREMENT,
  `TITRE_PRODUIT` varchar(255) NOT NULL,
  `PRIX_ACHAT_PRODUIT` double NOT NULL,
  `PRIX_VENTE_PRODUIT` double NOT NULL,
  `QUANTITE_PRODUIT` int(11) NOT NULL,
  `SEUIL_PRODUIT` int(11) NOT NULL,
  PRIMARY KEY (`ID_PRODUIT`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `produit`
--

INSERT INTO `produit` (`ID_PRODUIT`, `TITRE_PRODUIT`, `PRIX_ACHAT_PRODUIT`, `PRIX_VENTE_PRODUIT`, `QUANTITE_PRODUIT`, `SEUIL_PRODUIT`) VALUES
(1, 'Parfum Furaha', 2.5, 5, 195, 5),
(2, 'Huile de cheveux', 1.5, 3, 47, 10),
(3, 'Lotion de beautÃ©', 10.5, 25, 235, 15),
(6, 'Savon de beautÃ©', 0.5, 2.5, 3, 5);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `UTILISATEUR` (
  `ID_UTILISATEUR` int(11) NOT NULL AUTO_INCREMENT,
  `LOGIN` varchar(255) NOT NULL,
  `PASSE` varchar(255) NOT NULL,
  `NOM` varchar(255) NOT NULL,
  `NIVEAU` int(1) NOT NULL,
  PRIMARY KEY (`ID_UTILISATEUR`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `UTILISATEUR` (`ID_UTILISATEUR`, `LOGIN`, `PASSE`, `NOM`, `NIVEAU`) VALUES
(1, 'kagala', '81dc9bdb52d04dc20036dbd8313ed055', 'Kagala Cenyange', 1),
(3, 'paul', '81dc9bdb52d04dc20036dbd8313ed055', 'Paul Cihyoka', 0),
(4, 'bwony', '81dc9bdb52d04dc20036dbd8313ed055', 'Bwony Bir', 1);

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

CREATE TABLE IF NOT EXISTS `vente` (
  `ID_VENTE` int(11) NOT NULL AUTO_INCREMENT,
  `QUANTITE_VENTE` int(11) NOT NULL,
  `PRIX_VENTE` double NOT NULL,
  `PRIX_ACHAT` double NOT NULL,
  `VENDEUR` varchar(255) NOT NULL,
  `DATE_VENTE` datetime NOT NULL,
  `ID_PRODUIT` int(11) NOT NULL,
  PRIMARY KEY (`ID_VENTE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `vente`
--

INSERT INTO `vente` (`ID_VENTE`, `QUANTITE_VENTE`, `PRIX_VENTE`, `PRIX_ACHAT`, `VENDEUR`, `DATE_VENTE`, `ID_PRODUIT`) VALUES
(3, 1, 2.5, 0.5, 'Kagala Cenyange', '2023-02-11 19:53:22', 6),
(4, 5, 5, 2.5, 'Kagala Cenyange', '2023-02-11 19:53:31', 1),
(5, 2, 3, 1.5, 'Kagala Cenyange', '2023-02-11 19:53:40', 2),
(6, 10, 25, 10.5, 'Kagala Cenyange', '2023-02-12 17:37:15', 3),
(7, 1, 3, 1.5, 'Kagala Cenyange', '2023-02-12 17:56:51', 2),
(8, 2, 25, 10.5, 'Kagala Cenyange', '2023-02-13 12:08:40', 3),
(9, 3, 25, 10.5, 'Kagala Cenyange', '2023-02-14 11:20:11', 3),
(10, 1, 2.5, 0.5, 'Kagala Cenyange', '2023-02-14 11:38:18', 6);

-- --------------------------------------------------------

--
-- Structure de la table `vente_annulee`
--

CREATE TABLE IF NOT EXISTS `vente_annulee` (
  `ID_VENTE_ANNULEE` int(11) NOT NULL AUTO_INCREMENT,
  `QUANTITE_VENTE_ANNULEE` int(11) NOT NULL,
  `PRIX_VENTE_ANNULEE` int(11) NOT NULL,
  `DATE_VENTE_ANNULEE` datetime NOT NULL,
  `ID_PRODUIT` int(11) NOT NULL,
  PRIMARY KEY (`ID_VENTE_ANNULEE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `vente_annulee`
--

INSERT INTO `vente_annulee` (`ID_VENTE_ANNULEE`, `QUANTITE_VENTE_ANNULEE`, `PRIX_VENTE_ANNULEE`, `DATE_VENTE_ANNULEE`, `ID_PRODUIT`) VALUES
(1, 5, 5, '2023-02-13 17:18:15', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
