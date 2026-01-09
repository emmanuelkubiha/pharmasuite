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
        case 'add_product':
            $nom = trim($_POST['nom_produit'] ?? '');
            $id_categorie = $_POST['id_categorie'] ?? null;
            $prix_achat = floatval($_POST['prix_achat'] ?? 0);
            $prix_vente = floatval($_POST['prix_vente'] ?? 0);
            $quantite_stock = intval($_POST['quantite_stock'] ?? 0);
            $seuil_alerte = intval($_POST['seuil_alerte'] ?? 5);
            $description = trim($_POST['description'] ?? '');
            
            if (empty($nom)) {
                throw new Exception('Le nom du produit est obligatoire');
            }
            
            if ($prix_vente <= 0) {
                throw new Exception('Le prix de vente doit être supérieur à 0');
            }
            
            $sql = "INSERT INTO produits (nom_produit, id_categorie, prix_achat, prix_vente, quantite_stock, seuil_alerte, description, est_actif) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
            db_execute($sql, [$nom, $id_categorie, $prix_achat, $prix_vente, $quantite_stock, $seuil_alerte, $description]);
            
            // Enregistrer le mouvement de stock initial si quantité > 0
            if ($quantite_stock > 0) {
                $id_produit = db_last_insert_id();
                $sql_mvt = "INSERT INTO mouvements (id_produit, type_mouvement, quantite, id_utilisateur, motif, date_mouvement) 
                            VALUES (?, 'entree', ?, ?, 'Stock initial', NOW())";
                db_execute($sql_mvt, [$id_produit, $quantite_stock, $user_id]);
            }
            
            $response['success'] = true;
            $response['message'] = 'Produit ajouté avec succès';
            break;
            
        case 'update_product':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            $nom = trim($_POST['nom_produit'] ?? '');
            $id_categorie = $_POST['id_categorie'] ?? null;
            $prix_achat = floatval($_POST['prix_achat'] ?? 0);
            $prix_vente = floatval($_POST['prix_vente'] ?? 0);
            $seuil_alerte = intval($_POST['seuil_alerte'] ?? 5);
            $description = trim($_POST['description'] ?? '');
            
            if (!$id_produit) {
                throw new Exception('ID produit manquant');
            }
            
            if (empty($nom)) {
                throw new Exception('Le nom du produit est obligatoire');
            }
            
            $sql = "UPDATE produits SET nom_produit = ?, id_categorie = ?, prix_achat = ?, prix_vente = ?, 
                    seuil_alerte = ?, description = ? WHERE id_produit = ?";
            db_execute($sql, [$nom, $id_categorie, $prix_achat, $prix_vente, $seuil_alerte, $description, $id_produit]);
            
            $response['success'] = true;
            $response['message'] = 'Produit modifié avec succès';
            break;
            
        case 'delete_product':
            $id_produit = intval($_POST['id_produit'] ?? 0);
            
            if (!$id_produit) {
                throw new Exception('ID produit manquant');
            }
            
            // Vérifier si le produit a des ventes
            $ventes = db_fetch_one("SELECT COUNT(*) as nb FROM ventes_details WHERE id_produit = ?", [$id_produit]);
            
            if ($ventes['nb'] > 0) {
                // Désactiver au lieu de supprimer
                db_execute("UPDATE produits SET est_actif = 0 WHERE id_produit = ?", [$id_produit]);
                $response['message'] = 'Produit désactivé (a des ventes associées)';
            } else {
                // Supprimer réellement
                db_execute("DELETE FROM produits WHERE id_produit = ?", [$id_produit]);
                $response['message'] = 'Produit supprimé avec succès';
            }
            
            $response['success'] = true;
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
