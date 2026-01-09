<?php
/**
 * ============================================================================
 * ACCUEIL - TABLEAU DE BORD PRINCIPAL
 * ============================================================================
 * 
 * Affiche les statistiques principales du système
 * 
 * ============================================================================
 */

// Protection de la page et chargement des configurations
require_once 'protection_pages.php';

// Titre de la page
$page_title = 'Tableau de bord';

// Récupération des statistiques du jour
$stats_jour = db_fetch_one("
    SELECT 
        COUNT(id_vente) as nombre_ventes,
        COALESCE(SUM(montant_total), 0) as chiffre_affaires
    FROM ventes 
    WHERE DATE(date_vente) = CURDATE() AND statut = 'validee'
");

// Récupération des statistiques du mois
$stats_mois = db_fetch_one("
    SELECT 
        COUNT(id_vente) as nombre_ventes,
        COALESCE(SUM(montant_total), 0) as chiffre_affaires
    FROM ventes 
    WHERE MONTH(date_vente) = MONTH(CURDATE()) 
    AND YEAR(date_vente) = YEAR(CURDATE())
    AND statut = 'validee'
");

// Bénéfices (uniquement pour admin)
$benefices_jour = 0;
$benefices_mois = 0;
if ($is_admin) {
    $benefice_data_jour = db_fetch_one("
        SELECT COALESCE(SUM(vd.benefice_ligne), 0) as benefice_total
        FROM ventes_details vd
        INNER JOIN ventes v ON vd.id_vente = v.id_vente
        WHERE DATE(v.date_vente) = CURDATE() AND v.statut = 'validee'
    ");
    $benefices_jour = $benefice_data_jour['benefice_total'];
    
    $benefice_data_mois = db_fetch_one("
        SELECT COALESCE(SUM(vd.benefice_ligne), 0) as benefice_total
        FROM ventes_details vd
        INNER JOIN ventes v ON vd.id_vente = v.id_vente
        WHERE MONTH(v.date_vente) = MONTH(CURDATE())
        AND YEAR(v.date_vente) = YEAR(CURDATE())
        AND v.statut = 'validee'
    ");
    $benefices_mois = $benefice_data_mois['benefice_total'];
}

// Statistiques stock
$stats_stock = db_fetch_one("
    SELECT 
        COUNT(*) as total_produits,
        COALESCE(SUM(quantite_stock), 0) as quantite_totale,
        COALESCE(SUM(quantite_stock * prix_vente), 0) as valeur_stock
    FROM produits 
    WHERE est_actif = 1
");

// Produits en alerte
$produits_alerte = db_fetch_all("
    SELECT * FROM vue_produits_alertes 
    ORDER BY 
        CASE niveau_alerte
            WHEN 'rupture' THEN 1
            WHEN 'critique' THEN 2
            WHEN 'faible' THEN 3
        END
    LIMIT 5
");

// Dernières ventes
$dernieres_ventes = db_fetch_all("
    SELECT v.*, c.nom_client, u.nom_complet as vendeur
    FROM ventes v
    LEFT JOIN clients c ON v.id_client = c.id_client
    LEFT JOIN utilisateurs u ON v.id_vendeur = u.id_utilisateur
    WHERE v.statut = 'validee'
    ORDER BY v.date_vente DESC
    LIMIT 5
");

// Top 5 produits du mois
$top_produits = db_fetch_all("
    SELECT 
        p.nom_produit,
        p.code_produit,
        SUM(vd.quantite) as quantite_vendue,
        SUM(vd.prix_total) as montant_total
    FROM ventes_details vd
    INNER JOIN ventes v ON vd.id_vente = v.id_vente
    INNER JOIN produits p ON vd.id_produit = p.id_produit
    WHERE MONTH(v.date_vente) = MONTH(CURDATE())
    AND YEAR(v.date_vente) = YEAR(CURDATE())
    AND v.statut = 'validee'
    GROUP BY p.id_produit
    ORDER BY quantite_vendue DESC
    LIMIT 5
");

// Inclusion du header
include 'header.php';
?>

<div class="container-xl">
    <!-- En-tête de page -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Tableau de bord</h2>
                <div class="text-muted mt-1">Vue d'ensemble de votre activité</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="vente.php" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                        Nouvelle vente
                    </a>
                    <a href="vente.php" class="btn btn-primary d-sm-none btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Cartes de statistiques -->
        <div class="row row-deck row-cards mb-3">
            <!-- Ventes du jour -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Ventes aujourd'hui</div>
                        </div>
                        <div class="h1 mb-0 mt-1"><?php echo number_format($stats_jour['nombre_ventes']); ?></div>
                        <div class="d-flex mb-2">
                            <div class="text-muted">
                                <?php echo format_montant($stats_jour['chiffre_affaires']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ventes du mois -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Ventes ce mois</div>
                        </div>
                        <div class="h1 mb-0 mt-1"><?php echo number_format($stats_mois['nombre_ventes']); ?></div>
                        <div class="d-flex mb-2">
                            <div class="text-muted">
                                <?php echo format_montant($stats_mois['chiffre_affaires']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($is_admin): ?>
            <!-- Bénéfice du jour (Admin seulement) -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Bénéfice jour</div>
                            <div class="ms-auto">
                                <span class="badge bg-green">Admin</span>
                            </div>
                        </div>
                        <div class="h1 mb-0 mt-1 text-success"><?php echo format_montant($benefices_jour); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Bénéfice du mois (Admin seulement) -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Bénéfice mois</div>
                            <div class="ms-auto">
                                <span class="badge bg-green">Admin</span>
                            </div>
                        </div>
                        <div class="h1 mb-0 mt-1 text-success"><?php echo format_montant($benefices_mois); ?></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Produits en stock -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Produits en stock</div>
                        </div>
                        <div class="h1 mb-0 mt-1"><?php echo number_format($stats_stock['total_produits']); ?></div>
                        <div class="d-flex mb-2">
                            <div class="text-muted">
                                <?php echo number_format($stats_stock['quantite_totale']); ?> unités
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Valeur du stock -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Valeur du stock</div>
                        </div>
                        <div class="h1 mb-0 mt-1"><?php echo format_montant($stats_stock['valeur_stock']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="row row-deck row-cards">
            <!-- Dernières ventes -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dernières ventes</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Client</th>
                                    <th>Vendeur</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dernieres_ventes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucune vente enregistrée</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($dernieres_ventes as $vente): ?>
                                <tr>
                                    <td><span class="text-muted"><?php echo e($vente['numero_facture']); ?></span></td>
                                    <td><?php echo e($vente['nom_client'] ?: 'Client Comptoir'); ?></td>
                                    <td class="text-muted"><?php echo e($vente['vendeur']); ?></td>
                                    <td class="fw-bold"><?php echo format_montant($vente['montant_total']); ?></td>
                                    <td class="text-muted"><?php echo format_date($vente['date_vente']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Top produits -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top produits ce mois</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($top_produits)): ?>
                        <div class="text-center text-muted py-3">Aucune vente ce mois</div>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($top_produits as $produit): ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="fw-bold"><?php echo e($produit['nom_produit']); ?></div>
                                        <div class="text-muted small"><?php echo e($produit['code_produit']); ?></div>
                                    </div>
                                    <div class="col-auto text-end">
                                        <div class="fw-bold"><?php echo number_format($produit['quantite_vendue']); ?></div>
                                        <div class="text-muted small"><?php echo format_montant($produit['montant_total']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alertes stock -->
        <?php if (!empty($produits_alerte)): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Alertes de stock</h3>
                        <div class="card-actions">
                            <a href="notification.php" class="btn btn-sm btn-primary">Voir toutes les alertes</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Stock actuel</th>
                                    <th>Seuil alerte</th>
                                    <th>Niveau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produits_alerte as $produit): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($produit['nom_produit']); ?></div>
                                        <div class="text-muted small"><?php echo e($produit['code_produit']); ?></div>
                                    </td>
                                    <td><?php echo e($produit['nom_categorie'] ?: '-'); ?></td>
                                    <td class="fw-bold"><?php echo number_format($produit['quantite_stock']); ?></td>
                                    <td class="text-muted"><?php echo number_format($produit['seuil_alerte']); ?></td>
                                    <td>
                                        <?php if ($produit['niveau_alerte'] == 'rupture'): ?>
                                        <span class="badge bg-red">Rupture</span>
                                        <?php elseif ($produit['niveau_alerte'] == 'critique'): ?>
                                        <span class="badge bg-orange">Critique</span>
                                        <?php else: ?>
                                        <span class="badge bg-yellow">Faible</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
