-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 09 jan. 2026 à 11:32
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `storesuite`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(255) NOT NULL COMMENT 'Nom de la catégorie',
  `description` text DEFAULT NULL COMMENT 'Description de la catégorie',
  `icone` varchar(100) DEFAULT NULL COMMENT 'Icône ou classe CSS',
  `couleur` varchar(7) DEFAULT NULL COMMENT 'Couleur associée (format HEX)',
  `ordre_affichage` int(11) DEFAULT 0 COMMENT 'Ordre d''affichage',
  `est_actif` tinyint(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catégories de produits';

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom_categorie`, `description`, `icone`, `couleur`, `ordre_affichage`, `est_actif`, `date_creation`, `date_modification`) VALUES
(1, 'Électronique', 'Téléphones, ordinateurs, accessoires', 'ti-device-laptop', '#3498db', 1, 1, '2026-01-08 13:39:48', '2026-01-08 13:39:48'),
(2, 'Électroménager', 'Réfrigérateurs, télévisions, cuisinières', 'ti-device-tv', '#e74c3c', 2, 1, '2026-01-08 13:39:48', '2026-01-08 13:39:48'),
(3, 'Meubles', 'Tables, chaises, armoires', 'ti-armchair', '#9b59b6', 3, 1, '2026-01-08 13:39:48', '2026-01-08 13:39:48'),
(4, 'Vêtements', 'Habits, chaussures, accessoires', 'ti-hanger', '#1abc9c', 4, 1, '2026-01-08 13:39:48', '2026-01-08 13:39:48'),
(5, 'Alimentation', 'Produits alimentaires', 'ti-shopping-cart', '#f39c12', 5, 1, '2026-01-08 13:39:48', '2026-01-08 13:39:48');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `nom_client` varchar(255) NOT NULL COMMENT 'Nom du client',
  `telephone` varchar(50) DEFAULT NULL COMMENT 'Téléphone',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email',
  `adresse` text DEFAULT NULL COMMENT 'Adresse complète',
  `type_client` enum('particulier','entreprise') DEFAULT 'particulier',
  `numero_fiscal` varchar(100) DEFAULT NULL COMMENT 'Numéro fiscal (pour entreprises)',
  `total_achats` decimal(15,2) DEFAULT 0.00 COMMENT 'Total des achats',
  `nombre_achats` int(11) DEFAULT 0 COMMENT 'Nombre d''achats',
  `date_dernier_achat` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL COMMENT 'Notes sur le client',
  `est_actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Base de données clients';

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom_client`, `telephone`, `email`, `adresse`, `type_client`, `numero_fiscal`, `total_achats`, `nombre_achats`, `date_dernier_achat`, `notes`, `est_actif`, `date_creation`, `date_modification`) VALUES
(1, 'BAHATI', '', '', '', 'particulier', NULL, 0.00, 0, NULL, NULL, 1, '2026-01-08 13:39:48', '2026-01-09 09:29:18');

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

CREATE TABLE `configuration` (
  `id_config` int(11) NOT NULL,
  `nom_boutique` varchar(255) NOT NULL COMMENT 'Nom de la boutique/entreprise',
  `slogan` varchar(255) DEFAULT NULL COMMENT 'Slogan ou description courte',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Chemin vers le fichier logo',
  `couleur_primaire` varchar(7) DEFAULT '#e6e64c' COMMENT 'Couleur principale (format HEX)',
  `couleur_secondaire` varchar(7) DEFAULT '#556a94' COMMENT 'Couleur secondaire (format HEX)',
  `adresse` text DEFAULT NULL COMMENT 'Adresse complète de l''entreprise',
  `telephone` varchar(100) DEFAULT NULL COMMENT 'Numéro(s) de téléphone',
  `email` varchar(255) DEFAULT NULL COMMENT 'Adresse email',
  `site_web` varchar(255) DEFAULT NULL COMMENT 'Site web de l''entreprise',
  `num_registre_commerce` varchar(100) DEFAULT NULL COMMENT 'Numéro d''enregistrement (RCCM, etc.)',
  `num_impot` varchar(100) DEFAULT NULL COMMENT 'Numéro fiscal/TVA',
  `devise` varchar(10) DEFAULT '$' COMMENT 'Symbole de la devise utilisée',
  `taux_tva` decimal(5,2) DEFAULT 0.00 COMMENT 'Taux de TVA par défaut (%)',
  `fuseau_horaire` varchar(50) DEFAULT 'Africa/Lubumbashi' COMMENT 'Fuseau horaire',
  `langue` varchar(10) DEFAULT 'fr' COMMENT 'Langue du système (fr, en, etc.)',
  `est_configure` tinyint(1) DEFAULT 0 COMMENT '0=Non configuré, 1=Configuré',
  `date_configuration` datetime DEFAULT NULL COMMENT 'Date de première configuration',
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Paramètres globaux du système';

--
-- Déchargement des données de la table `configuration`
--

INSERT INTO `configuration` (`id_config`, `nom_boutique`, `slogan`, `logo`, `couleur_primaire`, `couleur_secondaire`, `adresse`, `telephone`, `email`, `site_web`, `num_registre_commerce`, `num_impot`, `devise`, `taux_tva`, `fuseau_horaire`, `langue`, `est_configure`, `date_configuration`, `date_modification`) VALUES
(1, 'CALEB SHOP', 'votre boutique d\'excellence', 'logo_6960b16333b21.png', '#206bc4', '#ffffff', 'ULINDI NDENDENRE 031', '+243 974051239', 'contact@caleb.com', 'https://www.test.com', 'RCCM/TEST/123', 'IMP-12345', '$', 0.00, 'Africa/Lubumbashi', 'fr', 1, '2026-01-09 09:42:27', '2026-01-09 07:42:27');

-- --------------------------------------------------------

--
-- Structure de la table `details_vente`
--

CREATE TABLE `details_vente` (
  `id_detail` int(11) NOT NULL,
  `id_vente` int(11) NOT NULL COMMENT 'Référence à la vente',
  `id_produit` int(11) NOT NULL COMMENT 'Produit vendu',
  `nom_produit` varchar(255) NOT NULL COMMENT 'Nom du produit',
  `quantite` int(11) NOT NULL DEFAULT 1 COMMENT 'Quantité vendue',
  `prix_unitaire` decimal(15,2) NOT NULL COMMENT 'Prix unitaire',
  `prix_achat_unitaire` decimal(15,2) NOT NULL COMMENT 'Prix achat',
  `prix_total` decimal(15,2) NOT NULL COMMENT 'Total ligne',
  `benefice_ligne` decimal(15,2) NOT NULL COMMENT 'Bénéfice ligne',
  `remise_ligne` decimal(15,2) DEFAULT 0.00 COMMENT 'Remise ligne',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logs_activites`
--

CREATE TABLE `logs_activites` (
  `id_log` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `type_action` varchar(100) NOT NULL COMMENT 'Type d''action (connexion, vente, modification, etc.)',
  `description` text NOT NULL COMMENT 'Description détaillée de l''action',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Adresse IP',
  `user_agent` text DEFAULT NULL COMMENT 'Navigateur/Device',
  `donnees_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Données supplémentaires en JSON' CHECK (json_valid(`donnees_json`)),
  `date_action` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Journal des activités';

--
-- Déchargement des données de la table `logs_activites`
--

INSERT INTO `logs_activites` (`id_log`, `id_utilisateur`, `type_action`, `description`, `ip_address`, `user_agent`, `donnees_json`, `date_action`) VALUES
(1, 2, 'configuration_initiale', 'Configuration initiale du système effectuée', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"nom_boutique\":\"Ma Super Boutique Test\",\"admin_login\":\"admin\"}', '2026-01-09 09:33:05'),
(2, 3, 'configuration_initiale', 'Configuration initiale du système effectuée', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '{\"nom_boutique\":\"CALEB SHOP\",\"admin_login\":\"admin\"}', '2026-01-09 09:42:27'),
(3, 3, 'connexion', 'Connexion réussie de Emmanuel K', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-09 08:43:43'),
(4, 3, 'deconnexion', 'Déconnexion de Emmanuel K', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-09 09:07:31'),
(5, 3, 'connexion', 'Connexion réussie de Emmanuel K', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2026-01-09 09:07:50');

-- --------------------------------------------------------

--
-- Structure de la table `mouvements`
--

CREATE TABLE `mouvements` (
  `id_mouvement` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `type_mouvement` enum('entree','sortie','ajustement','vente') NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(15,2) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL COMMENT 'Numéro facture ou bon',
  `motif` text DEFAULT NULL,
  `date_mouvement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

CREATE TABLE `mouvements_stock` (
  `id_mouvement` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL COMMENT 'Produit concerné',
  `type_mouvement` enum('entree','sortie','ajustement','retour') NOT NULL,
  `quantite` int(11) NOT NULL COMMENT 'Quantité du mouvement',
  `quantite_avant` int(11) NOT NULL COMMENT 'Stock avant le mouvement',
  `quantite_apres` int(11) NOT NULL COMMENT 'Stock après le mouvement',
  `id_vente` int(11) DEFAULT NULL COMMENT 'Référence vente si sortie',
  `id_utilisateur` int(11) NOT NULL COMMENT 'Utilisateur qui a fait l''opération',
  `motif` varchar(255) DEFAULT NULL COMMENT 'Raison du mouvement',
  `notes` text DEFAULT NULL,
  `date_mouvement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historique des mouvements de stock';

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `type_notification` enum('stock_faible','stock_critique','rupture_stock','vente_importante','systeme') NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `id_produit` int(11) DEFAULT NULL COMMENT 'Produit concerné si applicable',
  `niveau_urgence` enum('info','avertissement','urgent') DEFAULT 'info',
  `est_lue` tinyint(1) DEFAULT 0 COMMENT '0=Non lue, 1=Lue',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifications système';

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notification`, `type_notification`, `titre`, `message`, `id_produit`, `niveau_urgence`, `est_lue`, `date_creation`) VALUES
(1, 'systeme', 'Bienvenue !', 'Votre système de gestion de stock a été configuré avec succès. Vous pouvez maintenant commencer à ajouter vos produits et effectuer vos ventes.', NULL, 'info', 0, '2026-01-09 09:33:05'),
(2, 'systeme', 'Bienvenue !', 'Votre système de gestion de stock a été configuré avec succès. Vous pouvez maintenant commencer à ajouter vos produits et effectuer vos ventes.', NULL, 'info', 0, '2026-01-09 09:42:27');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL,
  `code_produit` varchar(100) DEFAULT NULL COMMENT 'Code/Référence unique du produit',
  `nom_produit` varchar(255) NOT NULL COMMENT 'Nom du produit',
  `description` text DEFAULT NULL COMMENT 'Description détaillée',
  `id_categorie` int(11) DEFAULT NULL COMMENT 'Catégorie du produit',
  `prix_achat` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Prix d''achat (VISIBLE ADMIN SEULEMENT)',
  `prix_vente` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Prix de vente recommandé',
  `prix_vente_min` decimal(15,2) DEFAULT NULL COMMENT 'Prix de vente minimum autorisé',
  `quantite_stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Quantité actuelle en stock',
  `seuil_alerte` int(11) DEFAULT 10 COMMENT 'Seuil pour alerte stock faible',
  `seuil_critique` int(11) DEFAULT 5 COMMENT 'Seuil critique (alerte rouge)',
  `unite_mesure` varchar(50) DEFAULT 'pièce' COMMENT 'Unité (pièce, kg, litre, etc.)',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image du produit',
  `code_barre` varchar(100) DEFAULT NULL COMMENT 'Code-barres pour scanner',
  `emplacement` varchar(255) DEFAULT NULL COMMENT 'Emplacement dans le magasin',
  `date_entree` date DEFAULT NULL COMMENT 'Date dernière entrée en stock',
  `date_derniere_vente` datetime DEFAULT NULL COMMENT 'Date de la dernière vente',
  `nombre_ventes` int(11) DEFAULT 0 COMMENT 'Nombre total de ventes',
  `est_actif` tinyint(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produits en stock avec gestion des alertes';

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `code_produit`, `nom_produit`, `description`, `id_categorie`, `prix_achat`, `prix_vente`, `prix_vente_min`, `quantite_stock`, `seuil_alerte`, `seuil_critique`, `unite_mesure`, `image`, `code_barre`, `emplacement`, `date_entree`, `date_derniere_vente`, `nombre_ventes`, `est_actif`, `date_creation`, `date_modification`) VALUES
(1, NULL, 'Television Samsung 32\'', '', 1, 500.00, 1200.00, NULL, 5, 1, 5, 'pièce', NULL, NULL, NULL, NULL, NULL, 0, 1, '2026-01-09 10:24:34', '2026-01-09 09:24:34');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom_complet` varchar(255) NOT NULL COMMENT 'Nom complet de l''utilisateur',
  `login` varchar(100) NOT NULL COMMENT 'Identifiant de connexion (unique)',
  `mot_de_passe` varchar(255) NOT NULL COMMENT 'Mot de passe hashé (password_hash)',
  `email` varchar(255) DEFAULT NULL COMMENT 'Adresse email',
  `telephone` varchar(50) DEFAULT NULL COMMENT 'Numéro de téléphone',
  `niveau_acces` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=Admin, 2=Vendeur',
  `photo` varchar(255) DEFAULT NULL COMMENT 'Photo de profil',
  `est_actif` tinyint(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` datetime DEFAULT current_timestamp() COMMENT 'Date de création du compte',
  `date_derniere_connexion` datetime DEFAULT NULL COMMENT 'Dernière connexion',
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utilisateurs du système avec permissions';

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom_complet`, `login`, `mot_de_passe`, `email`, `telephone`, `niveau_acces`, `photo`, `est_actif`, `date_creation`, `date_derniere_connexion`, `date_modification`) VALUES
(3, 'Emmanuel K', 'admin', '$2y$10$qcv4m7Sf5FsYTzk5sfFfe.TtdxPMvI3d5o1e4iv44HI0i/5JbYASy', 'admin@caleb.com', NULL, 1, NULL, 1, '2026-01-09 09:42:27', '2026-01-09 10:07:50', '2026-01-09 08:07:50');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id_vente` int(11) NOT NULL,
  `numero_facture` varchar(50) NOT NULL COMMENT 'Numéro unique de la facture',
  `id_client` int(11) DEFAULT NULL COMMENT 'Client (NULL = vente comptoir)',
  `id_vendeur` int(11) NOT NULL COMMENT 'Vendeur/Caissier qui a effectué la vente',
  `montant_total` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant total de la vente',
  `montant_ht` decimal(10,2) DEFAULT 0.00,
  `montant_remise` decimal(15,2) DEFAULT 0.00 COMMENT 'Remise accordée',
  `montant_tva` decimal(15,2) DEFAULT 0.00 COMMENT 'Montant TVA',
  `montant_paye` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant payé par le client',
  `montant_rendu` decimal(15,2) DEFAULT 0.00 COMMENT 'Monnaie rendue',
  `mode_paiement` enum('especes','carte','mobile_money','cheque','credit') DEFAULT 'especes',
  `statut` enum('en_cours','validee','annulee') DEFAULT 'validee',
  `notes` text DEFAULT NULL COMMENT 'Notes ou observations',
  `date_vente` datetime DEFAULT current_timestamp() COMMENT 'Date et heure de la vente',
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='En-têtes des ventes (factures)';

--
-- Déclencheurs `ventes`
--
DELIMITER $$
CREATE TRIGGER `before_vente_insert` BEFORE INSERT ON `ventes` FOR EACH ROW BEGIN
    IF NEW.numero_facture IS NULL OR NEW.numero_facture = '' THEN
        SET NEW.numero_facture = CONCAT('FAC', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD((SELECT COALESCE(MAX(id_vente), 0) + 1 FROM ventes), 6, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `ventes_details`
--

CREATE TABLE `ventes_details` (
  `id_detail` int(11) NOT NULL,
  `id_vente` int(11) NOT NULL COMMENT 'Référence à la vente',
  `id_produit` int(11) NOT NULL COMMENT 'Produit vendu',
  `nom_produit` varchar(255) NOT NULL COMMENT 'Nom du produit (copie pour historique)',
  `quantite` int(11) NOT NULL DEFAULT 1 COMMENT 'Quantité vendue',
  `prix_unitaire` decimal(15,2) NOT NULL COMMENT 'Prix unitaire de vente',
  `prix_achat_unitaire` decimal(15,2) NOT NULL COMMENT 'Prix d''achat (pour calcul bénéfice)',
  `prix_total` decimal(15,2) NOT NULL COMMENT 'Prix total de la ligne (quantité × prix)',
  `benefice_ligne` decimal(15,2) NOT NULL COMMENT 'Bénéfice sur cette ligne',
  `remise_ligne` decimal(15,2) DEFAULT 0.00 COMMENT 'Remise sur cette ligne',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Détails des ventes (lignes de factures)';

--
-- Déclencheurs `ventes_details`
--
DELIMITER $$
CREATE TRIGGER `after_vente_detail_insert` AFTER INSERT ON `ventes_details` FOR EACH ROW BEGIN
    -- Diminuer le stock du produit
    UPDATE produits 
    SET quantite_stock = quantite_stock - NEW.quantite,
        date_derniere_vente = NOW(),
        nombre_ventes = nombre_ventes + NEW.quantite
    WHERE id_produit = NEW.id_produit;
    
    -- Créer une notification si stock faible
    IF (SELECT quantite_stock FROM produits WHERE id_produit = NEW.id_produit) <= 
       (SELECT seuil_critique FROM produits WHERE id_produit = NEW.id_produit) THEN
        INSERT INTO notifications (type_notification, titre, message, id_produit, niveau_urgence)
        SELECT 'stock_critique', 
               CONCAT('Stock critique: ', nom_produit),
               CONCAT('Le stock de ', nom_produit, ' est critique (', quantite_stock, ' restant)'),
               id_produit,
               'urgent'
        FROM produits WHERE id_produit = NEW.id_produit;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_produits_alertes`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vue_produits_alertes` (
`id_produit` int(11)
,`nom_produit` varchar(255)
,`code_produit` varchar(100)
,`nom_categorie` varchar(255)
,`quantite_stock` int(11)
,`seuil_alerte` int(11)
,`seuil_critique` int(11)
,`niveau_alerte` varchar(8)
,`date_entree` date
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_statistiques_ventes`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vue_statistiques_ventes` (
`date_vente` date
,`nombre_ventes` bigint(21)
,`chiffre_affaires` decimal(37,2)
,`benefice_total` decimal(37,2)
,`vendeur` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure de la vue `vue_produits_alertes`
--
DROP TABLE IF EXISTS `vue_produits_alertes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_produits_alertes`  AS SELECT `p`.`id_produit` AS `id_produit`, `p`.`nom_produit` AS `nom_produit`, `p`.`code_produit` AS `code_produit`, `c`.`nom_categorie` AS `nom_categorie`, `p`.`quantite_stock` AS `quantite_stock`, `p`.`seuil_alerte` AS `seuil_alerte`, `p`.`seuil_critique` AS `seuil_critique`, CASE WHEN `p`.`quantite_stock` = 0 THEN 'rupture' WHEN `p`.`quantite_stock` <= `p`.`seuil_critique` THEN 'critique' WHEN `p`.`quantite_stock` <= `p`.`seuil_alerte` THEN 'faible' ELSE 'normal' END AS `niveau_alerte`, `p`.`date_entree` AS `date_entree` FROM (`produits` `p` left join `categories` `c` on(`p`.`id_categorie` = `c`.`id_categorie`)) WHERE `p`.`est_actif` = 1 AND `p`.`quantite_stock` <= `p`.`seuil_alerte` ;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_statistiques_ventes`
--
DROP TABLE IF EXISTS `vue_statistiques_ventes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_statistiques_ventes`  AS SELECT cast(`v`.`date_vente` as date) AS `date_vente`, count(`v`.`id_vente`) AS `nombre_ventes`, sum(`v`.`montant_total`) AS `chiffre_affaires`, sum(`vd`.`benefice_ligne`) AS `benefice_total`, `u`.`nom_complet` AS `vendeur` FROM ((`ventes` `v` left join `ventes_details` `vd` on(`v`.`id_vente` = `vd`.`id_vente`)) left join `utilisateurs` `u` on(`v`.`id_vendeur` = `u`.`id_utilisateur`)) WHERE `v`.`statut` = 'validee' GROUP BY cast(`v`.`date_vente` as date), `u`.`id_utilisateur` ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`),
  ADD KEY `idx_actif` (`est_actif`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`),
  ADD KEY `idx_telephone` (`telephone`);

--
-- Index pour la table `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`id_config`);

--
-- Index pour la table `details_vente`
--
ALTER TABLE `details_vente`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_vente` (`id_vente`),
  ADD KEY `idx_produit` (`id_produit`);

--
-- Index pour la table `logs_activites`
--
ALTER TABLE `logs_activites`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_type` (`type_action`),
  ADD KEY `idx_date` (`date_action`);

--
-- Index pour la table `mouvements`
--
ALTER TABLE `mouvements`
  ADD PRIMARY KEY (`id_mouvement`),
  ADD KEY `idx_produit` (`id_produit`),
  ADD KEY `idx_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_date` (`date_mouvement`);

--
-- Index pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  ADD PRIMARY KEY (`id_mouvement`),
  ADD KEY `idx_produit` (`id_produit`),
  ADD KEY `idx_type` (`type_mouvement`),
  ADD KEY `idx_date` (`date_mouvement`),
  ADD KEY `fk_mouvement_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `idx_lue` (`est_lue`),
  ADD KEY `idx_type` (`type_notification`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `code_produit` (`code_produit`),
  ADD KEY `idx_categorie` (`id_categorie`),
  ADD KEY `idx_stock` (`quantite_stock`),
  ADD KEY `idx_code` (`code_produit`),
  ADD KEY `idx_actif` (`est_actif`),
  ADD KEY `idx_produits_stock_actif` (`quantite_stock`,`est_actif`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `idx_login` (`login`),
  ADD KEY `idx_niveau` (`niveau_acces`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id_vente`),
  ADD UNIQUE KEY `numero_facture` (`numero_facture`),
  ADD UNIQUE KEY `unique_numero_facture` (`numero_facture`),
  ADD KEY `idx_client` (`id_client`),
  ADD KEY `idx_vendeur` (`id_vendeur`),
  ADD KEY `idx_date` (`date_vente`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_ventes_date_statut` (`date_vente`,`statut`);

--
-- Index pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_vente` (`id_vente`),
  ADD KEY `idx_produit` (`id_produit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `configuration`
--
ALTER TABLE `configuration`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `details_vente`
--
ALTER TABLE `details_vente`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `logs_activites`
--
ALTER TABLE `logs_activites`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `mouvements`
--
ALTER TABLE `mouvements`
  MODIFY `id_mouvement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  MODIFY `id_mouvement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id_vente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `details_vente`
--
ALTER TABLE `details_vente`
  ADD CONSTRAINT `fk_detail_produit_new` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  ADD CONSTRAINT `fk_detail_vente_new` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mouvements`
--
ALTER TABLE `mouvements`
  ADD CONSTRAINT `fk_mouvement_produit_new` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  ADD CONSTRAINT `fk_mouvement_utilisateur_new` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  ADD CONSTRAINT `fk_mouvement_produit` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  ADD CONSTRAINT `fk_mouvement_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE SET NULL;

--
-- Contraintes pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD CONSTRAINT `fk_vente_client` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vente_vendeur` FOREIGN KEY (`id_vendeur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  ADD CONSTRAINT `fk_detail_produit` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  ADD CONSTRAINT `fk_detail_vente` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
