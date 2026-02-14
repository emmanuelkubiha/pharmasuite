<?php
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'data' => null];
try {
    if (empty($_POST['action'])) throw new Exception('Action requise');
    $action = $_POST['action'];
    if ($action === 'add' || $action === 'update') {
        $type = $_POST['type'] ?? '';
        $montant = floatval($_POST['montant'] ?? 0);
        $mode_paiement = $_POST['mode_paiement'] ?? 'Cash';
        $date_mouvement = $_POST['date_mouvement'] ?? date('Y-m-d');
        $motif = trim($_POST['motif'] ?? '');
        $utilisateur = $user_id;
        if ($type === '' || $montant === 0) throw new Exception('Type et montant obligatoires');
        db_begin_transaction();
        if ($action === 'add') {
            db_insert('caisse_mouvements', [
                'type' => $type,
                'montant' => $montant,
                'mode_paiement' => $mode_paiement,
                'date_mouvement' => $date_mouvement,
                'motif' => $motif,
                'utilisateur' => $utilisateur,
                'cree_le' => date('Y-m-d H:i:s')
            ]);
            log_activity('caisse_add', 'Ajout mouvement caisse', ['type' => $type, 'montant' => $montant]);
            $msg = 'Mouvement ajouté';
        } else {
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('ID manquant');
            db_update('caisse_mouvements', [
                'type' => $type,
                'montant' => $montant,
                'mode_paiement' => $mode_paiement,
                'date_mouvement' => $date_mouvement,
                'motif' => $motif
            ], 'id_mouvement = ?', [$id]);
            log_activity('caisse_update', 'Modification mouvement caisse', ['id' => $id]);
            $msg = 'Mouvement modifié';
        }
        db_commit();
        $response = ['success' => true, 'message' => $msg];
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('ID manquant');
        db_begin_transaction();
        db_delete('caisse_mouvements', 'id_mouvement = ?', [$id]);
        log_activity('caisse_delete', 'Suppression mouvement caisse', ['id' => $id]);
        db_commit();
        $response = ['success' => true, 'message' => 'Mouvement supprimé'];
    } elseif ($action === 'list') {
        // Récupérer tous les mouvements manuels
        $sql = 'SELECT m.*, u.nom_complet FROM caisse_mouvements m LEFT JOIN utilisateurs u ON m.utilisateur = u.id_utilisateur ORDER BY date_mouvement DESC, id_mouvement DESC';
        $mouvements = db_fetch_all($sql);

        // Récupérer toutes les ventes validées
        $ventes = db_fetch_all("SELECT montant_total, mode_paiement, date_vente FROM ventes WHERE statut = 'validee'");

        // Calcul du solde actuel : ventes validées - sorties (dépenses)
        $total_ventes = 0;
        $total_depenses = 0;
        $date_jour = date('Y-m-d');
        $total_ventes_jour = 0;
        $total_depenses_jour = 0;

        // Soldes par mode de paiement
        // Initialisation des soldes par mode
        $soldes_mode = [];
        $modes = ['especes','mobile_money','carte','cheque','credit'];
        foreach ($modes as $m) $soldes_mode[$m] = 0;

        // Additionne toutes les ventes validées par mode
        foreach ($ventes as $v) {
            $montant = floatval($v['montant_total']);
            $mode = strtolower($v['mode_paiement']);
            if (isset($soldes_mode[$mode])) $soldes_mode[$mode] += $montant;
            $total_ventes += $montant;
            if (substr($v['date_vente'],0,10) === $date_jour) {
                $total_ventes_jour += $montant;
            }
        }

        // Soustrait toutes les sorties (dépenses) par mode
        foreach ($mouvements as $m) {
            $montant = floatval($m['montant']);
            $mode = strtolower($m['mode_paiement']);
            if ($m['type'] === 'Sortie' && isset($soldes_mode[$mode])) {
                $soldes_mode[$mode] -= $montant;
                $total_depenses += $montant;
                if ($m['date_mouvement'] === $date_jour) {
                    $total_depenses_jour += $montant;
                }
            }
        }

        // Calcul du total général
        $solde_actuel = array_sum($soldes_mode);
        $solde_jour = $total_ventes_jour - $total_depenses_jour;

        $solde_actuel = $total_ventes - $total_depenses;
        $solde_jour = $total_ventes_jour - $total_depenses_jour;

        $response = [
            'success' => true,
            'data' => $mouvements,
            'solde_total' => $solde_actuel,
            'solde_jour' => $solde_jour,
            'total_ventes' => $total_ventes,
            'total_depenses' => $total_depenses,
            'total_ventes_jour' => $total_ventes_jour,
            'total_depenses_jour' => $total_depenses_jour,
            'solde_especes' => $soldes_mode['especes'],
            'solde_mobile_money' => $soldes_mode['mobile_money'],
            'solde_carte' => $soldes_mode['carte'],
            'solde_cheque' => $soldes_mode['cheque'],
            'solde_credit' => $soldes_mode['credit']
        ];
    } elseif ($action === 'get') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('ID manquant');
        $m = db_fetch_one('SELECT * FROM caisse_mouvements WHERE id_mouvement = ?', [$id]);
        if (!$m) throw new Exception('Mouvement introuvable');
        $response = ['success' => true, 'data' => $m];
    } else {
        throw new Exception('Action inconnue');
    }
} catch (Exception $e) {
    if (db_in_transaction()) db_rollback();
    $response['message'] = $e->getMessage();
}
echo json_encode($response);