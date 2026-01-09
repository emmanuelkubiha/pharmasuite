<?php
/**
 * ============================================================================
 * HEADER - MENU DE NAVIGATION ET EN-TÊTE
 * ============================================================================
 * 
 * Importance : Fichier inclus sur toutes les pages du système
 *              Contient le menu de navigation, logo, notifications et profil
 * 
 * Variables requises (depuis protection_pages.php) :
 * - $config : Configuration du système
 * - $user_data : Informations de l'utilisateur connecté
 * - $is_admin : Booléen indiquant si l'utilisateur est admin
 * - $notifications_count : Nombre de notifications non lues
 * - $products_alert_count : Nombre de produits en alerte
 * 
 * ============================================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($nom_boutique); ?> - Système de Gestion de Stock">
    <title><?php echo e($nom_boutique); ?> - <?php echo isset($page_title) ? e($page_title) : 'Gestion de Stock'; ?></title>
    
    <!-- Tabler CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/tabler.min.css" rel="stylesheet"/>
    <link href="<?php echo BASE_URL; ?>assets/css/tabler-vendors.min.css" rel="stylesheet"/>
    
    <!-- Loader CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/loader.css" rel="stylesheet"/>
    
    <!-- Styles personnalisés -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet"/>
    
    <!-- Variables CSS dynamiques depuis la BD -->
    <style>
        :root {
            --couleur-primaire: <?php echo $couleur_primaire; ?>;
            --couleur-secondaire: <?php echo $couleur_secondaire; ?>;
    </style>
</head>
<body>
    <!-- Inclusion du Page Loader -->
    <?php include 'loading.php'; ?>
    
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md navbar-light d-print-none sticky-top bg-white border-bottom">
            <div class="container-xl">
                <!-- Bouton menu mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Logo / Nom de la boutique -->
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3 m-0">
                    <?php if (!empty($logo_boutique) && file_exists('uploads/logos/' . $logo_boutique)): ?>
                        <a href="accueil.php">
                            <img src="<?php echo BASE_URL . 'uploads/logos/' . e($logo_boutique); ?>" 
                                 class="navbar-brand-logo"
                                 alt="<?php echo e($nom_boutique); ?>">
                        </a>
                    <?php else: ?>
                        <a href="accueil.php" class="navbar-brand-text"><?php echo e($nom_boutique); ?></a>
                    <?php endif; ?>
                </h1>
                
                <!-- Actions navbar (droite) -->
                <div class="navbar-nav flex-row order-md-last">
                    
                    <!-- Bouton alerte stock -->
                    <?php if ($products_alert_count > 0): ?>
                    <div class="nav-item d-none d-md-flex me-2">
                        <a href="notification.php" class="btn btn-outline-danger btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                            <span class="d-none d-lg-inline">Alerte<?php echo $products_alert_count > 1 ? 's' : ''; ?></span>
                            <span class="badge bg-red ms-1"><?php echo $products_alert_count; ?></span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Notification bell -->
                    <div class="nav-item dropdown d-none d-md-flex me-2">
                        <a href="#" class="nav-link px-2 position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                            <?php if ($products_alert_count > 0): ?>
                            <span class="notification-badge"><?php echo $products_alert_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <div class="dropdown-header">Notifications</div>
                            <?php if ($products_alert_count > 0): ?>
                            <a href="notification.php" class="dropdown-item">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                                    </div>
                                    <div class="flex-fill ms-2">
                                        <strong>Alerte Stock</strong>
                                        <div class="text-muted small"><?php echo $products_alert_count; ?> produit<?php echo $products_alert_count > 1 ? 's' : ''; ?> en alerte</div>
                                    </div>
                                </div>
                            </a>
                            <?php else: ?>
                            <div class="dropdown-item text-muted">Aucune notification</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Menu utilisateur -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Menu utilisateur">
                            <span class="avatar avatar-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><circle cx="12" cy="10" r="3" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
                            </span>
                            <div class="d-none d-xl-block ps-2">
                                <div class="fw-bold"><?php echo e($user_name); ?></div>
                                <div class="mt-1 small text-muted"><?php echo $is_admin ? 'Administrateur' : 'Vendeur'; ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><circle cx="12" cy="10" r="3" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
                                Mon profil
                            </a>
                            <?php if ($is_admin): ?>
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><circle cx="12" cy="12" r="3" /></svg>
                                Paramètres
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                Utilisateurs
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="deconnexion.php" class="dropdown-item text-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M7 12h14l-3 -3m0 6l3 -3" /></svg>
                                Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Menu de navigation -->
        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light bg-light border-bottom">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'accueil.php') ? 'active' : ''; ?>">
                                <a class="nav-link" href="accueil.php">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                                    </span>
                                    <span class="nav-link-title">Accueil</span>
                                </a>
                            </li>
                            
                            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'vente.php') ? 'active' : ''; ?>">
                                <a class="nav-link" href="vente.php">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                    </span>
                                    <span class="nav-link-title">Vente</span>
                                </a>
                            </li>
                            
                            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'listes.php') ? 'active' : ''; ?>">
                                <a class="nav-link" href="listes.php">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><rect x="9" y="3" width="6" height="4" rx="2" /><line x1="9" y1="12" x2="9.01" y2="12" /><line x1="13" y1="12" x2="15" y2="12" /><line x1="9" y1="16" x2="9.01" y2="16" /><line x1="13" y1="16" x2="15" y2="16" /></svg>
                                    </span>
                                    <span class="nav-link-title">Produits</span>
                                </a>
                            </li>
                            
                            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'rapports.php') ? 'active' : ''; ?>">
                                <a class="nav-link" href="rapports.php">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="12" width="6" height="8" rx="1" /><rect x="9" y="8" width="6" height="12" rx="1" /><rect x="15" y="4" width="6" height="16" rx="1" /></svg>
                                    </span>
                                    <span class="nav-link-title">Rapports</span>
                                </a>
                            </li>
                            
                            <?php if ($is_admin): ?>
                            <li class="nav-item dropdown <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['tableau_de_bord.php'])) ? 'active' : ''; ?>">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                    </span>
                                    <span class="nav-link-title">Administration</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="tableau_de_bord.php">Statistiques</a>
                                    <a class="dropdown-item" href="#">Utilisateurs</a>
                                    <a class="dropdown-item" href="#">Paramètres</a>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenu de la page -->
        <div class="page-wrapper">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Logo et nom de la boutique -->
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <?php if (!empty($logo_boutique) && file_exists(LOGO_PATH . $logo_boutique)): ?>
                <a href="accueil.php">
                    <img src="<?php echo BASE_URL . 'uploads/logos/' . $logo_boutique; ?>" 
                         height="36" 
                         alt="<?php echo e($nom_boutique); ?>" 
                         style="max-width: 150px; object-fit: contain;">
                </a>
            <?php else: ?>
                <a href="accueil.php" class="titre"><?php echo e($nom_boutique); ?></a>
            <?php endif; ?>
        </h1>
        
        <!-- Actions navbar (droite) -->
        <div class="navbar-nav flex-row order-md-last">
            
            <!-- Bouton notifications -->
            <?php if ($products_alert_count > 0): ?>
            <div class="nav-item d-none d-md-flex me-3">
                <a href="notification.php" class="btn btn-outline-danger position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                    Alerte<?php echo $products_alert_count > 1 ? 's' : ''; ?> Stock
                    <span class="badge bg-red ms-2"><?php echo $products_alert_count; ?></span>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Icône notification avec dropdown -->
            <?php if ($products_alert_count > 0): ?>
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0 position-relative" data-bs-toggle="dropdown" tabindex="-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                    <span class="notification-badge"><?php echo $products_alert_count; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-body">
                            <strong>⚠️ Alertes Stock</strong><br>
                            <?php echo $products_alert_count; ?> produit<?php echo $products_alert_count > 1 ? 's ont' : ' a'; ?> un stock faible ou épuisé.<br>
                            <a href="notification.php" class="btn btn-sm btn-primary mt-2">Voir les alertes</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Menu utilisateur -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background: <?php echo $couleur_primaire; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11l2 2l4 -4" /></svg>
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div><?php echo e($user_name); ?></div>
                        <div class="mt-1 small text-muted">
                            <?php echo $is_admin ? 'Administrateur' : 'Vendeur'; ?>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="1" /><line x1="12" y1="8" x2="12" y2="8.01" /><line x1="12" y1="16" x2="12" y2="16.01" /></svg>
                        Mon profil
                    </a>
                    <?php if ($is_admin): ?>
                    <a href="#" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><circle cx="12" cy="12" r="3" /></svg>
                        Paramètres
                    </a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a href="deconnexion.php" class="dropdown-item text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M7 12h14l-3 -3m0 6l3 -3" /></svg>
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Menu de navigation principal -->
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <!-- Accueil -->
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'accueil.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="accueil.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                            </span>
                            <span class="nav-link-title">Accueil</span>
                        </a>
                    </li>
                    
                    <!-- Produits -->
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'listes.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="listes.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="9" y1="12" x2="15" y2="12" /></svg>
                            </span>
                            <span class="nav-link-title">Produits</span>
                        </a>
                    </li>
                    
                    <!-- Tableau de bord (Statistiques) -->
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'tableau_de_bord.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="tableau_de_bord.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="3" width="18" height="18" rx="2" /><line x1="9" y1="3" x2="9" y2="21" /><line x1="15" y1="3" x2="15" y2="21" /><line x1="3" y1="9" x2="21" y2="9" /><line x1="3" y1="15" x2="21" y2="15" /></svg>
                            </span>
                            <span class="nav-link-title">Statistiques</span>
                        </a>
                    </li>
                    
                    <!-- Ventes -->
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'vente.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="vente.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                            </span>
                            <span class="nav-link-title">Ventes</span>
                        </a>
                    </li>
                    
                    <!-- Rapports -->
                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'rapports.php') ? 'active' : ''; ?>">
                        <a class="nav-link" href="rapports.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><rect x="9" y="3" width="6" height="4" rx="2" /><line x1="9" y1="12" x2="9.01" y2="12" /><line x1="13" y1="12" x2="15" y2="12" /><line x1="9" y1="16" x2="9.01" y2="16" /><line x1="13" y1="16" x2="15" y2="16" /></svg>
                            </span>
                            <span class="nav-link-title">Rapports</span>
                        </a>
                    </li>
                    
                    <!-- Utilisateurs (Admin uniquement) -->
                    <?php if ($is_admin): ?>
                    <li class="nav-item dropdown <?php echo (basename($_SERVER['PHP_SELF']) == 'utilisateurs.php') ? 'active' : ''; ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </span>
                            <span class="nav-link-title">Administration</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="utilisateurs.php">
                                Gestion des utilisateurs
                            </a>
                            <a class="dropdown-item" href="parametres.php">
                                Paramètres système
                            </a>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
        </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-team4"><!-- Download SVG icon from http://tabler-icons.io/i/edit -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" /><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" /><line x1="16" y1="5" x2="19" y2="8" /></svg>
            Modifier mon profil
        </a>
        <div class="dropdown-divider"></div>
        <a href="deconnexion.php" class="dropdown-item btn_deconnexion"><!-- Download SVG icon from http://tabler-icons.io/i/circle-x -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
            Déconnexion
        </a>
        </div>
    </div>
    </div>
</div>
</header>
<div class="navbar-expand-md">
<div class="collapse navbar-collapse" id="navbar-menu">
    <div class="navbar navbar-light">
    <div class="container-xl">
        <ul class="navbar-nav">
        <li class="nav-item <?php if(!isset($_GET['page']) or $_GET['page'] == 1)echo 'active';?>">
                <a class="nav-link" href="accueil.php" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                    </span>
                    <span class="nav-link-title">
                        Accueil
                    </span>
                </a>
        </li>

        <li class="nav-item <?php if(isset($_GET['page']) and $_GET['page'] == 2)echo 'active';?>">
            <a class="nav-link" href="listes.php?page=2" >
                <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/align-justified -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="6" x2="20" y2="6" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="18" x2="16" y2="18" /></svg>
                </span>
                <span class="nav-link-title">
                    Listes des produits
                </span>
            </a>
        </li>

        <li class="nav-item dropdown <?php if(isset($_GET['page']) and $_GET['page'] == 3)echo 'active';?>">
            <?php if($ET['NIVEAU'] == 1){ ?>
                <a class="nav-link" href="tableau_de_bord.php?page=3">
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/chart-pie -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a0.9 .9 0 0 0 -1 -.8" /><path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5" /></svg>
                    </span>
                    <span class="nav-link-title">
                        Tableaux de bord
                    </span>
                </a>
            <?php }else{} ?>
        </li>
        <li class="nav-item <?php if(isset($_GET['page']) and $_GET['page'] == 4)echo 'active';?>">
            <a class="nav-link" href="vente.php?page=4" >
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
	            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2" /><circle cx="17" cy="19" r="2" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
            </span>
            <span class="nav-link-title">
                Ventes
            </span>
            </a>
        </li>
        <li class="nav-item dropdown <?php if(isset($_GET['page']) and $_GET['page'] == 5)echo 'active';?>">
            <?php if($ET['NIVEAU'] == 1){ ?>
                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" role="button" aria-expanded="false" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/file-text -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><line x1="9" y1="9" x2="10" y2="9" /><line x1="9" y1="13" x2="15" y2="13" /><line x1="9" y1="17" x2="15" y2="17" /></svg>
                    </span>
                    <span class="nav-link-title">
                        Rapports
                    </span>
                </a>
            <?php }else{} ?>
            <div class="dropdown-menu">
                <div class="dropend">
                    <a class="dropdown-item dropdown-toggle" href="#sidebar-authentication" data-bs-toggle="dropdown" role="button" aria-expanded="false" >
                    Produits
                    </a>
                    <div class="dropdown-menu">
                    <a href="rapports.php?rapport=1&page=5" class="dropdown-item">Tous les produits</a>
                    <!--<a href="rapports.php?rapport=4&page=5" class="dropdown-item">En voie d'expiration</a>-->
                    <a href="rapports.php?rapport=5&page=5" class="dropdown-item">Produits à stock faible</a>
                    <!--<a href="rapports.php?rapport=6&page=5" class="dropdown-item">Médicaments expirés</a>-->
                    <a href="rapports.php?rapport=7&page=5" class="dropdown-item">Produits à stock fini</a>
                    </div>
                </div>
                <div class="dropend">
                    <a class="dropdown-item dropdown-toggle" href="#sidebar-authentication" data-bs-toggle="dropdown" role="button" aria-expanded="false" >
                    Ventes
                    </a>
                    <div class="dropdown-menu overflow-auto" style="max-height: 21rem">
                    <a href="rapports.php?rapport=2&page=5" class="dropdown-item">Toutes les ventes</a>
                    <!-- -->
                    <?php
                    $jour = '';

                    $req = "select distinct date(DATE_VENTE) as date_vente from VENTE order by ID_VENTE desc limit 0,30 ";
                    $rs2010 = mysqli_query($connexion,$req);
                    while($ET2010=mysqli_fetch_assoc($rs2010)){
                        if ($ET2010['date_vente'] == date('Y-m-d')) {
                            $jour = 'Ventes d\'aujourd\'hui';
                        }
                        else {
                            $jour = 'Ventes du '.date("d/m/Y", strtotime($ET2010['date_vente']));
                        }
                    ?>
                        <a href="rapports.php?rapport=3&page=5&date=<?php echo $ET2010['date_vente'] ?>&jour=<?php echo $jour ?>" class="dropdown-item"><?php echo $jour ?></a>
                    <?php } ?>
                    <!-- -->
                    </div>
                </div>
                <div class="dropend">
                    <a class="dropdown-item dropdown-toggle" href="#sidebar-authentication" data-bs-toggle="dropdown" role="button" aria-expanded="false" >
                    Ventes annulées
                    </a>
                    <div class="dropdown-menu overflow-auto" style="max-height: 21rem">
                    <a href="rapports.php?rapport=6&page=5" class="dropdown-item">Toutes les ventes annulées</a>
                    <!-- -->
                    <?php
                    $jour = '';

                    $req = "select distinct date(DATE_VENTE_ANNULEE) as date_vente from VENTE_ANNULEE order by ID_VENTE_ANNULEE desc limit 0,30 ";
                    $rs2010 = mysqli_query($connexion,$req);
                    while($ET2010=mysqli_fetch_assoc($rs2010)){
                        if ($ET2010['date_vente'] == date('Y-m-d')) {
                            $jour = 'Ventes annulées d\'aujourd\'hui';
                        }
                        else {
                            $jour = 'Ventes annulées du '.date("d/m/Y", strtotime($ET2010['date_vente']));
                        }
                    ?>
                        <a href="rapports.php?rapport=4&page=5&date=<?php echo $ET2010['date_vente'] ?>&jour=<?php echo $jour ?>" class="dropdown-item"><?php echo $jour ?></a>
                    <?php } ?>
                    <!-- -->
                    </div>
                </div>
            </div>
        </li>
        </ul>
        <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
        <form action="" method="POST">
            <div class="input-icon">
            <span class="input-icon-addon">
                <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="10" cy="10" r="7" /><line x1="21" y1="21" x2="15" y2="15" /></svg>
            </span>
            <input id="search_produit" type="text" name="fac" class="form-control" placeholder="Rechercher…" aria-label="Search in website">
            </div>
        </form>
        </div>
        <!--
        <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
            <a href="#" class="btn btn-white" onclick="rafraichir();">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Actualiser
            </a>
        </div>
        -->
    </div>
    </div>
</div>
</div>
<?php
    $id_utilisateur = $_SESSION['ID'];
    $req = "select * from UTILISATEUR where ID_UTILISATEUR = $id_utilisateur ";
    $rs1000 = mysqli_query($connexion,$req);
    $ET1000 = mysqli_fetch_assoc($rs1000);
?>

<div class="modal modal-blur fade" id="modal-team4" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form class="formulaire" action="./dist/traitement/modifierscript.php?id_utilisateur=<?php echo $ET1000['ID_UTILISATEUR']?>" method="POST">
        <div class="modal-header">
        <h5 class="modal-title">Modifier mon profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="row mb-3 align-items-end">
            <div class="col">
                <label class="form-label">Identifiant</label>
                <input type="text" name="login" value="<?php echo $ET1000['LOGIN']?>" class="form-control"/>
            </div>
            <div class="col">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="hidden" name="passe1" value="<?php echo $ET1000['PASSE']?>" class="form-control"/>
                <input type="password" name="passe" placeholder="Mot de passe" class="form-control"/>
            </div>
        </div>
        <div class="col">
            <label class="form-label">Votre nom complet</label>
            <input type="text" name="nom" value="<?php echo $ET1000['NOM']?>" class="form-control"/>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Fermer</button>
        <input type="submit" class="btn btn-primary" data-bs-dismiss="modal" value="Modifier"></button>
        </div>
        </form>
    </div>
    </div>
</div>