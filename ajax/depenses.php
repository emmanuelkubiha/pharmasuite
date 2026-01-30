<?php
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'data' => null];
try {
    if (empty($_POST['action'])) throw new Exception('Action requise');
    $action = $_POST['action'];
    if ($action === 'add' || $action === 'update') {
        $libelle = trim($_POST['libelle'] ?? '');
        $montant = floatval($_POST['montant'] ?? 0);
        $date_depense = $_POST['date_depense'] ?? date('Y-m-d');
        $categorie = trim($_POST['categorie'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($libelle === '' || $montant <= 0) throw new Exception('Libellé et montant obligatoires');
        db_begin_transaction();
        if ($action === 'add') {
            db_insert('depenses', [
                'libelle' => $libelle,
                'montant' => $montant,
                'date_depense' => $date_depense,
                'categorie' => $categorie,
                'description' => $description,
                'cree_par' => $user_id,
                'cree_le' => date('Y-m-d H:i:s')
            ]);
            log_activity('depense_add', 'Ajout dépense', ['libelle' => $libelle, 'montant' => $montant]);
            $msg = 'Dépense ajoutée';
        } else {
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('ID manquant');
            db_update('depenses', [
                'libelle' => $libelle,
                'montant' => $montant,
                'date_depense' => $date_depense,
                'categorie' => $categorie,
                'description' => $description
            ], 'id_depense = ?', [$id]);
            log_activity('depense_update', 'Modification dépense', ['id' => $id]);
            $msg = 'Dépense modifiée';
        }
        db_commit();
        $response = ['success' => true, 'message' => $msg];
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('ID manquant');
        db_begin_transaction();
        db_delete('depenses', 'id_depense = ?', [$id]);
        log_activity('depense_delete', 'Suppression dépense', ['id' => $id]);
        db_commit();
        $response = ['success' => true, 'message' => 'Dépense supprimée'];
    } elseif ($action === 'list') {
        $depenses = db_fetch_all('SELECT * FROM depenses ORDER BY date_depense DESC, id_depense DESC');
        $response = ['success' => true, 'data' => $depenses];
    } elseif ($action === 'get') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('ID manquant');
        $dep = db_fetch_one('SELECT * FROM depenses WHERE id_depense = ?', [$id]);
        if (!$dep) throw new Exception('Dépense introuvable');
        $response = ['success' => true, 'data' => $dep];
    } else {
        throw new Exception('Action inconnue');
    }
} catch (Exception $e) {
    if (db_in_transaction()) db_rollback();
    $response['message'] = $e->getMessage();
}
echo json_encode($response);