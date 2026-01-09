-- ============================================================================
-- STORESUITE - SYSTÈME DE GESTION DE STOCK AVEC FACTURATION
-- ============================================================================
-- Date de création : 8 janvier 2026
-- Nom du système : STORESUITE
-- Description : Base de données complète pour un système de gestion de stock
--               avec facturation, multi-utilisateurs et rapports avancés
-- 
-- Fonctionnalités principales :
-- - Configuration personnalisée (logo, couleurs, informations entreprise)
-- - Gestion multi-utilisateurs avec niveaux de permissions
-- - Gestion des produits par catégories avec alertes de stock
-- - Système de vente avec prix flexibles
-- - Historique complet des opérations
-- - Rapports et statistiques selon le niveau utilisateur
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Encodage UTF-8 pour support complet des caractères
SET NAMES utf8mb4;

-- ============================================================================
-- CRÉATION DE LA BASE DE DONNÉES STORESUITE
-- ============================================================================
DROP DATABASE IF EXISTS `storesuite`;
CREATE DATABASE `storesuite` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `storesuite`;

-- ============================================================================
-- TABLE : configuration
-- Description : Stocke les paramètres globaux du système (nom, logo, couleurs)
--               Cette table ne contient qu'un seul enregistrement
-- Importance : Permet la personnalisation complète du système pour chaque client
-- ============================================================================
CREATE TABLE `configuration` (
  `id_config` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_boutique` VARCHAR(255) NOT NULL COMMENT 'Nom de la boutique/entreprise',
  `slogan` VARCHAR(255) DEFAULT NULL COMMENT 'Slogan ou description courte',
  `logo` VARCHAR(255) DEFAULT NULL COMMENT 'Chemin vers le fichier logo',
  `couleur_primaire` VARCHAR(7) DEFAULT '#206bc4' COMMENT 'Couleur principale (format HEX)',
  `couleur_secondaire` VARCHAR(7) DEFAULT '#1a7f5a' COMMENT 'Couleur secondaire (format HEX)',
  `adresse` TEXT DEFAULT NULL COMMENT 'Adresse complète de l\'entreprise',
  `telephone` VARCHAR(100) DEFAULT NULL COMMENT 'Numéro(s) de téléphone',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Adresse email',
  `site_web` VARCHAR(255) DEFAULT NULL COMMENT 'Site web de l\'entreprise',
  `num_registre_commerce` VARCHAR(100) DEFAULT NULL COMMENT 'Numéro d\'enregistrement (RCCM, etc.)',
  `num_impot` VARCHAR(100) DEFAULT NULL COMMENT 'Numéro fiscal/TVA',
  `devise` VARCHAR(10) DEFAULT 'CDF' COMMENT 'Symbole de la devise utilisée (CDF ou $)',
  `taux_tva` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taux de TVA par défaut (%)',
  `fuseau_horaire` VARCHAR(50) DEFAULT 'Africa/Lubumbashi' COMMENT 'Fuseau horaire',
  `langue` VARCHAR(10) DEFAULT 'fr' COMMENT 'Langue du système (fr, en, etc.)',
  `est_configure` TINYINT(1) DEFAULT 0 COMMENT '0=Non configuré, 1=Configuré',
  `date_configuration` DATETIME DEFAULT NULL COMMENT 'Date de première configuration',
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Paramètres globaux du système';

-- Insertion d'un enregistrement vide pour le premier démarrage
INSERT INTO `configuration` (`id_config`, `est_configure`) VALUES (1, 0);

-- ============================================================================
-- TABLE : utilisateurs
-- Description : Gère tous les utilisateurs du système (administrateurs et vendeurs)
-- Importance : Système de permissions pour séparer admin (voit tout) et vendeurs
-- Niveaux : 1=Administrateur (accès complet), 2=Vendeur/Caissier (accès limité)
-- ============================================================================
CREATE TABLE `utilisateurs` (
  `id_utilisateur` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_complet` VARCHAR(255) NOT NULL COMMENT 'Nom complet de l\'utilisateur',
  `login` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Identifiant de connexion (unique)',
  `mot_de_passe` VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé (password_hash)',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Adresse email',
  `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Numéro de téléphone',
  `niveau_acces` TINYINT(1) NOT NULL DEFAULT 2 COMMENT '1=Admin, 2=Vendeur',
  `photo` VARCHAR(255) DEFAULT NULL COMMENT 'Photo de profil',
  `est_actif` TINYINT(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du compte',
  `date_derniere_connexion` DATETIME DEFAULT NULL COMMENT 'Dernière connexion',
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  KEY `idx_login` (`login`),
  KEY `idx_niveau` (`niveau_acces`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utilisateurs du système avec permissions';

-- Création du compte administrateur par défaut (mot de passe: admin123)
INSERT INTO `utilisateurs` (`nom_complet`, `login`, `mot_de_passe`, `niveau_acces`, `est_actif`) 
VALUES ('Administrateur', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- ============================================================================
-- TABLE : categories
-- Description : Catégories de produits pour mieux organiser l'inventaire
-- Importance : Permet de classer les produits (Électronique, Électroménager, etc.)
-- ============================================================================
CREATE TABLE `categories` (
  `id_categorie` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` VARCHAR(255) NOT NULL COMMENT 'Nom de la catégorie',
  `description` TEXT DEFAULT NULL COMMENT 'Description de la catégorie',
  `icone` VARCHAR(100) DEFAULT NULL COMMENT 'Icône ou classe CSS',
  `couleur` VARCHAR(7) DEFAULT NULL COMMENT 'Couleur associée (format HEX)',
  `ordre_affichage` INT(11) DEFAULT 0 COMMENT 'Ordre d\'affichage',
  `est_actif` TINYINT(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categorie`),
  KEY `idx_actif` (`est_actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catégories de produits';

-- Insertion de catégories par défaut
INSERT INTO `categories` (`nom_categorie`, `description`, `icone`, `couleur`, `ordre_affichage`) VALUES
('Électronique', 'Téléphones, ordinateurs, accessoires', 'ti-device-laptop', '#3498db', 1),
('Électroménager', 'Réfrigérateurs, télévisions, cuisinières', 'ti-device-tv', '#e74c3c', 2),
('Meubles', 'Tables, chaises, armoires', 'ti-armchair', '#9b59b6', 3),
('Vêtements', 'Habits, chaussures, accessoires', 'ti-hanger', '#1abc9c', 4),
('Alimentation', 'Produits alimentaires', 'ti-shopping-cart', '#f39c12', 5);

-- ============================================================================
-- TABLE : produits
-- Description : Catalogue complet des produits en stock
-- Importance : Cœur du système - gère l'inventaire avec alertes et historique
-- ============================================================================
CREATE TABLE `produits` (
  `id_produit` INT(11) NOT NULL AUTO_INCREMENT,
  `code_produit` VARCHAR(100) DEFAULT NULL UNIQUE COMMENT 'Code/Référence unique du produit',
  `nom_produit` VARCHAR(255) NOT NULL COMMENT 'Nom du produit',
  `description` TEXT DEFAULT NULL COMMENT 'Description détaillée',
  `id_categorie` INT(11) DEFAULT NULL COMMENT 'Catégorie du produit',
  `prix_achat` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Prix d\'achat (VISIBLE ADMIN SEULEMENT)',
  `prix_vente` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Prix de vente recommandé',
  `prix_vente_min` DECIMAL(15,2) DEFAULT NULL COMMENT 'Prix de vente minimum autorisé',
  `quantite_stock` INT(11) NOT NULL DEFAULT 0 COMMENT 'Quantité actuelle en stock',
  `seuil_alerte` INT(11) DEFAULT 10 COMMENT 'Seuil pour alerte stock faible',
  `seuil_critique` INT(11) DEFAULT 5 COMMENT 'Seuil critique (alerte rouge)',
  `unite_mesure` VARCHAR(50) DEFAULT 'pièce' COMMENT 'Unité (pièce, kg, litre, etc.)',
  `image` VARCHAR(255) DEFAULT NULL COMMENT 'Image du produit',
  `code_barre` VARCHAR(100) DEFAULT NULL COMMENT 'Code-barres pour scanner',
  `emplacement` VARCHAR(255) DEFAULT NULL COMMENT 'Emplacement dans le magasin',
  `date_entree` DATE DEFAULT NULL COMMENT 'Date dernière entrée en stock',
  `date_derniere_vente` DATETIME DEFAULT NULL COMMENT 'Date de la dernière vente',
  `nombre_ventes` INT(11) DEFAULT 0 COMMENT 'Nombre total de ventes',
  `est_actif` TINYINT(1) DEFAULT 1 COMMENT '0=Inactif, 1=Actif',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produit`),
  KEY `idx_categorie` (`id_categorie`),
  KEY `idx_stock` (`quantite_stock`),
  KEY `idx_code` (`code_produit`),
  KEY `idx_actif` (`est_actif`),
  CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produits en stock avec gestion des alertes';

-- ============================================================================
-- TABLE : clients
-- Description : Base de données des clients (optionnel pour facturation)
-- Importance : Permet de garder l'historique des achats par client
-- ============================================================================
CREATE TABLE `clients` (
  `id_client` INT(11) NOT NULL AUTO_INCREMENT,
  `nom_client` VARCHAR(255) NOT NULL COMMENT 'Nom du client',
  `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Téléphone',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email',
  `adresse` TEXT DEFAULT NULL COMMENT 'Adresse complète',
  `type_client` ENUM('particulier', 'entreprise') DEFAULT 'particulier',
  `numero_fiscal` VARCHAR(100) DEFAULT NULL COMMENT 'Numéro fiscal (pour entreprises)',
  `total_achats` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total des achats',
  `nombre_achats` INT(11) DEFAULT 0 COMMENT 'Nombre d\'achats',
  `date_dernier_achat` DATETIME DEFAULT NULL,
  `notes` TEXT DEFAULT NULL COMMENT 'Notes sur le client',
  `est_actif` TINYINT(1) DEFAULT 1,
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`),
  KEY `idx_telephone` (`telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Base de données clients';

-- Client par défaut pour les ventes au comptoir
INSERT INTO `clients` (`nom_client`, `type_client`) VALUES ('Client Comptoir', 'particulier');

-- ============================================================================
-- TABLE : ventes
-- Description : En-tête des ventes (une vente peut contenir plusieurs produits)
-- Importance : Enregistre chaque transaction avec tous les détails
-- ============================================================================
CREATE TABLE `ventes` (
  `id_vente` INT(11) NOT NULL AUTO_INCREMENT,
  `numero_facture` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Numéro unique de la facture',
  `id_client` INT(11) DEFAULT NULL COMMENT 'Client (NULL = vente comptoir)',
  `id_vendeur` INT(11) NOT NULL COMMENT 'Vendeur/Caissier qui a effectué la vente',
  `montant_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant total de la vente',
  `montant_remise` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Remise accordée',
  `montant_tva` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Montant TVA',
  `montant_paye` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant payé par le client',
  `montant_rendu` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Monnaie rendue',
  `mode_paiement` ENUM('especes', 'carte', 'mobile_money', 'cheque', 'credit') DEFAULT 'especes',
  `statut` ENUM('en_cours', 'validee', 'annulee') DEFAULT 'validee',
  `notes` TEXT DEFAULT NULL COMMENT 'Notes ou observations',
  `date_vente` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure de la vente',
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vente`),
  UNIQUE KEY `unique_numero_facture` (`numero_facture`),
  KEY `idx_client` (`id_client`),
  KEY `idx_vendeur` (`id_vendeur`),
  KEY `idx_date` (`date_vente`),
  KEY `idx_statut` (`statut`),
  CONSTRAINT `fk_vente_client` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE SET NULL,
  CONSTRAINT `fk_vente_vendeur` FOREIGN KEY (`id_vendeur`) REFERENCES `utilisateurs` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='En-têtes des ventes (factures)';

-- ============================================================================
-- TABLE : ventes_details
-- Description : Détails de chaque vente (lignes de la facture)
-- Importance : Chaque ligne = un produit vendu avec quantité et prix
-- ============================================================================
CREATE TABLE `ventes_details` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_vente` INT(11) NOT NULL COMMENT 'Référence à la vente',
  `id_produit` INT(11) NOT NULL COMMENT 'Produit vendu',
  `nom_produit` VARCHAR(255) NOT NULL COMMENT 'Nom du produit (copie pour historique)',
  `quantite` INT(11) NOT NULL DEFAULT 1 COMMENT 'Quantité vendue',
  `prix_unitaire` DECIMAL(15,2) NOT NULL COMMENT 'Prix unitaire de vente',
  `prix_achat_unitaire` DECIMAL(15,2) NOT NULL COMMENT 'Prix d\'achat (pour calcul bénéfice)',
  `prix_total` DECIMAL(15,2) NOT NULL COMMENT 'Prix total de la ligne (quantité × prix)',
  `benefice_ligne` DECIMAL(15,2) NOT NULL COMMENT 'Bénéfice sur cette ligne',
  `remise_ligne` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Remise sur cette ligne',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  KEY `idx_vente` (`id_vente`),
  KEY `idx_produit` (`id_produit`),
  CONSTRAINT `fk_detail_vente` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_produit` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Détails des ventes (lignes de factures)';

-- ============================================================================
-- TABLE : mouvements_stock
-- Description : Historique de tous les mouvements de stock (entrées/sorties)
-- Importance : Traçabilité complète pour audits et inventaires
-- ============================================================================
CREATE TABLE `mouvements_stock` (
  `id_mouvement` INT(11) NOT NULL AUTO_INCREMENT,
  `id_produit` INT(11) NOT NULL COMMENT 'Produit concerné',
  `type_mouvement` ENUM('entree', 'sortie', 'ajustement', 'retour') NOT NULL,
  `quantite` INT(11) NOT NULL COMMENT 'Quantité du mouvement',
  `quantite_avant` INT(11) NOT NULL COMMENT 'Stock avant le mouvement',
  `quantite_apres` INT(11) NOT NULL COMMENT 'Stock après le mouvement',
  `id_vente` INT(11) DEFAULT NULL COMMENT 'Référence vente si sortie',
  `id_utilisateur` INT(11) NOT NULL COMMENT 'Utilisateur qui a fait l\'opération',
  `motif` VARCHAR(255) DEFAULT NULL COMMENT 'Raison du mouvement',
  `notes` TEXT DEFAULT NULL,
  `date_mouvement` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mouvement`),
  KEY `idx_produit` (`id_produit`),
  KEY `idx_type` (`type_mouvement`),
  KEY `idx_date` (`date_mouvement`),
  CONSTRAINT `fk_mouvement_produit` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  CONSTRAINT `fk_mouvement_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historique des mouvements de stock';

-- ============================================================================
-- TABLE : notifications
-- Description : Système de notifications pour alertes stock, etc.
-- Importance : Alertes en temps réel pour l'administrateur
-- ============================================================================
CREATE TABLE `notifications` (
  `id_notification` INT(11) NOT NULL AUTO_INCREMENT,
  `type_notification` ENUM('stock_faible', 'stock_critique', 'rupture_stock', 'vente_importante', 'systeme') NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `id_produit` INT(11) DEFAULT NULL COMMENT 'Produit concerné si applicable',
  `niveau_urgence` ENUM('info', 'avertissement', 'urgent') DEFAULT 'info',
  `est_lue` TINYINT(1) DEFAULT 0 COMMENT '0=Non lue, 1=Lue',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  KEY `idx_lue` (`est_lue`),
  KEY `idx_type` (`type_notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifications système';

-- ============================================================================
-- TABLE : logs_activites
-- Description : Journal de toutes les activités importantes du système
-- Importance : Sécurité et traçabilité des actions utilisateurs
-- ============================================================================
CREATE TABLE `logs_activites` (
  `id_log` INT(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT(11) DEFAULT NULL,
  `type_action` VARCHAR(100) NOT NULL COMMENT 'Type d\'action (connexion, vente, modification, etc.)',
  `description` TEXT NOT NULL COMMENT 'Description détaillée de l\'action',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Adresse IP',
  `user_agent` TEXT DEFAULT NULL COMMENT 'Navigateur/Device',
  `donnees_json` JSON DEFAULT NULL COMMENT 'Données supplémentaires en JSON',
  `date_action` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_utilisateur` (`id_utilisateur`),
  KEY `idx_type` (`type_action`),
  KEY `idx_date` (`date_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Journal des activités';

-- ============================================================================
-- VUE : vue_produits_alertes
-- Description : Vue simplifiée des produits nécessitant une alerte
-- ============================================================================
CREATE OR REPLACE VIEW `vue_produits_alertes` AS
SELECT 
    p.id_produit,
    p.nom_produit,
    p.code_produit,
    c.nom_categorie,
    p.quantite_stock,
    p.seuil_alerte,
    p.seuil_critique,
    CASE 
        WHEN p.quantite_stock = 0 THEN 'rupture'
        WHEN p.quantite_stock <= p.seuil_critique THEN 'critique'
        WHEN p.quantite_stock <= p.seuil_alerte THEN 'faible'
        ELSE 'normal'
    END AS niveau_alerte,
    p.date_entree
FROM produits p
LEFT JOIN categories c ON p.id_categorie = c.id_categorie
WHERE p.est_actif = 1 AND p.quantite_stock <= p.seuil_alerte;

-- ============================================================================
-- VUE : vue_statistiques_ventes
-- Description : Statistiques de ventes pour le dashboard
-- ============================================================================
CREATE OR REPLACE VIEW `vue_statistiques_ventes` AS
SELECT 
    DATE(v.date_vente) as date_vente,
    COUNT(v.id_vente) as nombre_ventes,
    SUM(v.montant_total) as chiffre_affaires,
    SUM(vd.benefice_ligne) as benefice_total,
    u.nom_complet as vendeur
FROM ventes v
LEFT JOIN ventes_details vd ON v.id_vente = vd.id_vente
LEFT JOIN utilisateurs u ON v.id_vendeur = u.id_utilisateur
WHERE v.statut = 'validee'
GROUP BY DATE(v.date_vente), u.id_utilisateur;

-- ============================================================================
-- TRIGGERS AUTOMATIQUES
-- ============================================================================

-- Trigger : Mise à jour du stock après une vente
DELIMITER $$
CREATE TRIGGER `after_vente_detail_insert` AFTER INSERT ON `ventes_details`
FOR EACH ROW
BEGIN
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
END$$
DELIMITER ;

-- Trigger : Génération automatique du numéro de facture
DELIMITER $$
CREATE TRIGGER `before_vente_insert` BEFORE INSERT ON `ventes`
FOR EACH ROW
BEGIN
    IF NEW.numero_facture IS NULL OR NEW.numero_facture = '' THEN
        SET NEW.numero_facture = CONCAT('FAC', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD((SELECT COALESCE(MAX(id_vente), 0) + 1 FROM ventes), 6, '0'));
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- INDEX POUR OPTIMISATION DES PERFORMANCES
-- ============================================================================
CREATE INDEX idx_ventes_date_statut ON ventes(date_vente, statut);
CREATE INDEX idx_produits_stock_actif ON produits(quantite_stock, est_actif);

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================
