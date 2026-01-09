<?php
/**
 * ENDPOINT: Validation et sauvegarde d'une vente
 * POST: cart (JSON array), id_client (optionnel)
 */
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'id_vente' => null];

try {
    // Validation des données
    if (empty($_POST['cart'])) {
        throw new Exception('Panier vide');
    }
    
    $cart = json_decode($_POST['cart'], true);
    if (!is_array($cart) || empty($cart)) {
        throw new Exception('Données panier invalides');
    }
    
    $id_client = !empty($_POST['id_client']) ? intval($_POST['id_client']) : null;
    
    // Vérifier le stock pour chaque produit
    foreach ($cart as $item) {
        $produit = db_fetch_one(
            "SELECT quantite_stock FROM produits WHERE id_produit = ?",
            [$item['id']]
        );
        
        if (!$produit) {
            throw new Exception("Produit {$item['nom']} non trouvé");
        }
        
        if ($produit['quantite_stock'] < $item['quantite']) {
            throw new Exception("Stock insuffisant pour {$item['nom']} (disponible: {$produit['quantite_stock']})");
        }
    }
    
    // Générer le numéro de facture
    $last_facture = db_fetch_one(
        "SELECT numero_facture FROM ventes ORDER BY id_vente DESC LIMIT 1"
    );
    
    $numero_facture = 'FAC-' . date('Ymd') . '-' . str_pad(
        (int)substr($last_facture['numero_facture'] ?? 'FAC-00000000-0000', -4) + 1, 
        4, 
        '0', 
        STR_PAD_LEFT
    );
    
    // Calculer les montants
    $montant_ht = 0;
    foreach ($cart as $item) {
        $montant_ht += $item['prix'] * $item['quantite'];
    }
    
    $montant_tva = round($montant_ht * 0.16, 2);
    $montant_total = $montant_ht + $montant_tva;
    $montant_remise = 0; // TODO: Ajouter support des remises
    
    // Commencer la transaction
    db_begin_transaction();
    
    try {
        // Créer la vente
        $id_vente = db_insert('ventes', [
            'numero_facture' => $numero_facture,
            'id_client' => $id_client,
            'id_vendeur' => $user_id,
            'montant_ht' => $montant_ht,
            'montant_tva' => $montant_tva,
            'montant_remise' => $montant_remise,
            'montant_paye' => 0,
            'montant_rendu' => 0,
            'montant_total' => $montant_total,
            'mode_paiement' => 'especes', // Default - TODO: Ajouter interface paiement
            'statut' => 'validee',
            'notes' => '',
            'date_vente' => date('Y-m-d H:i:s')
        ]);
        
        if (!$id_vente) {
            throw new Exception('Erreur lors de la création de la vente');
        }
        
        // Ajouter les détails de vente et mettre à jour le stock
        foreach ($cart as $item) {
            // Insérer le détail de vente
            db_insert('details_vente', [
                'id_vente' => $id_vente,
                'id_produit' => $item['id'],
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix']
            ]);
            
            // Mettre à jour le stock du produit
            db_execute(
                "UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id_produit = ?",
                [$item['quantite'], $item['id']]
            );
            
            // Enregistrer le mouvement de stock
            db_insert('mouvements_stock', [
                'id_produit' => $item['id'],
                'type_mouvement' => 'sortie',
                'quantite' => $item['quantite'],
                'stock_avant' => $produit['quantite_stock'],
                'stock_apres' => $produit['quantite_stock'] - $item['quantite'],
                'motif' => 'Vente ' . $numero_facture,
                'id_utilisateur' => $user_id,
                'date_mouvement' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Enregistrer l'activité
        log_activity('VENTE', "Nouvelle vente créée: $numero_facture ($montant_total " . $devise . ")", [
            'id_vente' => $id_vente,
            'numero_facture' => $numero_facture,
            'montant' => $montant_total
        ]);
        
        db_commit();
        
        $response = [
            'success' => true,
            'message' => "Vente validée avec succès! N° facture: $numero_facture",
            'id_vente' => $id_vente,
            'numero_facture' => $numero_facture,
            'montant_total' => $montant_total
        ];
        
    } catch (Exception $e) {
        db_rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($response);
