-- MIGRATION SQL : Adaptation PharmaSuite (ajouts sans renommage)
-- À exécuter sur la base existante storesuite (devenue pharmasuite)
ALTER TABLE produits
  ADD COLUMN dosage VARCHAR(100) DEFAULT NULL AFTER unite_mesure,
  ADD COLUMN conditionnement VARCHAR(100) DEFAULT NULL AFTER dosage,
  ADD COLUMN date_peremption DATE DEFAULT NULL AFTER conditionnement,
  ADD COLUMN fabriquant VARCHAR(255) DEFAULT NULL AFTER date_peremption;
-- Vue pour le suivi des péremptions (alerte sur lots proches ou dépassant la date de péremption)
CREATE OR REPLACE VIEW vue_peremptions AS
SELECT l.id_lot, p.nom_produit, l.numero_lot, l.date_peremption, l.quantite,
  CASE 
    WHEN l.date_peremption < CURDATE() THEN 'expiré'
    WHEN l.date_peremption <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'alerte_grave'
    WHEN l.date_peremption <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) THEN 'alerte'
    ELSE 'ok'
  END AS statut_peremption
FROM lots_medicaments l
JOIN produits p ON l.id_produit = p.id_produit
ORDER BY l.date_peremption ASC;
  date_entree DATE DEFAULT NULL,
  FOREIGN KEY (id_produit) REFERENCES produits(id_produit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ajout du champ id_lot dans ventes_details et mouvements_stock
ALTER TABLE ventes_details ADD COLUMN id_lot INT DEFAULT NULL AFTER id_produit;
ALTER TABLE mouvements_stock ADD COLUMN id_lot INT DEFAULT NULL AFTER id_produit;

-- 4. Création des tables caisses et depenses
CREATE TABLE IF NOT EXISTS caisses (
  id_caisse INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  solde_initial DECIMAL(10,2) DEFAULT 0.00,
  solde_actuel DECIMAL(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS depenses (
  id_depense INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  date_depense DATETIME DEFAULT CURRENT_TIMESTAMP,
  montant DECIMAL(10,2) NOT NULL,
  motif VARCHAR(255) NOT NULL,
  utilisateur_id INT NOT NULL,
  caisse_id INT DEFAULT NULL,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur),
  FOREIGN KEY (caisse_id) REFERENCES caisses(id_caisse)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Vue pour le suivi des péremptions (optionnel)
CREATE OR REPLACE VIEW vue_peremptions AS
SELECT l.id_lot, p.nom_produit, l.numero_lot, l.date_peremption, l.quantite
FROM lots_medicaments l
JOIN produits p ON l.id_produit = p.id_produit
WHERE l.date_peremption <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
ORDER BY l.date_peremption ASC;

-- FIN MIGRATION