-- ============================================================================
-- SCRIPT DE MISE À JOUR - STORE SUITE
-- Ajouter les colonnes TVA et corriger la structure
-- ============================================================================

-- 1. Ajouter les colonnes HT et TVA à la table ventes si elles n'existent pas
ALTER TABLE ventes 
ADD COLUMN IF NOT EXISTS montant_ht DECIMAL(10,2) DEFAULT 0 AFTER montant_total,
ADD COLUMN IF NOT EXISTS montant_tva DECIMAL(10,2) DEFAULT 0 AFTER montant_ht;

-- 2. Calculer rétroactivement le HT et la TVA pour les ventes existantes (si montant_ht = 0)
-- En supposant que montant_total = TTC avec TVA 16%
UPDATE ventes 
SET 
    montant_ht = ROUND(montant_total / 1.16, 2),
    montant_tva = ROUND(montant_total - (montant_total / 1.16), 2)
WHERE montant_ht = 0 OR montant_ht IS NULL;

-- 3. Vérifier la structure de la table utilisateurs
-- La colonne doit s'appeler 'nom_complet' et pas 'nom_utilisateur'
-- La colonne mot de passe doit s'appeler 'password_hash' et pas 'password'

-- Si besoin, renommer nom_utilisateur :
-- ALTER TABLE utilisateurs CHANGE nom_utilisateur nom_complet VARCHAR(100) NOT NULL;

-- Si besoin, renommer password :
-- ALTER TABLE utilisateurs CHANGE password password_hash VARCHAR(255) NOT NULL;

-- Vérifier si les colonnes existent et les créer/renommer si nécessaire
-- Si la colonne s'appelle 'password', la renommer en 'password_hash'
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'utilisateurs' 
    AND COLUMN_NAME = 'password');

SET @sql = IF(@col_exists > 0, 
    'ALTER TABLE utilisateurs CHANGE password password_hash VARCHAR(255) NOT NULL',
    'SELECT "Colonne password_hash déjà correcte" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Créer la table details_vente si elle n'existe pas (alias de ventes_details)
CREATE TABLE IF NOT EXISTS `details_vente` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `id_vente` INT(11) NOT NULL COMMENT 'Référence à la vente',
  `id_produit` INT(11) NOT NULL COMMENT 'Produit vendu',
  `nom_produit` VARCHAR(255) NOT NULL COMMENT 'Nom du produit',
  `quantite` INT(11) NOT NULL DEFAULT 1 COMMENT 'Quantité vendue',
  `prix_unitaire` DECIMAL(15,2) NOT NULL COMMENT 'Prix unitaire',
  `prix_achat_unitaire` DECIMAL(15,2) NOT NULL COMMENT 'Prix achat',
  `prix_total` DECIMAL(15,2) NOT NULL COMMENT 'Total ligne',
  `benefice_ligne` DECIMAL(15,2) NOT NULL COMMENT 'Bénéfice ligne',
  `remise_ligne` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Remise ligne',
  `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  KEY `idx_vente` (`id_vente`),
  KEY `idx_produit` (`id_produit`),
  CONSTRAINT `fk_detail_vente_new` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_produit_new` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Créer la table mouvements si elle n'existe pas (alias de mouvements_stock)
CREATE TABLE IF NOT EXISTS `mouvements` (
  `id_mouvement` INT(11) NOT NULL AUTO_INCREMENT,
  `id_produit` INT(11) NOT NULL,
  `id_utilisateur` INT(11) NOT NULL,
  `type_mouvement` ENUM('entree','sortie','ajustement','vente') NOT NULL,
  `quantite` INT(11) NOT NULL,
  `prix_unitaire` DECIMAL(15,2) DEFAULT NULL,
  `reference` VARCHAR(100) DEFAULT NULL COMMENT 'Numéro facture ou bon',
  `motif` TEXT DEFAULT NULL,
  `date_mouvement` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mouvement`),
  KEY `idx_produit` (`id_produit`),
  KEY `idx_utilisateur` (`id_utilisateur`),
  KEY `idx_date` (`date_mouvement`),
  CONSTRAINT `fk_mouvement_produit_new` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`),
  CONSTRAINT `fk_mouvement_utilisateur_new` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Afficher la structure finale
SELECT 'Migration terminée avec succès !' as message;
