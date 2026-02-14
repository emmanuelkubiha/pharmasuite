<?php
/**
 * Purge des logs d'activités de plus de 90 jours
 * Endpoint AJAX pour nettoyer l'historique
 */
require_once __DIR__ . '/../protection_pages.php';
require_admin(); // Seuls les administrateurs peuvent purger

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Récupérer le nombre de logs à supprimer
    $sql_count = "SELECT COUNT(*) as total 
                  FROM logs_activites 
                  WHERE date_action < DATE_SUB(NOW(), INTERVAL 90 DAY)";
    
    $count_result = db_fetch_one($sql_count);
    $nb_logs = $count_result['total'];
    
    if ($nb_logs == 0) {
        $response['success'] = true;
        $response['message'] = 'Aucun log de plus de 90 jours à supprimer';
        $response['deleted'] = 0;
    } else {
        // Supprimer les logs
        $sql_delete = "DELETE FROM logs_activites 
                       WHERE date_action < DATE_SUB(NOW(), INTERVAL 90 DAY)";
        
        db_begin_transaction();
        $deleted = db_delete($sql_delete, []);
        
        // Logger l'action de purge
        log_activity('PURGE_LOGS', "Purge de $nb_logs logs de plus de 90 jours", [
            'logs_supprimes' => $nb_logs,
            'by_admin' => $user_name
        ]);
        
        db_commit();
        
        $response['success'] = true;
        $response['message'] = "$nb_logs log(s) de plus de 90 jours supprimé(s) avec succès";
        $response['deleted'] = $nb_logs;
    }
    
} catch (Exception $e) {
    if (db_in_transaction()) {
        db_rollback();
    }
    $response['message'] = 'Erreur lors de la purge des logs : ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
