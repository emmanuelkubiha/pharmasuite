echo 'DEBUG AJAX OK\n';
<?php
// NE RIEN AFFICHER AVANT CE POINT !
if (headers_sent()) die('Erreur : headers déjà envoyés');
header('Content-Type: application/json');
require_once __DIR__ . '/../protection_pages.php';
if (!isset($user_id) || !$user_id) {
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Session non reconnue ou utilisateur non connecté (debug AJAX)'
    ]);
    exit;
}
// Ne rien afficher d'autre que le JSON !
$response = ['success' => false, 'data' => null, 'message' => ''];
try {
    // Lots à risque de péremption (vue réelle)
            $sql = "SELECT l.id_lot, l.numero_lot, l.date_peremption, l.quantite, p.nom_produit, p.id_produit, p.conditionnement, c.nom_categorie
                FROM lots_medicaments l
                LEFT JOIN produits p ON l.id_produit = p.id_produit
                LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                WHERE l.date_peremption IS NOT NULL
                ORDER BY l.date_peremption ASC";
    $lots = db_fetch_all($sql);
    $response = ['success' => true, 'data' => $lots];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}
echo json_encode($response);
exit;