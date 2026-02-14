<?php
/**
 * IMPORT EXCEL - PHARMA SUITE
 * Import des données depuis un fichier Excel
 */

require_once __DIR__ . '/../protection_pages.php';
require_admin(); // Seuls les admins peuvent importer

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'stats' => []];

try {
    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['fichier_excel']) || $_FILES['fichier_excel']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Aucun fichier reçu ou erreur lors de l\'upload');
    }
    
    $fichier = $_FILES['fichier_excel'];
    $type_import = $_POST['type_import'] ?? 'produits';
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
        throw new Exception('Format de fichier non supporté. Utilisez .xlsx, .xls ou .csv');
    }
    
    // Charger le fichier avec une bibliothèque simple (on va lire directement)
    $data = [];
    
    if ($extension === 'csv') {
        // Lire le CSV
        $handle = fopen($fichier['tmp_name'], 'r');
        if ($handle === false) {
            throw new Exception('Impossible de lire le fichier CSV');
        }
        
        // Détecter le séparateur (virgule ou point-virgule)
        $first_line = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($first_line, ';') > substr_count($first_line, ',')) ? ';' : ',';
        
        $headers = fgetcsv($handle, 0, $delimiter); // Première ligne = en-têtes
        if ($headers === false) {
            throw new Exception('Fichier CSV vide ou mal formaté');
        }
        
        // Nettoyer les en-têtes (enlever BOM et espaces)
        $headers = array_map(function($h) {
            return trim(str_replace("\xEF\xBB\xBF", '', $h));
        }, $headers);
        
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) > 0 && !empty($row[0]) && trim($row[0]) !== '') {
                // Combiner en-têtes et valeurs
                $combined = [];
                for ($i = 0; $i < count($headers); $i++) {
                    $combined[$headers[$i]] = isset($row[$i]) ? trim($row[$i]) : '';
                }
                $data[] = $combined;
            }
        }
        fclose($handle);
    } else {
        // Pour Excel, on utilise une approche simple avec une bibliothèque PHP
        // Si PhpSpreadsheet n'est pas installée, on demande un export en CSV
        if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new Exception('Bibliothèque PhpSpreadsheet non installée. Veuillez convertir votre fichier en CSV (.csv) et réessayer.');
        }
        
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            throw new Exception('Le fichier Excel est vide');
        }
        
        $headers = array_shift($rows); // Première ligne = en-têtes
        
        foreach ($rows as $row) {
            if (count($row) > 0 && !empty($row[0])) {
                $data[] = array_combine($headers, $row);
            }
        }
    }
    
    if (empty($data)) {
        throw new Exception('Aucune donnée trouvée dans le fichier');
    }
    
    // Traiter l'import selon le type
    switch ($type_import) {
        case 'produits':
            $stats = importerProduits($data);
            break;
        case 'clients':
            $stats = importerClients($data);
            break;
        case 'fournisseurs':
            $stats = importerFournisseurs($data);
            break;
        default:
            throw new Exception('Type d\'import non supporté');
    }
    
    $response = [
        'success' => true,
        'message' => "Import réussi ! {$stats['ajoutes']} ajoutés, {$stats['mis_a_jour']} mis à jour, {$stats['erreurs']} erreurs.",
        'stats' => $stats
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

/**
 * Importer des produits depuis les données
 */
function importerProduits($data) {
    global $pdo;
    
    $stats = [
        'ajoutes' => 0,
        'mis_a_jour' => 0,
        'erreurs' => 0,
        'details_erreurs' => []
    ];
    
    db_begin_transaction();
    
    try {
        foreach ($data as $index => $row) {
            try {
                $ligne = $index + 2; // +2 car ligne 1 = en-têtes, et index commence à 0
                
                // Récupérer les données (avec fallback pour différents noms de colonnes)
                $nom_produit = trim($row['nom_produit'] ?? $row['Nom'] ?? $row['nom'] ?? '');
                $code_barre = trim($row['code_barre'] ?? $row['Code'] ?? $row['code'] ?? '');
                $categorie_nom = trim($row['categorie'] ?? $row['Catégorie'] ?? $row['categorie_nom'] ?? '');
                $prix_achat = floatval($row['prix_achat'] ?? $row['Prix Achat'] ?? $row['prix achat'] ?? 0);
                $prix_vente = floatval($row['prix_vente'] ?? $row['Prix Vente'] ?? $row['prix vente'] ?? 0);
                $quantite = intval($row['quantite_stock'] ?? $row['Quantité'] ?? $row['quantite'] ?? $row['Stock'] ?? 0);
                $seuil_alerte = intval($row['seuil_alerte'] ?? $row['Seuil'] ?? $row['seuil'] ?? 10);
                
                // Validation
                if (empty($nom_produit)) {
                    throw new Exception("Ligne $ligne : Nom du produit manquant");
                }
                
                if ($prix_vente <= 0) {
                    throw new Exception("Ligne $ligne : Prix de vente invalide");
                }
                
                // Trouver ou créer la catégorie
                $id_categorie = 1; // Catégorie par défaut
                if (!empty($categorie_nom)) {
                    $cat = db_fetch_one("SELECT id_categorie FROM categories WHERE nom_categorie = ?", [$categorie_nom]);
                    if ($cat) {
                        $id_categorie = $cat['id_categorie'];
                    } else {
                        // Créer la catégorie
                        db_execute("INSERT INTO categories (nom_categorie, description) VALUES (?, ?)", [$categorie_nom, 'Créée par import']);
                        $id_categorie = $pdo->lastInsertId();
                    }
                }
                
                // Vérifier si le produit existe déjà
                $produit_existant = null;
                if (!empty($code_barre)) {
                    $produit_existant = db_fetch_one("SELECT id_produit FROM produits WHERE code_barre = ?", [$code_barre]);
                } else {
                    $produit_existant = db_fetch_one("SELECT id_produit FROM produits WHERE nom_produit = ?", [$nom_produit]);
                }
                
                if ($produit_existant) {
                    // Mise à jour
                    db_execute("
                        UPDATE produits SET
                            nom_produit = ?,
                            id_categorie = ?,
                            prix_achat = ?,
                            prix_vente = ?,
                            quantite_stock = quantite_stock + ?,
                            seuil_alerte = ?
                        WHERE id_produit = ?
                    ", [$nom_produit, $id_categorie, $prix_achat, $prix_vente, $quantite, $seuil_alerte, $produit_existant['id_produit']]);
                    
                    // Enregistrer mouvement de stock
                    if ($quantite > 0) {
                        db_execute("
                            INSERT INTO mouvements_stock (id_produit, type_mouvement, quantite, motif, date_mouvement)
                            VALUES (?, 'entree', ?, 'Import Excel', NOW())
                        ", [$produit_existant['id_produit'], $quantite]);
                    }
                    
                    $stats['mis_a_jour']++;
                } else {
                    // Insertion
                    db_execute("
                        INSERT INTO produits (
                            nom_produit, code_barre, id_categorie, 
                            prix_achat, prix_vente, quantite_stock, 
                            seuil_alerte, est_actif, date_creation
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())
                    ", [$nom_produit, $code_barre, $id_categorie, $prix_achat, $prix_vente, $quantite, $seuil_alerte]);
                    
                    $id_produit = $pdo->lastInsertId();
                    
                    // Enregistrer mouvement de stock initial
                    if ($quantite > 0) {
                        db_execute("
                            INSERT INTO mouvements_stock (id_produit, type_mouvement, quantite, motif, date_mouvement)
                            VALUES (?, 'entree', ?, 'Création par import Excel', NOW())
                        ", [$id_produit, $quantite]);
                    }
                    
                    $stats['ajoutes']++;
                }
                
            } catch (Exception $e) {
                $stats['erreurs']++;
                $stats['details_erreurs'][] = $e->getMessage();
            }
        }
        
        db_commit();
        
    } catch (Exception $e) {
        db_rollback();
        throw $e;
    }
    
    return $stats;
}

/**
 * Importer des clients
 */
function importerClients($data) {
    global $pdo;
    
    $stats = [
        'ajoutes' => 0,
        'mis_a_jour' => 0,
        'erreurs' => 0,
        'details_erreurs' => []
    ];
    
    db_begin_transaction();
    
    try {
        foreach ($data as $index => $row) {
            try {
                $ligne = $index + 2;
                
                $nom_client = trim($row['nom_client'] ?? $row['Nom'] ?? $row['nom'] ?? '');
                $telephone = trim($row['telephone'] ?? $row['Téléphone'] ?? $row['tel'] ?? '');
                $email = trim($row['email'] ?? $row['Email'] ?? '');
                $adresse = trim($row['adresse'] ?? $row['Adresse'] ?? '');
                
                if (empty($nom_client)) {
                    throw new Exception("Ligne $ligne : Nom du client manquant");
                }
                
                // Vérifier si le client existe
                $client_existant = null;
                if (!empty($telephone)) {
                    $client_existant = db_fetch_one("SELECT id_client FROM clients WHERE telephone = ?", [$telephone]);
                } else {
                    $client_existant = db_fetch_one("SELECT id_client FROM clients WHERE nom_client = ?", [$nom_client]);
                }
                
                if ($client_existant) {
                    db_execute("
                        UPDATE clients SET nom_client = ?, email = ?, adresse = ?
                        WHERE id_client = ?
                    ", [$nom_client, $email, $adresse, $client_existant['id_client']]);
                    $stats['mis_a_jour']++;
                } else {
                    db_execute("
                        INSERT INTO clients (nom_client, telephone, email, adresse, date_creation)
                        VALUES (?, ?, ?, ?, NOW())
                    ", [$nom_client, $telephone, $email, $adresse]);
                    $stats['ajoutes']++;
                }
                
            } catch (Exception $e) {
                $stats['erreurs']++;
                $stats['details_erreurs'][] = $e->getMessage();
            }
        }
        
        db_commit();
        
    } catch (Exception $e) {
        db_rollback();
        throw $e;
    }
    
    return $stats;
}

/**
 * Importer des fournisseurs
 */
function importerFournisseurs($data) {
    global $pdo;
    
    $stats = [
        'ajoutes' => 0,
        'mis_a_jour' => 0,
        'erreurs' => 0,
        'details_erreurs' => []
    ];
    
    db_begin_transaction();
    
    try {
        foreach ($data as $index => $row) {
            try {
                $ligne = $index + 2;
                
                $nom_fournisseur = trim($row['nom_fournisseur'] ?? $row['Nom'] ?? $row['nom'] ?? '');
                $telephone = trim($row['telephone'] ?? $row['Téléphone'] ?? $row['tel'] ?? '');
                $email = trim($row['email'] ?? $row['Email'] ?? '');
                $adresse = trim($row['adresse'] ?? $row['Adresse'] ?? '');
                
                if (empty($nom_fournisseur)) {
                    throw new Exception("Ligne $ligne : Nom du fournisseur manquant");
                }
                
                // Vérifier si le fournisseur existe
                $fournisseur_existant = null;
                if (!empty($telephone)) {
                    $fournisseur_existant = db_fetch_one("SELECT id_fournisseur FROM fournisseurs WHERE telephone = ?", [$telephone]);
                } else {
                    $fournisseur_existant = db_fetch_one("SELECT id_fournisseur FROM fournisseurs WHERE nom_fournisseur = ?", [$nom_fournisseur]);
                }
                
                if ($fournisseur_existant) {
                    db_execute("
                        UPDATE fournisseurs SET nom_fournisseur = ?, email = ?, adresse = ?
                        WHERE id_fournisseur = ?
                    ", [$nom_fournisseur, $email, $adresse, $fournisseur_existant['id_fournisseur']]);
                    $stats['mis_a_jour']++;
                } else {
                    db_execute("
                        INSERT INTO fournisseurs (nom_fournisseur, telephone, email, adresse)
                        VALUES (?, ?, ?, ?)
                    ", [$nom_fournisseur, $telephone, $email, $adresse]);
                    $stats['ajoutes']++;
                }
                
            } catch (Exception $e) {
                $stats['erreurs']++;
                $stats['details_erreurs'][] = $e->getMessage();
            }
        }
        
        db_commit();
        
    } catch (Exception $e) {
        db_rollback();
        throw $e;
    }
    
    return $stats;
}
