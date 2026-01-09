<?php
/**
 * AJAX - VALIDATION DES VENTES
 * Enregistrer une vente avec TVA 16%
 */
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Récupération des données
    $id_client = !empty($_POST['id_client']) ? intval($_POST['id_client']) : null;
    $mode_paiement = $_POST['mode_paiement'] ?? 'especes';
    $montant_ht = floatval($_POST['montant_ht'] ?? 0);
    $montant_tva = floatval($_POST['montant_tva'] ?? 0);
    $montant_total = floatval($_POST['montant_total'] ?? 0);
    $cart = json_decode($_POST['cart'] ?? '[]', true);
    
    // Validations
    if (empty($cart)) {
        throw new Exception('Le panier est vide');
    }
    
    if ($montant_total <= 0) {
        throw new Exception('Montant invalide');
    }
    
    // Vérifier les modes de paiement valides
    $modes_valides = ['especes', 'carte', 'mobile_money', 'cheque'];
    if (!in_array($mode_paiement, $modes_valides)) {
        throw new Exception('Mode de paiement invalide');
    }
    
    // Générer le numéro de facture unique
    $numero_facture = 'FAC-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Vérifier que le numéro n'existe pas déjà
    $existing = db_fetch_one("SELECT id_vente FROM ventes WHERE numero_facture = ?", [$numero_facture]);
    if ($existing) {
        // Régénérer si collision
        $numero_facture = 'FAC-' . date('YmdHis') . '-' . rand(100, 999);
    }
    
    // Démarrer la transaction
    db_begin_transaction();
    
    // Insérer la vente
    $sql_vente = "INSERT INTO ventes (
        numero_facture,
        id_client,
        id_vendeur,
        montant_ht,
        montant_tva,
        montant_total,
        mode_paiement,
        statut,
        date_vente
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'validee', NOW())";
    
    db_execute($sql_vente, [
        $numero_facture,
        $id_client,
        $user_id,
        $montant_ht,
        $montant_tva,
        $montant_total,
        $mode_paiement
    ]);
    
    // Récupérer l'ID de la vente
    $id_vente = db_last_insert_id();
    
    // Insérer les détails de la vente
    $sql_detail = "INSERT INTO details_vente (
        id_vente,
        id_produit,
        quantite,
        prix_unitaire,
        sous_total
    ) VALUES (?, ?, ?, ?, ?)";
    
    foreach ($cart as $item) {
        // Vérifier le stock
        $produit = db_fetch_one("SELECT quantite_stock, nom_produit FROM produits WHERE id_produit = ?", [$item['id']]);
        
        if (!$produit) {
            throw new Exception("Produit ID {$item['id']} introuvable");
        }
        
        if ($produit['quantite_stock'] < $item['quantity']) {
            throw new Exception("Stock insuffisant pour {$produit['nom_produit']} (disponible: {$produit['quantite_stock']}, demandé: {$item['quantity']})");
        }
        
        // Insérer le détail
        db_execute($sql_detail, [
            $id_vente,
            $item['id'],
            $item['quantity'],
            $item['price'],
            $item['subtotal']
        ]);
        
        // Déduire du stock
        db_execute("UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id_produit = ?", [
            $item['quantity'],
            $item['id']
        ]);
        
        // Enregistrer le mouvement de stock
        db_execute("INSERT INTO mouvements (
            id_produit,
            type_mouvement,
            quantite,
            id_utilisateur,
            motif,
            date_mouvement
        ) VALUES (?, 'sortie', ?, ?, ?, NOW())", [
            $item['id'],
            $item['quantity'],
            $user_id,
            "Vente - Facture $numero_facture"
        ]);
    }
    
    // Valider la transaction
    db_commit();
    
    $response['success'] = true;
    $response['message'] = 'Vente enregistrée avec succès';
    $response['numero_facture'] = $numero_facture;
    $response['id_vente'] = $id_vente;
    
} catch (Exception $e) {
    db_rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
