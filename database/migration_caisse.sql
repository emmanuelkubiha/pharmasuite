CREATE TABLE IF NOT EXISTS caisse_mouvements (
    id_mouvement INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20) NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    mode_paiement VARCHAR(20) NOT NULL DEFAULT 'Cash',
    date_mouvement DATE NOT NULL,
    motif VARCHAR(255),
    utilisateur INT NOT NULL,
    cree_le DATETIME NOT NULL,
    INDEX(date_mouvement),
    INDEX(utilisateur)
);
