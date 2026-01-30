<?php
require_once('protection_pages.php');

// --- Récupération des filtres (GET ou POST selon contexte) ---
$search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$date_min = isset($_REQUEST['date_min']) ? $_REQUEST['date_min'] : '';
$date_max = isset($_REQUEST['date_max']) ? $_REQUEST['date_max'] : '';
$statut = isset($_REQUEST['statut']) ? $_REQUEST['statut'] : '';

// --- PHP : récupération et filtrage des lots (toujours avant export) ---
$sql = "SELECT l.id_lot, l.numero_lot, l.date_peremption, l.quantite, p.nom_produit, p.id_produit, p.conditionnement, c.nom_categorie
        FROM lots_medicaments l
        LEFT JOIN produits p ON l.id_produit = p.id_produit
        LEFT JOIN categories c ON p.id_categorie = c.id_categorie
        WHERE l.date_peremption IS NOT NULL";
$params = [];
if ($search) {
    $sql .= " AND (p.nom_produit LIKE ? OR l.numero_lot LIKE ? OR l.date_peremption LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($date_min) {
    $sql .= " AND l.date_peremption >= ?";
    $params[] = $date_min;
}
if ($date_max) {
    $sql .= " AND l.date_peremption <= ?";
    $params[] = $date_max;
}
$sql .= " ORDER BY l.date_peremption ASC";
$lots = db_fetch_all($sql, $params);
function statut_peremption($date_peremption) {
    $today = date('Y-m-d');
    $jours = (strtotime($date_peremption) - strtotime($today)) / 86400;
    if ($jours < 0) return 'expiré';
    if ($jours <= 30) return 'alerte_grave';
    if ($jours <= 90) return 'alerte';
    return 'plus_90';
}
if ($statut) {
    $lots = array_filter($lots, function($lot) use ($statut) {
        return statut_peremption($lot['date_peremption']) === $statut;
    });
}

// --- Export Excel (CSV) ---
if (isset($_POST['export']) && $_POST['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lots_peremption_'.date('Ymd_His').'.csv"');
    // BOM UTF-8 pour Excel
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    // Titre principal
    fputs($out, '"Lots à surveiller – Suivi des péremptions"\r\n');
    // Résumé des filtres actifs
    $filtres = [];
    if ($search) $filtres[] = 'Recherche : '.str_replace('"','""',$search);
    if ($date_min) $filtres[] = 'Date min : '.str_replace('"','""',$date_min);
    if ($date_max) $filtres[] = 'Date max : '.str_replace('"','""',$date_max);
    if ($statut) {
        $libelles = [
            'expiré' => 'Expiré',
            'alerte_grave' => 'Alerte grave (<30j)',
            'alerte' => 'Alerte (<90j)',
            'plus_90' => 'Péremption > 90j'
        ];
        $filtres[] = 'Statut : '.($libelles[$statut] ?? $statut);
    }
    if (count($filtres)) {
        fputs($out, 'Filtres actifs : '.implode(' | ', $filtres)."\r\n");
    } else {
        fputs($out, "Aucun filtre actif\r\n");
    }
    fputs($out, "\r\n");
    // En-têtes du tableau
    $entetes = ['Médicament','Lot','Date péremption','Quantité','Catégorie','Conditionnement','Statut'];
    fputcsv($out, $entetes, ';', '"');
    foreach ($lots as $lot) {
        $statut_lot = statut_peremption($lot['date_peremption']);
        $ligne = [
            $lot['nom_produit'], $lot['numero_lot'], $lot['date_peremption'], $lot['quantite'],
            $lot['nom_categorie'], $lot['conditionnement'],
            ($statut_lot==='expiré'?'Périmé':($statut_lot==='alerte_grave'?'Moins de 30j':($statut_lot==='alerte'?'Moins de 90j':'Péremption > 90j')))
        ];
        // fputcsv n'ajoute pas \r\n sur certains serveurs, donc on le force
        fputs($out, implode(';', array_map(function($v){return '"'.str_replace('"','""',$v).'"';}, $ligne))."\r\n");
    }
    fclose($out);
    exit;
}
// --- Export PDF (structure, à compléter avec FPDF si besoin) ---
if (isset($_POST['export']) && $_POST['export'] === 'pdf') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="lots_peremption_'.date('Ymd_His').'.pdf"');
    echo "%PDF-1.4\n% Export PDF à activer prochainement.";
    exit;
}

$page_title = 'Suivi des péremptions';
require_once('header.php');
?>
<div class="container py-4">
    <h1 class="mb-4"><i class="material-symbols-outlined align-middle text-warning" style="font-size:2.2rem;">warning</i> Suivi des péremptions</h1>
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="alert alert-info mb-2">
                <span class="material-symbols-outlined align-middle text-primary" style="font-size:1.3em;vertical-align:middle;" data-bs-toggle="tooltip" title="Astuce : Utilisez les filtres pour cibler les lots à risque ou exporter la liste.">info</span>
                Retrouvez ici tous les lots de médicaments proches ou dépassant leur date de péremption.<br>
                <span class="text-muted small" data-bs-toggle="tooltip" title="Filtrez par date, statut ou nom pour trouver rapidement les lots à surveiller.">Filtrez, exportez et agissez rapidement pour éviter les pertes.</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-2 border-warning">
                <div class="card-body py-2 px-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fw-bold text-danger" id="statExp" data-bs-toggle="tooltip" title="Nombre de lots déjà périmés">0</div>
                            <div class="small text-muted">Expirés</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning" id="statAlerte" data-bs-toggle="tooltip" title="Lots à risque : moins de 90 jours avant péremption">0</div>
                            <div class="small text-muted">Alerte (<90j)</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success" id="statTotal" data-bs-toggle="tooltip" title="Nombre total de lots affichés">0</div>
                            <div class="small text-muted">Total lots</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-2">

    </div>
    <form method="get" class="row mb-3 g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label mb-1">Recherche</label>
            <input type="text" name="search" class="form-control" placeholder="Nom, lot, date..." value="<?=e(isset($_GET['search'])?$_GET['search']:'')?>">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Date min</label>
            <input type="date" name="date_min" class="form-control" value="<?=e(isset($_GET['date_min'])?$_GET['date_min']:'')?>">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Date max</label>
            <input type="date" name="date_max" class="form-control" value="<?=e(isset($_GET['date_max'])?$_GET['date_max']:'')?>">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Statut</label>
            <select name="statut" class="form-select">
                <option value="" <?=empty($_GET['statut'])?'selected':''?>>Tous</option>
                <option value="expiré" <?=(isset($_GET['statut'])&&$_GET['statut']=='expiré')?'selected':''?>>Expiré</option>
                <option value="alerte_grave" <?=(isset($_GET['statut'])&&$_GET['statut']=='alerte_grave')?'selected':''?>>Alerte grave (<30j)</option>
                <option value="alerte" <?=(isset($_GET['statut'])&&$_GET['statut']=='alerte')?'selected':''?>>Alerte (<90j)</option>
                <option value="plus_90" <?=(isset($_GET['statut'])&&$_GET['statut']=='plus_90')?'selected':''?>>Péremption > 90j</option>
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <button class="btn btn-primary" type="submit"><i class="material-symbols-outlined align-middle">search</i> Filtrer</button>
            <a href="peremptions.php" class="btn btn-secondary">Réinitialiser</a>
        </div>
    </form>
    <div id="print-section" class="card">
        <!-- Titre et filtres pour impression -->
        <div class="d-none d-print-block" style="margin-bottom:18px;">
            <h2 style="font-size:1.7rem; font-weight:bold; margin-bottom:0.5em;">Lots à surveiller – Suivi des péremptions</h2>
            <?php
            $filtres = [];
            if ($search) $filtres[] = 'Recherche : <b>'.e($search).'</b>';
            if ($date_min) $filtres[] = 'Date min : <b>'.e($date_min).'</b>';
            if ($date_max) $filtres[] = 'Date max : <b>'.e($date_max).'</b>';
            if ($statut) {
                $libelles = [
                    'expiré' => 'Expiré',
                    'alerte_grave' => 'Alerte grave (<30j)',
                    'alerte' => 'Alerte (<90j)',
                    'plus_90' => 'Péremption > 90j'
                ];
                $filtres[] = 'Statut : <b>'.($libelles[$statut] ?? e($statut)).'</b>';
            }
            if (count($filtres)) {
                echo '<div style="font-size:1.1rem; color:#444; margin-bottom:0.5em;">Filtres actifs : '.implode(' | ', $filtres).'</div>';
            }
            ?>
        </div>
        <style>
        @media print {
            body * { visibility: hidden !important; }
            #print-section, #print-section * { visibility: visible !important; }
            #print-section { position: absolute; left: 0; top: 0; width: 100vw; background: white; font-family: var(--bs-body-font-family, system-ui, Arial, sans-serif); }
            #print-section .btn, #print-section .form-control, #print-section .form-select, #print-section .alert, #print-section .badge, #print-section .d-flex, #print-section .mb-3, #print-section .mt-2 { display: none !important; }
            #print-section .d-print-block { display: block !important; }
            #print-section table { font-size: 1rem; border-collapse: collapse; width: 100%; margin-top: 10px; background: #fff; page-break-inside: auto; }
            #print-section thead { display: table-header-group; background: #f8f9fa !important; }
            #print-section tfoot { display: table-footer-group; }
            #print-section th, #print-section td { border: 1px solid #dee2e6; padding: 8px 12px; text-align: left; vertical-align: middle; }
            #print-section th { background: #f8f9fa !important; color: #212529; font-weight: 700; font-size: 1.05rem; }
            #print-section tr { page-break-inside: avoid; page-break-after: auto; background: #fff !important; color: #222; }
            #print-section tr.table-danger, #print-section tr.table-warning, #print-section tr.table-info, #print-section tr.table-success {
                background: #fff !important; color: #222 !important;
            }
            #print-section tbody tr:nth-of-type(odd) { background-color: #f2f2f2 !important; }
        }
        </style>
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <b>Lots à surveiller</b>
            <div class="d-flex gap-2">
                <form method="post" action="peremptions.php" style="display:inline;" id="export-excel-form">
                    <input type="hidden" name="export" value="csv">
                    <input type="hidden" name="search" value="<?=e($search)?>">
                    <input type="hidden" name="date_min" value="<?=e($date_min)?>">
                    <input type="hidden" name="date_max" value="<?=e($date_max)?>">
                    <input type="hidden" name="statut" value="<?=e($statut)?>">
                    <button class="btn btn-sm btn-outline-success me-2" type="submit"><i class="material-symbols-outlined align-middle">download</i> Export Excel</button>
                </form>
                <script>
                // Recharge la page après export Excel pour éviter le blocage sur loading
                document.getElementById('export-excel-form').addEventListener('submit', function(e) {
                    setTimeout(function() { window.location.reload(); }, 1000);
                });
                </script>
                <button class="btn btn-sm btn-outline-danger" type="button" onclick="window.print();"><i class="material-symbols-outlined align-middle">print</i> Imprimer</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="lotsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" data-sort="nom_produit" data-bs-toggle="tooltip" title="Trier par nom du médicament">Médicament <span class="material-symbols-outlined sort-icon">swap_vert</span></th>
                            <th class="sortable" data-sort="numero_lot" data-bs-toggle="tooltip" title="Trier par numéro de lot">Lot <span class="material-symbols-outlined sort-icon">swap_vert</span></th>
                            <th class="sortable" data-sort="date_peremption" data-bs-toggle="tooltip" title="Trier par date de péremption">Date péremption <span class="material-symbols-outlined sort-icon">swap_vert</span></th>
                            <th class="sortable" data-sort="quantite" data-bs-toggle="tooltip" title="Trier par quantité">Quantité <span class="material-symbols-outlined sort-icon">swap_vert</span></th>
                            <th data-bs-toggle="tooltip" title="Catégorie du médicament">Catégorie</th>
                            <th data-bs-toggle="tooltip" title="Conditionnement (ex : boîte, flacon)">Conditionnement</th>
                            <th data-bs-toggle="tooltip" title="Actions disponibles sur le lot">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (empty($lots)) {
                        echo '<tr><td colspan="7" class="text-muted">Aucune alerte, aucune information à afficher.</td></tr>';
                    } else {
                        foreach ($lots as $lot) {
                            $statut_lot = statut_peremption($lot['date_peremption']);
                            echo '<tr class="'.
                                ($statut_lot==='expiré'?'table-danger':
                                ($statut_lot==='alerte_grave'?'table-warning':
                                ($statut_lot==='alerte'?'table-info':'table-success')))
                                .'">';
                            echo '<td>'.e($lot['nom_produit']).'</td>';
                            echo '<td>'.e($lot['numero_lot']).'</td>';
                            echo '<td>'.e($lot['date_peremption']).'</td>';
                            echo '<td>'.e($lot['quantite']).'</td>';
                            echo '<td>'.e($lot['nom_categorie']).'</td>';
                            echo '<td>'.e($lot['conditionnement']).'</td>';
                            echo '<td>';
                            if ($statut_lot==='expiré') {
                                echo '<span class="badge bg-danger">Périmé</span> ';
                                // Bouton Retirer du stock
                                $url = 'mouvements_stock.php?mode=peremption'
                                    . '&id_produit=' . urlencode($lot['id_produit'])
                                    . '&id_lot=' . urlencode($lot['id_lot'])
                                    . '&quantite=' . urlencode($lot['quantite'])
                                    . '&numero_lot=' . urlencode($lot['numero_lot'])
                                    . '&designation=' . urlencode($lot['nom_produit'])
                                    . '&type_mouvement=sortie'
                                    . '&motif=Retrait%20pour%20péremption';
                                echo '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-outline-danger ms-2" title="Retirer ce lot du stock (péremption)"><i class="bi bi-trash"></i> Retirer du stock</a>';
                            }
                            elseif ($statut_lot==='alerte_grave') echo '<span class="badge bg-warning text-dark">Moins de 30j</span>';
                            elseif ($statut_lot==='alerte') echo '<span class="badge bg-info text-dark">Moins de 90j</span>';
                            else echo '<span class="badge bg-success">Péremption > 90j</span>';
                            echo '</td></tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
