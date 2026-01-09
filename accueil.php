<?php
/**
 * ============================================================================
 * TABLEAU DE BORD MODERNE - STORE SUITE
 * ============================================================================
 * Dashboard professionnel avec couleurs personnalisées et menus selon rôle
 * ============================================================================
 */

require_once 'protection_pages.php';
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

include 'header.php';
?>

<style>
:root {
    --couleur-user-primaire: <?php echo $couleur_primaire; ?>;
    --couleur-user-secondaire: <?php echo $couleur_secondaire; ?>;
}

.stat-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    background: white;
    position: relative;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--couleur-user-primaire);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
}

.stat-card:hover::before {
    transform: scaleY(1);
}

.stat-icon {
    width: 68px;
    height: 68px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%);
    color: white;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.1;
    margin: 12px 0 8px;
    background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-detail {
    color: #495057;
    font-weight: 600;
    font-size: 1rem;
    margin-top: 10px;
}

.welcome-banner {
    background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%);
    border-radius: 24px;
    color: white;
    padding: 3rem 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 100%;
    height: 200%;
    background: rgba(255, 255, 255, 0.08);
    transform: rotate(25deg);
    z-index: 0;
}

.welcome-banner > * {
    position: relative;
    z-index: 1;
}

.welcome-banner h1 {
    position: relative;
    z-index: 1;
}

.welcome-banner .btn {
    position: relative;
    z-index: 2;
}

.action-menu {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    background: white;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #495057;
    font-weight: 600;
}

.action-btn:hover {
    border-color: var(--couleur-user-primaire);
    background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%);
    color: white;
    transform: translateX(5px);
}

.action-btn-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.action-btn:hover .action-btn-icon {
    background: rgba(255, 255, 255, 0.2);
}

.chart-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(var(--tblr-primary-rgb), 0.04);
    transform: scale(1.002);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animated-card {
    animation: fadeInUp 0.6s ease-out;
}

.animated-card:nth-child(1) { animation-delay: 0.1s; }
.animated-card:nth-child(2) { animation-delay: 0.2s; }
.animated-card:nth-child(3) { animation-delay: 0.3s; }
.animated-card:nth-child(4) { animation-delay: 0.4s; }
</style>

<div class="page-wrapper">
    <div class="container-xl">
        <!-- Bannière de bienvenue -->
        <div class="welcome-banner">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-3" style="opacity: 0.9;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" />
                            <path d="M9 11v-5a3 3 0 0 1 6 0v5" />
                        </svg>
                        <h1 style="font-size: 2.2rem; font-weight: 700; margin: 0;">
                            Bienvenue, <?php echo e($user_data['nom_complet']); ?> !
                        </h1>
                    </div>
                    <p class="mb-0 opacity-90" style="font-size: 1.05rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /></svg>
                        <?php
                        $jours_fr = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        $mois_fr = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
                        echo $jours_fr[date('w')] . ' ' . date('j') . ' ' . $mois_fr[date('n')] . ' ' . date('Y');
                        ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="vente.php" class="btn btn-white btn-lg shadow-sm d-inline-flex align-items-center" style="font-weight: 600; cursor: pointer; text-decoration: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        Nouvelle vente
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu d'actions rapides -->
        <div class="action-menu">
            <h3 class="mb-3" style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">Actions rapides</h3>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <a href="vente.php" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                        </div>
                        <span>Nouvelle vente</span>
                    </a>
                </div>
                <?php if ($is_admin): ?>
                <div class="col-md-3 col-sm-6">
                    <a href="listes.php?page=produits" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" /><line x1="12" y1="12" x2="20" y2="7.5" /><line x1="12" y1="12" x2="12" y2="21" /><line x1="12" y1="12" x2="4" y2="7.5" /></svg>
                        </div>
                        <span>Produits</span>
                    </a>
                </div>
                <?php endif; ?>
                <div class="col-md-3 col-sm-6">
                    <a href="listes.php?page=clients" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                        </div>
                        <span>Clients</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="rapports.php" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                        </div>
                        <span>Rapports</span>
                    </a>
                </div>
            </div>
            <?php if ($is_admin): ?>
            <div class="row g-3 mt-2">
                <div class="col-md-3 col-sm-6">
                    <a href="listes.php?page=categories" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="6" height="6" rx="1" /><rect x="14" y="4" width="6" height="6" rx="1" /><rect x="4" y="14" width="6" height="6" rx="1" /><rect x="14" y="14" width="6" height="6" rx="1" /></svg>
                        </div>
                        <span>Catégories</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="listes.php?page=utilisateurs" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11l2 2l4 -4" /></svg>
                        </div>
                        <span>Utilisateurs</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="listes.php?page=mouvements" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                        </div>
                        <span>Mouvements stock</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="notification.php" class="action-btn w-100">
                        <div class="action-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                        </div>
                        <span>Notifications</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Cartes de statistiques principales -->
        <div class="row row-deck row-cards mb-4">
            <!-- Ventes aujourd'hui -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Ventes du jour</div>
                        <div class="stat-value"><?php echo number_format($stats_jour['nombre_ventes']); ?></div>
                        <div class="stat-detail">
                            <strong><?php echo format_montant($stats_jour['chiffre_affaires']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ventes ce mois -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="3" y1="21" x2="21" y2="21" /><path d="M3 10l6 -6l4 4l4 -4l4 4v11h-18z" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Ventes du mois</div>
                        <div class="stat-value"><?php echo number_format($stats_mois['nombre_ventes']); ?></div>
                        <div class="stat-detail">
                            <strong><?php echo format_montant($stats_mois['chiffre_affaires']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($is_admin): ?>
            <!-- Bénéfice aujourd'hui -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Bénéfice jour <span class="badge bg-teal ms-2">Admin</span></div>
                        <div class="stat-value"><?php echo format_montant($benefices_jour); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Bénéfice ce mois -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Bénéfice mois <span class="badge bg-cyan ms-2">Admin</span></div>
                        <div class="stat-value"><?php echo format_montant($benefices_mois); ?></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Produits en stock -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" /><line x1="12" y1="12" x2="20" y2="7.5" /><line x1="12" y1="12" x2="12" y2="21" /><line x1="12" y1="12" x2="4" y2="7.5" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Produits</div>
                        <div class="stat-value"><?php echo number_format($stats_stock['total_produits']); ?></div>
                        <div class="stat-detail">
                            <?php echo number_format($stats_stock['quantite_totale']); ?> unités
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Valeur du stock -->
            <div class="col-sm-6 col-lg-3 animated-card">
                <div class="stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="7" y="9" width="14" height="10" rx="2" /><circle cx="14" cy="14" r="2" /><path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" /></svg>
                            </div>
                        </div>
                        <div class="stat-label">Valeur stock</div>
                        <div class="stat-value"><?php echo format_montant($stats_stock['valeur_stock']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row row-deck row-cards">
            <!-- Dernières ventes -->
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%); color: white;">
                        <h3 class="card-title mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                            Dernières ventes
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover card-table table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th>N° Facture</th>
                                        <th>Client</th>
                                        <th>Vendeur</th>
                                        <th class="text-end">Montant</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dernieres_ventes)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="10" x2="9.01" y2="10" /><line x1="15" y1="10" x2="15.01" y2="10" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                                                <div>Aucune vente enregistrée</div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dernieres_ventes as $vente): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary-lt"><?php echo e($vente['numero_facture']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2" style="background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%); color: white;">
                                                            <?php echo strtoupper(substr($vente['nom_client'] ?: 'C', 0, 1)); ?>
                                                        </div>
                                                        <?php echo e($vente['nom_client'] ?: 'Client Comptoir'); ?>
                                                    </div>
                                                </td>
                                                <td class="text-muted"><?php echo e($vente['vendeur']); ?></td>
                                                <td class="text-end">
                                                    <strong><?php echo format_montant($vente['montant_total']); ?></strong>
                                                </td>
                                                <td>
                                                    <div class="text-muted">
                                                        <?php echo date('d/m/Y \u00e0 H:i', strtotime($vente['date_vente'])); ?>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="facture.php?id=<?php echo $vente['id_vente']; ?>" class="btn btn-sm btn-primary" title="Voir la facture">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($dernieres_ventes)): ?>
                        <div class="card-footer text-center">
                            <a href="listes.php?page=ventes" class="btn btn-link">
                                Voir toutes les ventes
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Produits en alerte + Top produits -->
            <div class="col-lg-4">
                <!-- Alertes de stock -->
                <div class="chart-card mb-3">
                    <div class="card-header bg-danger" style="color: white;">
                        <h3 class="card-title mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                            Alertes stock
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($produits_alerte)): ?>
                            <div class="text-center text-muted py-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                <div>Aucun produit en alerte</div>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($produits_alerte as $produit): ?>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <strong><?php echo e($produit['nom_produit']); ?></strong>
                                                <div class="text-muted small mt-1">
                                                    Seuil : <?php echo $produit['seuil_alerte']; ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <?php if ($produit['niveau_alerte'] == 'rupture'): ?>
                                                <span class="badge bg-danger" style="font-size: 0.85rem; padding: 8px 14px;">
                                                    <?php echo $produit['quantite_stock']; ?>
                                                </span>
                                                <?php elseif ($produit['niveau_alerte'] == 'critique'): ?>
                                                <span class="badge bg-orange" style="font-size: 0.85rem; padding: 8px 14px;">
                                                    <?php echo $produit['quantite_stock']; ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="badge bg-yellow" style="font-size: 0.85rem; padding: 8px 14px;">
                                                    <?php echo $produit['quantite_stock']; ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top produits -->
                <div class="chart-card">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%); color: white;">
                        <h3 class="card-title mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l-8 4l8 4l8 -4l-8 -4" /><path d="M4 14l8 4l8 -4" /></svg>
                            Top produits du mois
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($top_produits)): ?>
                            <div class="text-center text-muted py-4">
                                Aucune vente ce mois
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($top_produits as $index => $produit): ?>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar" style="background: linear-gradient(135deg, var(--couleur-user-primaire) 0%, var(--couleur-user-secondaire) 100%); color: white; font-weight: 700;">
                                                    <?php echo ($index + 1); ?>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <strong><?php echo e($produit['nom_produit']); ?></strong>
                                                <div class="text-muted small mt-1">
                                                    <?php echo number_format($produit['quantite_vendue']); ?> vendus
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="text-end">
                                                    <strong style="color: var(--couleur-user-primaire);">
                                                        <?php echo format_montant($produit['montant_total']); ?>
                                                    </strong>
                                                </div>
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
    </div>
</div>

<?php include 'footer.php'; ?>
