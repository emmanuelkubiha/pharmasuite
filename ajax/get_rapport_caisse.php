<?php
/**
 * RAPPORT CAISSE - PharmaSuite
 * Récupère les données réelles des mouvements de caisse
 */
require_once __DIR__ . '/../protection_pages.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'stats' => [], 'message' => ''];

try {
    // Récupérer les filtres
    $date_debut = $_GET['date_debut'] ?? date('Y-m-01');
    $date_fin = $_GET['date_fin'] ?? date('Y-m-d');
    $categorie = $_GET['categorie'] ?? '';
    
    // Récupérer le solde actuel de la caisse
    $caisse = db_fetch_one("SELECT solde_actuel FROM caisses LIMIT 1");
    $solde_actuel = $caisse ? floatval($caisse['solde_actuel']) : 0;
    
    // Construire la requête pour les mouvements
    $mouvements = [];
    
    // 1. Récupérer les VENTES (entrées)
    $sql_ventes = "
        SELECT 
            v.date_vente as date,
            CONCAT('Vente N°', v.numero_facture) as libelle,
            v.montant_total as montant,
            'Entrée' as type,
            'Vente' as categorie,
            CONCAT('Client: ', COALESCE(c.nom_client, 'Comptant')) as description,
            v.date_vente as date_tri
        FROM ventes v
        LEFT JOIN clients c ON v.id_client = c.id_client
        WHERE DATE(v.date_vente) BETWEEN ? AND ?
        AND v.statut != 'annulee'
    ";
    
    $params_ventes = [$date_debut, $date_fin];
    if (!empty($categorie) && $categorie === 'Vente') {
        $ventes = db_fetch_all($sql_ventes, $params_ventes);
        $mouvements = array_merge($mouvements, $ventes);
    } elseif (empty($categorie)) {
        $ventes = db_fetch_all($sql_ventes, $params_ventes);
        $mouvements = array_merge($mouvements, $ventes);
    }
    
    // 2. Récupérer les DÉPENSES (sorties)
    $where_depense = ["DATE(d.date_depense) BETWEEN ? AND ?"];
    $params_depense = [$date_debut, $date_fin];
    
    // Mapper les catégories
    if (!empty($categorie)) {
        if ($categorie === 'Achat stock') {
            $where_depense[] = "d.motif LIKE '%stock%' OR d.motif LIKE '%achat%'";
        } elseif ($categorie === 'Frais') {
            $where_depense[] = "d.motif LIKE '%frais%' OR d.motif LIKE '%facture%'";
        } elseif ($categorie === 'Salaire') {
            $where_depense[] = "d.motif LIKE '%salaire%' OR d.motif LIKE '%paie%'";
        } elseif ($categorie === 'Autre') {
            $where_depense[] = "d.motif NOT LIKE '%stock%' AND d.motif NOT LIKE '%frais%' AND d.motif NOT LIKE '%salaire%'";
        }
    }
    
    $where_depense_sql = implode(' AND ', $where_depense);
    
    $sql_depenses = "
        SELECT 
            d.date_depense as date,
            d.motif as libelle,
            -ABS(d.montant) as montant,
            'Dépense' as type,
            CASE 
                WHEN d.motif LIKE '%stock%' OR d.motif LIKE '%achat%' THEN 'Achat stock'
                WHEN d.motif LIKE '%frais%' OR d.motif LIKE '%facture%' THEN 'Frais'
                WHEN d.motif LIKE '%salaire%' OR d.motif LIKE '%paie%' THEN 'Salaire'
                ELSE 'Autre'
            END as categorie,
            CONCAT('Utilisateur: ', u.nom_complet) as description,
            d.date_depense as date_tri
        FROM depenses d
        LEFT JOIN utilisateurs u ON d.utilisateur_id = u.id_utilisateur
        WHERE $where_depense_sql
    ";
    
    if (empty($categorie) || $categorie !== 'Vente') {
        $depenses = db_fetch_all($sql_depenses, $params_depense);
        $mouvements = array_merge($mouvements, $depenses);
    }
    
    // Trier par date décroissante
    usort($mouvements, function($a, $b) {
        return strtotime($b['date_tri']) - strtotime($a['date_tri']);
    });
    
    // Calculer les statistiques
    $total_entrees = 0;
    $total_depenses = 0;
    $nb_operations = count($mouvements);
    
    foreach ($mouvements as &$m) {
        // Formater la date
        $m['date'] = date('d/m/Y H:i', strtotime($m['date']));
        $m['montant'] = floatval($m['montant']);
        
        // Calculer totaux
        if ($m['montant'] > 0) {
            $total_entrees += $m['montant'];
        } else {
            $total_depenses += abs($m['montant']);
        }
        
        // Supprimer date_tri inutile pour le front
        unset($m['date_tri']);
    }
    
    $response = [
        'success' => true,
        'data' => $mouvements,
        'stats' => [
            'solde_actuel' => $solde_actuel,
            'entrees' => $total_entrees,
            'depenses' => $total_depenses,
            'nb_operations' => $nb_operations,
            'solde_periode' => $total_entrees - $total_depenses
        ],
        'message' => "$nb_operations opération(s) trouvée(s)"
    ];
    
} catch (Exception $e) {
    $response['message'] = 'Erreur lors de la récupération des données : ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
