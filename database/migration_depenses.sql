-- Migration : Table depenses (si non existante)
CREATE TABLE IF NOT EXISTS `depenses` (
  `id_depense` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_depense` date NOT NULL DEFAULT (CURRENT_DATE),
  `categorie` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cree_par` int DEFAULT NULL,
  `cree_le` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_depense`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dépenses et opérations comptables';
