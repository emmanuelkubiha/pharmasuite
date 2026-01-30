
<?php
/**
 * AJAX - GESTION DES PRODUITS
 * Ajouter, modifier, supprimer des produits
 */
require_once '../protection_pages.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'get_product':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            if (!$id_produit) {
                throw new Exception('ID produit manquant');
            }
            $produit = db_fetch_one("SELECT id_produit, nom_produit, id_categorie, id_fournisseur_principal, prix_achat, prix_vente, quantite_stock, seuil_alerte, description, code_produit, code_barre, unite_mesure, dosage, conditionnement, date_peremption, est_actif FROM produits WHERE id_produit = ?", [$id_produit]);
            if (!$produit) {
                throw new Exception('Produit introuvable');
            }
            // Récupérer le dernier mouvement de stock pour ce produit (pour avoir le lot utilisé)
            $dernier_mvt = db_fetch_one("SELECT id_lot FROM mouvements_stock WHERE id_produit = ? AND id_lot IS NOT NULL ORDER BY date_mouvement DESC, id_mouvement DESC LIMIT 1", [$id_produit]);
            $numero_lot = '';
            if ($dernier_mvt && !empty($dernier_mvt['id_lot'])) {
                $lot = db_fetch_one("SELECT numero_lot FROM lots_medicaments WHERE id_lot = ?", [$dernier_mvt['id_lot']]);
                $numero_lot = $lot['numero_lot'] ?? '';
            } else {
                // Si aucun mouvement_stock avec lot, prendre le dernier lot créé pour ce produit
                $lot = db_fetch_one("SELECT numero_lot FROM lots_medicaments WHERE id_produit = ? ORDER BY id_lot DESC LIMIT 1", [$id_produit]);
                $numero_lot = $lot['numero_lot'] ?? '';
            }
            $produit['numero_lot'] = $numero_lot;

            // Récupérer le dépôt initial (le premier créé pour ce produit)
            $depot = db_fetch_one("SELECT d.id_depot, d.nom_depot FROM stock_par_depot spd JOIN depots d ON spd.id_depot = d.id_depot WHERE spd.id_produit = ? ORDER BY spd.id_depot ASC LIMIT 1", [$id_produit]);
            $produit['id_depot'] = $depot['id_depot'] ?? '';
            $produit['nom_depot'] = $depot['nom_depot'] ?? '';
            $response['success'] = true;
            $response['produit'] = $produit;
            break;
        case 'add_product':
            $nom = trim($_POST['product_name'] ?? $_POST['nom_produit'] ?? '');
            $id_categorie = $_POST['product_category'] ?? $_POST['id_categorie'] ?? null;
            $id_fournisseur = $_POST['product_fournisseur'] ?? null;
            $id_depot = $_POST['product_depot'] ?? null;
            $prix_achat = floatval($_POST['product_purchase_price'] ?? $_POST['prix_achat'] ?? 0);
            $prix_vente = floatval($_POST['product_sale_price'] ?? $_POST['prix_vente'] ?? 0);
            $quantite_stock = intval($_POST['product_stock'] ?? $_POST['quantite_stock'] ?? 0);
            $seuil_alerte = intval($_POST['product_min_stock'] ?? $_POST['seuil_alerte'] ?? 5);
            $description = trim($_POST['product_description'] ?? $_POST['description'] ?? '');
            $code_barcode = trim($_POST['product_barcode'] ?? '');
            // Correction : si code_barcode est vide, on passe NULL pour code_produit
            $code_produit = $code_barcode !== '' ? $code_barcode : null;
            $unite_mesure = trim($_POST['product_unit'] ?? 'pièce');
            $dosage = trim($_POST['product_dosage'] ?? $_POST['dosage'] ?? '');
            $conditionnement = trim($_POST['product_conditionnement'] ?? $_POST['conditionnement'] ?? '');
            $date_peremption = trim($_POST['product_peremption'] ?? $_POST['date_peremption'] ?? '');
            
            if (empty($nom)) {
                throw new Exception('Le nom du produit est obligatoire');
            }
            
            if ($prix_vente <= 0) {
                throw new Exception('Le prix de vente doit être supérieur à 0');
            }
            
            if (empty($id_fournisseur)) {
                throw new Exception('Le fournisseur est obligatoire');
            }
            
            if (empty($id_depot)) {
                throw new Exception('Le dépôt initial est obligatoire');
            }
            
            db_begin_transaction();
            try {
                $sql = "INSERT INTO produits (nom_produit, id_categorie, id_fournisseur_principal, prix_achat, prix_vente, quantite_stock, seuil_alerte, seuil_critique, description, code_produit, unite_mesure, dosage, conditionnement, date_peremption, est_actif) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                db_execute($sql, [$nom, $id_categorie, $id_fournisseur, $prix_achat, $prix_vente, $quantite_stock, $seuil_alerte, intval($seuil_alerte/2), $description, $code_produit, $unite_mesure, $dosage, $conditionnement, $date_peremption]);
                $id_produit = $pdo->lastInsertId();

                // Création du lot si info disponible (généré ou transmis)
                $numero_lot = $_POST['product_lot'] ?? null;
                if (!$numero_lot) {
                    // Générer un lot si vide
                    $numero_lot = 'L'.date('Ymd').'-'.rand(100,999);
                }
                // On n'ajoute le lot que si une date de péremption est fournie (sinon lot inutile)
                if ($numero_lot && $date_peremption) {
                    $sql_lot = "INSERT INTO lots_medicaments (id_produit, numero_lot, date_peremption, quantite, date_entree) VALUES (?, ?, ?, ?, NOW())";
                    db_execute($sql_lot, [$id_produit, $numero_lot, $date_peremption, $quantite_stock]);
                }

                // Créer l'entrée stock_par_depot
                $sql_stock = "INSERT INTO stock_par_depot (id_produit, id_depot, quantite, seuil_alerte) 
                              VALUES (?, ?, ?, ?)";
                db_execute($sql_stock, [$id_produit, $id_depot, $quantite_stock, $seuil_alerte]);
                // Enregistrer le mouvement de stock initial si quantité > 0
                if ($quantite_stock > 0) {
                    $sql_mvt = "INSERT INTO mouvements_stock (id_produit, type_mouvement, quantite, quantite_avant, quantite_apres, id_depot_source, id_fournisseur, id_utilisateur, motif, date_mouvement) 
                                VALUES (?, 'entree', ?, 0, ?, ?, ?, ?, 'Stock initial lors de la création du produit', NOW())";
                    db_execute($sql_mvt, [$id_produit, $quantite_stock, $quantite_stock, $id_depot, $id_fournisseur, $user_id]);
                }
                db_commit();
                $response['success'] = true;
                $response['message'] = 'Produit ajouté avec succès';
                $response['id_produit'] = $id_produit;
            } catch (Exception $e) {
                db_rollback();
                throw $e;
            }
            break;
            
        case 'update_product':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            $nom = trim($_POST['product_name'] ?? $_POST['nom_produit'] ?? '');
            $id_categorie = $_POST['product_category'] ?? $_POST['id_categorie'] ?? null;
            $prix_achat = floatval($_POST['product_purchase_price'] ?? $_POST['prix_achat'] ?? 0);
            $prix_vente = floatval($_POST['product_sale_price'] ?? $_POST['prix_vente'] ?? 0);
            $seuil_alerte = intval($_POST['product_min_stock'] ?? $_POST['seuil_alerte'] ?? 5);
            $description = trim($_POST['product_description'] ?? $_POST['description'] ?? '');
            $id_fournisseur = $_POST['product_fournisseur'] ?? null;
            $dosage = trim($_POST['product_dosage'] ?? $_POST['dosage'] ?? '');
            $conditionnement = trim($_POST['product_conditionnement'] ?? $_POST['conditionnement'] ?? '');
            $date_peremption = trim($_POST['product_peremption'] ?? $_POST['date_peremption'] ?? '');
            
            if (!$id_produit) {
                throw new Exception('ID produit manquant');
            }
            
            if (empty($nom)) {
                throw new Exception('Le nom du produit est obligatoire');
            }
            
            if ($id_fournisseur && !is_numeric($id_fournisseur)) {
                throw new Exception('Fournisseur invalide');
            }
            
            // NULL pour id_categorie si vide
            $id_categorie = $id_categorie && $id_categorie !== '0' ? $id_categorie : null;
            // NULL pour id_fournisseur si vide
            $id_fournisseur = $id_fournisseur && $id_fournisseur !== '0' ? $id_fournisseur : null;
            
                $sql = "UPDATE produits SET nom_produit = ?, id_categorie = ?, id_fournisseur_principal = ?, prix_achat = ?, 
                    prix_vente = ?, seuil_alerte = ?, description = ?, dosage = ?, conditionnement = ?, date_peremption = ? WHERE id_produit = ?";
                db_execute($sql, [$nom, $id_categorie, $id_fournisseur, $prix_achat, $prix_vente, $seuil_alerte, $description, $dosage, $conditionnement, $date_peremption, $id_produit]);
            
            $response['success'] = true;
            $response['message'] = 'Produit modifié avec succès';
            break;
            
        case 'delete_product':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            if (!$id_produit) {
                throw new Exception('ID produit manquant');
            }
            // Vérifier le stock total dans tous les dépôts
            $stock_total = db_fetch_one("
                SELECT SUM(quantite) as total 
                FROM stock_par_depot 
                WHERE id_produit = ? AND quantite > 0
            ", [$id_produit]);
            $stock_disponible = $stock_total['total'] ?? 0;
            if ($stock_disponible > 0) {
                throw new Exception('Impossible de supprimer un produit qui a du stock. Videz d\'abord tous les dépôts.');
            }
            // Vérifier s'il y a des ventes associées (table correcte : details_vente)
            $ventes = db_fetch_one("SELECT COUNT(*) as nb FROM details_vente WHERE id_produit = ?", [$id_produit]);
            db_begin_transaction();
            try {
                if ($ventes['nb'] > 0) {
                    // Toujours désactiver si des ventes existent (jamais de DELETE physique)
                    db_execute("UPDATE produits SET est_actif = 0 WHERE id_produit = ?", [$id_produit]);
                    $response['message'] = 'Produit désactivé (a des ventes historiques)';
                } else {
                    // Supprimer tous les mouvements de stock et lots associés avant le produit
                    db_execute("DELETE FROM mouvements_stock WHERE id_produit = ?", [$id_produit]);
                    db_execute("DELETE FROM lots_medicaments WHERE id_produit = ?", [$id_produit]);
                    db_execute("DELETE FROM stock_par_depot WHERE id_produit = ?", [$id_produit]);
                    db_execute("DELETE FROM produits WHERE id_produit = ?", [$id_produit]);
                    $response['message'] = 'Produit supprimé avec succès. Tous les mouvements de stock et lots associés ont également été supprimés. Cette action est irréversible.';
                }
                db_commit();
                $response['success'] = true;
            } catch (Exception $e) {
                db_rollback();
                throw $e;
            }
            break;
            
        case 'adjust_stock':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            $type_mouvement = $_POST['type_mouvement'] ?? 'entree';
            $quantite = intval($_POST['quantite'] ?? 0);
            $motif = trim($_POST['motif'] ?? '');
            
            if (!$id_produit || $quantite <= 0) {
                throw new Exception('Données invalides');
            }
            
            // Récupérer le stock actuel
            $produit = db_fetch_one("SELECT quantite_stock FROM produits WHERE id_produit = ?", [$id_produit]);
            
            if (!$produit) {
                throw new Exception('Produit introuvable');
            }
            
            // Calculer le nouveau stock
            $nouveau_stock = $type_mouvement === 'entree' 
                ? $produit['quantite_stock'] + $quantite 
                : $produit['quantite_stock'] - $quantite;
            
            if ($nouveau_stock < 0) {
                throw new Exception('Stock insuffisant');
            }
            
            // Mettre à jour le stock
            db_execute("UPDATE produits SET quantite_stock = ? WHERE id_produit = ?", [$nouveau_stock, $id_produit]);
            
            // Enregistrer le mouvement
            $sql = "INSERT INTO mouvements (id_produit, type_mouvement, quantite, id_utilisateur, motif, date_mouvement) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            db_execute($sql, [$id_produit, $type_mouvement, $quantite, $user_id, $motif]);
            
            $response['success'] = true;
            $response['message'] = 'Stock ajusté avec succès';
            $response['nouveau_stock'] = $nouveau_stock;
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
