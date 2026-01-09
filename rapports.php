<?php
/**
 * PAGE RAPPORTS - STORE SUITE
 * Rapports et statistiques de vente
 */
require_once 'protection_pages.php';
$page_title = 'Rapports';

// Périodes prédéfinies
$periode = $_GET['periode'] ?? 'today';
$date_debut = $_GET['date_debut'] ?? date('Y-m-d');
$date_fin = $_GET['date_fin'] ?? date('Y-m-d');

// Calcul des dates selon la période
switch ($periode) {
    case 'today':
        $date_debut = $date_fin = date('Y-m-d');
        break;
    case 'week':
        $date_debut = date('Y-m-d', strtotime('monday this week'));
        $date_fin = date('Y-m-d');
        break;
    case 'month':
        $date_debut = date('Y-m-01');
        $date_fin = date('Y-m-d');
        break;
    case 'year':
        $date_debut = date('Y-01-01');
        $date_fin = date('Y-m-d');
        break;
}

// Statistiques globales
$stats = db_fetch_one("
    SELECT 
        COUNT(*) as total_ventes,
        COALESCE(SUM(montant_total), 0) as chiffre_affaires,
        COALESCE(AVG(montant_total), 0) as panier_moyen
    FROM ventes
    WHERE DATE(date_vente) BETWEEN ? AND ?
    AND statut = 'validee'
", [$date_debut, $date_fin]);

// Top 10 produits
$top_produits = db_fetch_all("
    SELECT 
        p.nom_produit,
        p.prix_vente,
        SUM(vd.quantite) as quantite_vendue,
        SUM(vd.prix_total) as montant_total
    FROM ventes_details vd
    INNER JOIN produits p ON vd.id_produit = p.id_produit
    INNER JOIN ventes v ON vd.id_vente = v.id_vente
    WHERE DATE(v.date_vente) BETWEEN ? AND ?
    AND v.statut = 'validee'
    GROUP BY p.id_produit
    ORDER BY quantite_vendue DESC
    LIMIT 10
", [$date_debut, $date_fin]);

// Ventes par jour
$ventes_jour = db_fetch_all("
    SELECT 
        DATE(date_vente) as jour,
        COUNT(*) as nombre_ventes,
        SUM(montant_total) as montant
    FROM ventes
    WHERE DATE(date_vente) BETWEEN ? AND ?
    AND statut = 'validee'
    GROUP BY DATE(date_vente)
    ORDER BY jour ASC
", [$date_debut, $date_fin]);

include 'header.php';
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="4" y1="19" x2="20" y2="19"/>
                        <polyline points="4 15 8 9 12 11 16 6 20 10"/>
                    </svg>
                    Rapports
                </h2>
            </div>
            <div class="col-auto">
                <a href="accueil.php" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <line x1="5" y1="12" x2="9" y2="16"/>
                        <line x1="5" y1="12" x2="9" y2="8"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select" onchange="toggleCustomDates(this.value)">
                        <option value="today" <?php echo $periode === 'today' ? 'selected' : ''; ?>>Aujourd'hui</option>
                        <option value="week" <?php echo $periode === 'week' ? 'selected' : ''; ?>>Cette semaine</option>
                        <option value="month" <?php echo $periode === 'month' ? 'selected' : ''; ?>>Ce mois</option>
                        <option value="year" <?php echo $periode === 'year' ? 'selected' : ''; ?>>Cette année</option>
                        <option value="custom" <?php echo $periode === 'custom' ? 'selected' : ''; ?>>Personnalisée</option>
                    </select>
                </div>
                <div class="col-md-2" id="date_debut_col" style="display: <?php echo $periode === 'custom' ? 'block' : 'none'; ?>;">
                    <label class="form-label">Date début</label>
                    <input type="date" name="date_debut" class="form-control" value="<?php echo $date_debut; ?>">
                </div>
                <div class="col-md-2" id="date_fin_col" style="display: <?php echo $periode === 'custom' ? 'block' : 'none'; ?>;">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="date_fin" class="form-control" value="<?php echo $date_fin; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7"/><line x1="21" y1="21" x2="15" y2="15"/></svg>
                        Filtrer
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')" data-bs-toggle="tooltip" title="Télécharger en Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><polyline points="9 14 12 17 15 14"/></svg>
                            Excel
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="exportReport('pdf')" data-bs-toggle="tooltip" title="Télécharger en PDF">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"/><path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6"/><path d="M17 18h2"/><path d="M20 15h-3v6"/><path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z"/></svg>
                            PDF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Boutons de types de rapports -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><line x1="9" y1="12" x2="9.01" y2="12"/><line x1="13" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="9.01" y2="16"/><line x1="13" y1="16" x2="15" y2="16"/></svg>
                Types de rapports disponibles
            </h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card card-link card-link-pop">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg bg-blue-lt mb-3 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M12 12l0 9"/><path d="M12 12l-8 -4.5"/></svg>
                            </div>
                            <h4>Liste des produits</h4>
                            <p class="text-muted mb-3">Tous les produits avec stock et prix</p>
                            <div class="btn-group btn-group-sm">
                                <a href="ajax/export_excel.php?type=produits" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=produits" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-link card-link-pop">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg bg-green-lt mb-3 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                            </div>
                            <h4>Rapport des ventes</h4>
                            <p class="text-muted mb-3">Détail de toutes les ventes</p>
                            <div class="btn-group btn-group-sm">
                                <a href="ajax/export_excel.php?type=ventes&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=ventes&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-link card-link-pop">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg bg-yellow-lt mb-3 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"/><path d="M12 3v3m0 12v3"/></svg>
                            </div>
                            <h4>Rapport des bénéfices</h4>
                            <p class="text-muted mb-3">Marges et rentabilité par produit</p>
                            <div class="btn-group btn-group-sm">
                                <a href="ajax/export_excel.php?type=benefices&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=benefices&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-link card-link-pop">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg bg-purple-lt mb-3 mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2"/><line x1="16" y1="3" x2="16" y2="7"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="4" y1="11" x2="20" y2="11"/><rect x="8" y="15" width="2" height="2"/></svg>
                            </div>
                            <h4>Rapport par catégories</h4>
                            <p class="text-muted mb-3">Performance par catégorie</p>
                            <div class="btn-group btn-group-sm">
                                <a href="ajax/export_excel.php?type=categories&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=categories&date_debut=<?php echo $date_debut; ?>&date_fin=<?php echo $date_fin; ?>" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-0"><?php echo $stats['total_ventes']; ?></h3>
                            <div class="text-muted">Nombre de ventes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-success-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"/>
                                <path d="M12 3v3m0 12v3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-0"><?php echo format_montant($stats['chiffre_affaires']); ?></h3>
                            <div class="text-muted">Chiffre d'affaires</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-info-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="6" cy="19" r="2"/>
                                <circle cx="17" cy="19" r="2"/>
                                <path d="M17 17h-11v-14h-2"/>
                                <path d="M6 5l14 1l-1 7h-13"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="mb-0"><?php echo format_montant($stats['panier_moyen']); ?></h3>
                            <div class="text-muted">Panier moyen</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Évolution des ventes</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Produits</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($top_produits)): ?>
                        <div class="list-group-item text-center text-muted py-4">
                            Aucune vente
                        </div>
                        <?php else: ?>
                        <?php foreach ($top_produits as $index => $produit): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2"><?php echo $index + 1; ?></span>
                                    <strong><?php echo e($produit['nom_produit']); ?></strong>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small"><?php echo $produit['quantite_vendue']; ?> unités</div>
                                    <strong><?php echo format_montant($produit['montant_total']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Toggle custom dates
function toggleCustomDates(periode) {
    const dateDebutCol = document.getElementById('date_debut_col');
    const dateFinCol = document.getElementById('date_fin_col');
    if (periode === 'custom') {
        dateDebutCol.style.display = 'block';
        dateFinCol.style.display = 'block';
    } else {
        dateDebutCol.style.display = 'none';
        dateFinCol.style.display = 'none';
        // Auto-submit when not custom
        document.querySelector('form').submit();
    }
}

// Export report
function exportReport(format) {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Sélectionner le type de rapport à exporter
    const types = ['ventes', 'benefices', 'categories', 'produits', 'stock'];
    const typeSelect = prompt('Choisissez le type de rapport:\n1. Ventes\n2. Bénéfices\n3. Catégories\n4. Produits\n5. État du stock', '1');
    
    if (!typeSelect || typeSelect < 1 || typeSelect > 5) {
        return;
    }
    
    const type = types[parseInt(typeSelect) - 1];
    const url = `ajax/export_${format}.php?type=${type}&${params.toString()}`;
    window.open(url, '_blank');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Sales chart
const salesData = <?php echo json_encode($ventes_jour); ?>;
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: salesData.map(d => new Date(d.jour).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })),
        datasets: [{
            label: 'Ventes (<?php echo $devise; ?>)',
            data: salesData.map(d => d.montant),
            borderColor: '<?php echo $couleur_primaire; ?>',
            backgroundColor: '<?php echo $couleur_primaire; ?>20',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => value.toLocaleString() + ' <?php echo $devise; ?>'
                }
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
