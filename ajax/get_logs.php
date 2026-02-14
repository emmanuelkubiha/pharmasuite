<?php
/**
 * Récupération des logs d'activités
 * Endpoint AJAX pour afficher l'historique des actions
 */
require_once __DIR__ . '/../protection_pages.php';
require_admin(); // Seuls les administrateurs peuvent consulter les logs

header('Content-Type: application/json');

$response = ['success' => false, 'logs' => [], 'total' => 0, 'message' => ''];

try {
    // Récupérer les filtres
    $type_action = $_GET['type_action'] ?? '';
    $id_utilisateur = $_GET['id_utilisateur'] ?? '';
    $date_debut = $_GET['date_debut'] ?? date('Y-m-01');
    $date_fin = $_GET['date_fin'] ?? date('Y-m-d');
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 50; // Nombre de logs par page
    $offset = ($page - 1) * $limit;
    
    // Construire la requête SQL optimisée
    $where_clauses = [
        "DATE(date_action) BETWEEN ? AND ?"
    ];
    $params = [$date_debut, $date_fin];
    
    if (!empty($type_action)) {
        $where_clauses[] = "type_action = ?";
        $params[] = $type_action;
    }
    
    if (!empty($id_utilisateur)) {
        $where_clauses[] = "id_utilisateur = ?";
        $params[] = $id_utilisateur;
    }
    
    $where_sql = implode(' AND ', $where_clauses);
    
    // Compter le nombre total de logs (requête rapide)
    $sql_count = "SELECT COUNT(*) as total 
                  FROM logs_activites
                  WHERE $where_sql";
    
    $count_result = db_fetch_one($sql_count, $params);
    $total = $count_result['total'];
    
    // Récupérer les logs paginés avec JOIN optimisé
    $sql = "SELECT 
                la.id_log,
                la.type_action,
                la.description,
                la.ip_address,
                la.user_agent,
                la.date_action,
                u.nom_complet as utilisateur
            FROM logs_activites la
            LEFT JOIN utilisateurs u ON la.id_utilisateur = u.id_utilisateur
            WHERE $where_sql
            ORDER BY la.date_action DESC
            LIMIT $limit OFFSET $offset";
    
    $logs = db_fetch_all($sql, $params);
    
    // Formatter les données (version simplifiée)
    $logs_formatted = [];
    foreach ($logs as $log) {
        // Badge de couleur selon le type d'action
        $badge_color = 'secondary';
        switch (strtoupper($log['type_action'])) {
            case 'CONNEXION':
                $badge_color = 'success';
                break;
            case 'DECONNEXION':
                $badge_color = 'secondary';
                break;
            case 'VENTE':
            case 'VENTE_VALIDEE':
                $badge_color = 'primary';
                break;
            case 'VENTE_ANNULEE':
            case 'VENTE_SUPPRIMEE':
                $badge_color = 'danger';
                break;
            case 'PRODUIT_AJOUT':
            case 'PRODUIT_CREATION':
                $badge_color = 'success';
                break;
            case 'PRODUIT_MODIFICATION':
                $badge_color = 'warning';
                break;
            case 'PRODUIT_SUPPRESSION':
                $badge_color = 'danger';
                break;
            case 'CONFIGURATION_INITIALE':
            case 'CONFIGURATION':
                $badge_color = 'info';
                break;
        }
        
        // Extraire navigateur simplifié
        $browser = 'N/A';
        if (!empty($log['user_agent'])) {
            if (strpos($log['user_agent'], 'Chrome') !== false) $browser = 'Chrome';
            elseif (strpos($log['user_agent'], 'Firefox') !== false) $browser = 'Firefox';
            elseif (strpos($log['user_agent'], 'Safari') !== false) $browser = 'Safari';
            elseif (strpos($log['user_agent'], 'Edge') !== false) $browser = 'Edge';
        }
        
        $logs_formatted[] = [
            'id' => $log['id_log'],
            'date' => date('d/m/Y H:i', strtotime($log['date_action'])),
            'utilisateur' => $log['utilisateur'] ?? 'Système',
            'type_action' => $log['type_action'],
            'type_badge' => $badge_color,
            'description' => $log['description'],
            'ip' => $log['ip_address'] ?? 'N/A',
            'browser' => $browser
        ];
    }
    
    $response['success'] = true;
    $response['logs'] = $logs_formatted;
    $response['total'] = $total;
    $response['page'] = $page;
    $response['limit'] = $limit;
    $response['total_pages'] = ceil($total / $limit);
    $response['message'] = count($logs_formatted) . ' log(s) trouvé(s)';
    
} catch (Exception $e) {
    $response['message'] = 'Erreur lors de la récupération des logs : ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
