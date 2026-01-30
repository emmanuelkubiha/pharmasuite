<?php
/**
 * AJAX endpoint - Gestion des dépôts (CRUD)
 */
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');

if (!$is_admin) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $nom = $_POST['nom_depot'] ?? '';
            $description = $_POST['description'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $est_principal = isset($_POST['est_principal']) ? intval($_POST['est_principal']) : 0;
            
            if (empty($nom)) {
                throw new Exception('Nom du dépôt requis');
            }
            
            // Si on marque comme principal, retirer le statut principal des autres
            if ($est_principal) {
                db_query("UPDATE depots SET est_principal = 0");
            }
            
            $id = db_insert('depots', [
                'nom_depot' => $nom,
                'description' => $description,
                'adresse' => $adresse,
                'est_principal' => $est_principal
            ]);
            
            if ($id) {
                $response = ['success' => true, 'message' => 'Dépôt ajouté avec succès', 'id' => $id];
            } else {
                throw new Exception('Erreur lors de l\'ajout');
            }
            break;
            
        case 'update':
            $id = isset($_POST['id_depot']) ? intval($_POST['id_depot']) : 0;
            // DEBUG: log la valeur et le type de $id
            error_log('DEBUG depots.php update: id_depot=' . var_export($id, true) . ' type=' . gettype($id));
            if (is_array($id)) {
                error_log('ERREUR FATALE depots.php update: id_depot EST UN ARRAY ! ' . var_export($id, true));
                throw new Exception('Erreur technique : id_depot malformé (array)');
            }
            $nom = $_POST['nom_depot'] ?? '';
            $description = $_POST['description'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $est_principal = isset($_POST['est_principal']) ? intval($_POST['est_principal']) : 0;
            
            if (empty($id) || empty($nom)) {
                throw new Exception('Données incomplètes');
            }
            
            // Vérifier si c'est le dépôt principal
            $depot = db_fetch_one("SELECT est_principal FROM depots WHERE id_depot = ?", [$id]);
            
            // Si on enlève le statut principal, vérifier qu'il y en a au moins un autre
            if ($depot['est_principal'] == 1 && $est_principal == 0) {
                $autres = db_fetch_one("SELECT COUNT(*) as nb FROM depots WHERE est_actif = 1 AND id_depot != ?", [$id]);
                if ($autres['nb'] == 0) {
                    throw new Exception('Impossible de retirer le statut principal : au moins un dépôt doit être principal');
                }
            }
            
            // Si on marque comme principal, retirer le statut principal des autres
            if ($est_principal && !$depot['est_principal']) {
                db_query("UPDATE depots SET est_principal = 0");
            }
            
            $updated = db_update('depots', [
                'nom_depot' => $nom,
                'description' => $description,
                'adresse' => $adresse,
                'est_principal' => $est_principal
            ], 'id_depot = ?', [$id]);
            // DEBUG: log la requête db_update
            error_log('DEBUG depots.php update: db_update where id_depot=' . var_export($id, true));
            
            if ($updated !== false) {
                $response = ['success' => true, 'message' => 'Dépôt modifié avec succès'];
            } else {
                throw new Exception('Erreur lors de la modification');
            }
            break;
            
        case 'delete':
            $id = isset($_POST['id_depot']) ? intval($_POST['id_depot']) : 0;
            // DEBUG: log la valeur et le type de $id pour suppression
            error_log('DEBUG depots.php delete: id_depot=' . var_export($id, true) . ' type=' . gettype($id));
            if (is_array($id)) {
                error_log('ERREUR FATALE depots.php delete: id_depot EST UN ARRAY ! ' . var_export($id, true));
                throw new Exception('Erreur technique : id_depot malformé (array)');
            }
            
            if (empty($id)) {
                throw new Exception('ID manquant');
            }
            
            // Vérifier si le dépôt est principal
            $depot = db_fetch_one("SELECT est_principal FROM depots WHERE id_depot = ?", [$id]);
            if ($depot['est_principal']) {
                throw new Exception('Impossible de supprimer le dépôt principal');
            }
            
            // Vérifier si le dépôt contient du stock
            $stock = db_fetch_one("
                SELECT SUM(quantite) as total FROM stock_par_depot WHERE id_depot = ?
            ", [$id]);
            
            if ($stock['total'] > 0) {
                throw new Exception('Impossible de supprimer : ce dépôt contient du stock (' . $stock['total'] . ' unités)');
            }
            
            $deleted = db_update('depots', ['est_actif' => 0], 'id_depot = ?', [$id]);
                                    // DEBUG: log la requête db_update suppression
                                    error_log('DEBUG depots.php delete: db_update where id_depot=' . var_export($id, true));
                        // DEBUG: log la requête db_update suppression
                        error_log('DEBUG depots.php delete: db_update where id_depot=' . var_export($id, true));
            
            if ($deleted !== false) {
                $response = ['success' => true, 'message' => 'Dépôt supprimé avec succès'];
            } else {
                throw new Exception('Erreur lors de la suppression');
            }
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
