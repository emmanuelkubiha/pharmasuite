-- Migration pour créer la table mouvements_stock si elle n'existe pas

CREATE TABLE IF NOT EXISTS `mouvements_stock` (
  `id_mouvement` int(11) NOT NULL AUTO_INCREMENT,
  `id_produit` int(11) NOT NULL,
  `type_mouvement` enum('entree','sortie','ajustement') NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `stock_avant` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_apres` decimal(10,2) NOT NULL DEFAULT '0.00',
  `motif` varchar(255) DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `date_mouvement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mouvement`),
  KEY `id_produit` (`id_produit`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `date_mouvement` (`date_mouvement`),
  CONSTRAINT `mouvements_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  CONSTRAINT `mouvements_stock_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
