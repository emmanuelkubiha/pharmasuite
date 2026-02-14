<?php
/**
 * PAGE PARAMÈTRES - STORE SUITE
 * Configuration du système (Admin uniquement)
 */
require_once 'protection_pages.php';
require_admin(); // Vérifier que c'est un admin

$page_title = 'Paramètres du Système';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';
    $success = '';
    
    try {
        $nom_boutique = trim($_POST['nom_boutique'] ?? '');
        $devise = trim($_POST['devise'] ?? '');
        $couleur_primaire = trim($_POST['couleur_primaire'] ?? '');
        $couleur_secondaire = trim($_POST['couleur_secondaire'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $num_registre_commerce = trim($_POST['num_registre_commerce'] ?? '');
        $num_impot = trim($_POST['num_impot'] ?? '');
        
        if (empty($nom_boutique)) {
            throw new Exception('Le nom de la boutique est obligatoire');
        }
        
        if (empty($devise)) {
            throw new Exception('La devise est obligatoire');
        }
        
        // Traitement du logo (si présent)
        $logo = $config['logo'] ?? '';
        if (!empty($_POST['logo_cropped_data'])) {
            // Décoder l'image base64
            $logoData = $_POST['logo_cropped_data'];
            if (strpos($logoData, 'data:image') === 0) {
                $logoData = substr($logoData, strpos($logoData, ',') + 1);
                $logoData = base64_decode($logoData);
                
                // Générer un nom standardisé
                $logoFilename = 'logo_boutique.png';
                $logoPath = __DIR__ . '/uploads/logos/' . $logoFilename;
                
                // Créer le dossier si nécessaire
                if (!is_dir(__DIR__ . '/uploads/logos')) {
                    mkdir(__DIR__ . '/uploads/logos', 0755, true);
                }
                
                // Sauvegarder le fichier
                if (file_put_contents($logoPath, $logoData)) {
                    $logo = 'uploads/logos/' . $logoFilename;
                }
            }
        }
        
        // Mise à jour de la configuration
        $sql = "UPDATE configuration SET 
                nom_boutique = ?,
                devise = ?,
                couleur_primaire = ?,
                couleur_secondaire = ?,
                adresse = ?,
                telephone = ?,
                email = ?,
                num_registre_commerce = ?,
                num_impot = ?,
                logo = ?,
                date_modification = NOW()
                WHERE id_config = 1";
        
        db_execute($sql, [
            $nom_boutique,
            $devise,
            $couleur_primaire,
            $couleur_secondaire,
            $adresse,
            $telephone,
            $email,
            $num_registre_commerce,
            $num_impot,
            $logo
        ]);
        
        $success = 'Paramètres mis à jour avec succès. Actualisez la page pour voir les changements.';
        
        // Recharger la config
        $config = get_system_config();
        $nom_boutique = $config['nom_boutique'];
        $devise = $config['devise'];
        $couleur_primaire = $config['couleur_primaire'];
        $couleur_secondaire = $config['couleur_secondaire'];
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer les statistiques système
$stats_systeme = db_fetch_one("
    SELECT 
        (SELECT COUNT(*) FROM produits WHERE est_actif = 1) as nb_produits,
        (SELECT COUNT(*) FROM categories WHERE est_actif = 1) as nb_categories,
        (SELECT COUNT(*) FROM clients WHERE est_actif = 1) as nb_clients,
        (SELECT COUNT(*) FROM utilisateurs WHERE est_actif = 1) as nb_utilisateurs,
        (SELECT COUNT(*) FROM ventes WHERE statut = 'validee') as nb_ventes,
        (SELECT SUM(quantite_stock) FROM produits WHERE est_actif = 1) as stock_total
");

include 'header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css"/>

<style>
/* Styles pour les tabs */
.params-nav {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.params-nav .nav-link {
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.params-nav .nav-link:hover {
    background: <?php echo $couleur_primaire; ?>15;
    color: <?php echo $couleur_primaire; ?>;
}

.params-nav .nav-link.active {
    background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.params-nav .nav-link .icon {
    width: 18px;
    height: 18px;
}

/* Styles pour le crop d'image */
#cropModal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
}

#cropModal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.crop-container {
    max-width: 90%;
    max-height: 90vh;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.crop-container h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: <?php echo $couleur_primaire; ?>;
}

#cropImage {
    max-width: 100%;
    max-height: 60vh;
    display: block;
}

.crop-buttons {
    margin-top: 15px;
    text-align: right;
}

.logo-preview {
    max-width: 200px;
    max-height: 200px;
    margin-top: 10px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px;
}
</style>

<div class="container-xl">
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Paramètres du Système
                </h2>
                <div class="text-muted mt-1">Configuration générale de la boutique</div>
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

    <?php if (isset($error) && $error): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
            </div>
            <div><?php echo e($error); ?></div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert"></a>
    </div>
    <?php endif; ?>

    <?php if (isset($success) && $success): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>
            </div>
            <div><?php echo e($success); ?></div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert"></a>
    </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills params-nav" id="paramsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="config-tab" data-bs-toggle="pill" data-bs-target="#configTab" type="button" role="tab" aria-controls="configTab" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                Configuration
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reinit-tab" data-bs-toggle="pill" data-bs-target="#reinitTab" type="button" role="tab" aria-controls="reinitTab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                Réinitialisations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="import-tab" data-bs-toggle="pill" data-bs-target="#importTab" type="button" role="tab" aria-controls="importTab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                Import / Export
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="pill" data-bs-target="#logsTab" type="button" role="tab" aria-controls="logsTab" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><line x1="9" y1="12" x2="9.01" y2="12"/><line x1="13" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="9.01" y2="16"/><line x1="13" y1="16" x2="15" y2="16"/></svg>
                Logs d'Activités
            </button>
        </li>
    </ul>

    <!-- Statistiques en cartes modernes -->
    <div class="row mb-4">
        <div class="col-md-4 col-xl mb-3">
            <div class="card card-sm" style="border-left: 4px solid <?php echo $couleur_primaire; ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background: <?php echo $couleur_primaire; ?>20; color: <?php echo $couleur_primaire; ?>;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M12 12l0 9"/><path d="M12 12l-8 -4.5"/></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium"><?php echo number_format($stats_systeme['nb_produits']); ?></div>
                            <div class="text-muted small">Produits actifs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl mb-3">
            <div class="card card-sm" style="border-left: 4px solid <?php echo $couleur_secondaire; ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background: <?php echo $couleur_secondaire; ?>20; color: <?php echo $couleur_secondaire; ?>;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium"><?php echo number_format($stats_systeme['nb_clients']); ?></div>
                            <div class="text-muted small">Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl mb-3">
            <div class="card card-sm" style="border-left: 4px solid <?php echo $couleur_primaire; ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background: <?php echo $couleur_primaire; ?>20; color: <?php echo $couleur_primaire; ?>;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium"><?php echo number_format($stats_systeme['nb_ventes']); ?></div>
                            <div class="text-muted small">Ventes totales</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl mb-3">
            <div class="card card-sm" style="border-left: 4px solid <?php echo $couleur_secondaire; ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background: <?php echo $couleur_secondaire; ?>20; color: <?php echo $couleur_secondaire; ?>;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="4" rx="2"/><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium"><?php echo number_format($stats_systeme['stock_total']); ?></div>
                            <div class="text-muted small">Stock total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl mb-3">
            <div class="card card-sm" style="border-left: 4px solid <?php echo $couleur_primaire; ?>;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background: <?php echo $couleur_primaire; ?>20; color: <?php echo $couleur_primaire; ?>;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><line x1="19" y1="7" x2="19" y2="10"/><line x1="19" y1="14" x2="19" y2="14.01"/></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium"><?php echo number_format($stats_systeme['nb_utilisateurs']); ?></div>
                            <div class="text-muted small">Utilisateurs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="paramsTabContent">
        <!-- TAB CONFIGURATION -->
        <div class="tab-pane fade show active" id="configTab" role="tabpanel" aria-labelledby="config-tab">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configuration de la boutique</h3>
                </div>
                <div class="card-body">
                    <form method="post" id="configForm">
                        <h4 class="mb-3">Informations générales</h4>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">
                                    <span class="text-danger">*</span> Nom de la boutique
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Ce nom apparaît partout dans l'application : en-tête, factures, rapports et écran de chargement">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="text" class="form-control" name="nom_boutique" value="<?php echo e($nom_boutique); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <span class="text-danger">*</span> Devise
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Monnaie utilisée pour toutes les transactions, prix et factures">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <select class="form-select" name="devise" required>
                                    <option value="CDF" <?php echo $devise === 'CDF' ? 'selected' : ''; ?>>CDF (Franc Congolais)</option>
                                    <option value="USD" <?php echo $devise === 'USD' ? 'selected' : ''; ?>>USD (Dollar Américain)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Adresse de la boutique
                                <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Votre adresse sera visible sur les factures et reçus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                </span>
                            </label>
                            <input type="text" class="form-control" name="adresse" value="<?php echo isset($config['adresse']) ? e($config['adresse']) : ''; ?>" placeholder="Ex: 123 Avenue Lumumba, Kinshasa">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Numéro de téléphone
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Votre numéro sera visible sur toutes les factures et reçus pour que les clients puissent vous contacter">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="tel" class="form-control" name="telephone" value="<?php echo isset($config['telephone']) ? e($config['telephone']) : ''; ?>" placeholder="+243 XXX XXX XXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Adresse email
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Email de contact affiché sur les factures">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="email" class="form-control" name="email" value="<?php echo isset($config['email']) ? e($config['email']) : ''; ?>" placeholder="contact@boutique.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Numéro RCCM (Registre de Commerce)
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Numéro d'immatriculation au registre de commerce">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="text" class="form-control" name="num_registre_commerce" value="<?php echo isset($config['num_registre_commerce']) ? e($config['num_registre_commerce']) : ''; ?>" placeholder="Ex: CD/KNG/RCCM/XX-X-XXXX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Numéro d'impôt
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Numéro d'identification fiscale">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="text" class="form-control" name="num_impot" value="<?php echo isset($config['num_impot']) ? e($config['num_impot']) : ''; ?>" placeholder="Ex: A1234567X">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                            Logo du système
                            <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Logo affiché dans l'en-tête, l'écran de chargement et sur les factures imprimées">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                            </span>
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <?php 
                                $logo_actuel = isset($config['logo']) ? $config['logo'] : '';
                                $logo_existe = !empty($logo_actuel) && file_exists(__DIR__ . '/' . $logo_actuel);
                                ?>
                                <?php if ($logo_existe): ?>
                                <div class="card">
                                    <div class="card-body text-center p-4">
                                        <img src="<?php echo BASE_URL . e($logo_actuel); ?>" alt="Logo actuel" class="img-fluid mb-2" style="max-height: 100px;">
                                        <div class="text-muted small">Logo actuel</div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="card">
                                    <div class="card-body text-center p-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z"/>
                                            <path d="M9 11v-5a3 3 0 0 1 6 0v5"/>
                                        </svg>
                                        <div class="text-muted small">Aucun logo</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">
                                    Uploader un nouveau logo
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Sélectionnez une image puis recadrez-la avant d'enregistrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <input type="file" class="form-control" id="logoInput" accept="image/*" onchange="selectLogo(this)">
                                <input type="hidden" name="logo_cropped_data" id="logoCroppedData">
                                <small class="text-muted d-block">Formats acceptés : JPG, PNG, GIF (max 5MB). Vous pourrez recadrer l'image.</small>
                                <div id="logoPreviewContainer" style="display:none; margin-top:10px;">
                                    <img id="logoPreview" class="logo-preview" alt="Aperçu du logo">
                                    <button type="button" class="btn btn-sm btn-warning mt-2" onclick="reopenCrop()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="1" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
                                        Recadrer à nouveau
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z"/><path d="M3.6 9h16.8"/><path d="M3.6 15h16.8"/><circle cx="12" cy="12" r="9"/></svg>
                            Personnalisation visuelle
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Couleur primaire
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Couleur principale pour : en-tête du site, menu de navigation, boutons principaux et éléments actifs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="couleur_primaire" id="couleur1" value="<?php echo e($couleur_primaire); ?>" onchange="updateColorText('couleur1')">
                                    <input type="text" class="form-control" id="couleur1_text" value="<?php echo e($couleur_primaire); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Couleur secondaire
                                    <span class="text-muted ms-1" data-bs-toggle="tooltip" title="Couleur secondaire pour : dégradés, survol des boutons, badges et éléments d'accentuation">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="couleur_secondaire" id="couleur2" value="<?php echo e($couleur_secondaire); ?>" onchange="updateColorText('couleur2')">
                                    <input type="text" class="form-control" id="couleur2_text" value="<?php echo e($couleur_secondaire); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                            <strong>Note :</strong> Actualisez la page (F5) après la sauvegarde pour voir les changements de couleurs appliqués.
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                Enregistrer les paramètres
                            </button>
                            <span class="text-muted ms-3"><span class="text-danger">*</span> Champs obligatoires</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <!-- FIN TAB CONFIGURATION -->

        <!-- TAB RÉINITIALISATIONS -->
        <div class="tab-pane fade" id="reinitTab" role="tabpanel" aria-labelledby="reinit-tab">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-danger shadow-lg">
                    <div class="card-header border-danger bg-gradient" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                        <h3 class="card-title mb-0 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="3 5 9 5 9 11 3 11 3 5"/>
                                <polyline points="15 13 21 13 21 19 15 19 15 13"/>
                                <path d="M9 5h6 a 2 2 0 0 0 2 -2 a 2 2 0 0 0 -2 -2 h -6 a 2 2 0 0 0 -2 2 a 2 2 0 0 0 2 2"/>
                                <path d="M5 15h6 a 2 2 0 0 0 2 -2 a 2 2 0 0 0 -2 -2 h -6 a 2 2 0 0 0 -2 2 a 2 2 0 0 0 2 2"/>
                            </svg>
                            Zone de Réinitialisation du Système
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-danger border-0 rounded-lg mb-4" style="background-color: #f8d7da;">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-3 mt-1 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                    <path d="M12 8v4"/>
                                    <path d="M12 16h.01"/>
                                </svg>
                                <div>
                                    <strong>⚠️ ATTENTION !</strong> Les opérations ci-dessous sont <strong>irréversibles</strong>. Une confirmation ET votre mot de passe seront requis. <strong>Aucune sauvegarde ne sera possible</strong> une fois exécutées.
                                </div>
                            </div>
                        </div>

                        <!-- Réinitialisation Ventes -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card border-1 border-danger h-100 hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="badge badge-lg bg-danger me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <polyline points="3 6 5 4 7 6"/>
                                                    <path d="M5 4v5a8 8 0 0 0 13.95 7m1.3 -4a8 8 0 0 0 -13.95 -7v5"/>
                                                </svg>
                                            </div>
                                            <h5 class="card-title mb-0 text-danger">Supprimer les ventes</h5>
                                        </div>
                                        <p class="text-muted small mb-3">Efface <strong><?php echo e($stats_systeme['nb_ventes'] ?? 0); ?> ventes</strong> de la base de données</p>
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="reinitVentes()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            </svg>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Réinitialisation Produits -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-1 border-danger h-100 hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="badge badge-lg bg-danger me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M6 19a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2 h10a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-10z"/>
                                                    <path d="M9 3v-1"/>
                                                    <path d="M15 3v-1"/>
                                                    <line x1="6" y1="8" x2="18" y2="8"/>
                                                </svg>
                                            </div>
                                            <h5 class="card-title mb-0 text-danger">Supprimer produits</h5>
                                        </div>
                                        <p class="text-muted small mb-3">Efface <strong><?php echo e($stats_systeme['nb_produits'] ?? 0); ?> produits</strong> et le stock</p>
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="reinitProduits()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            </svg>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Réinitialisation Clients -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-1 border-danger h-100 hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="badge badge-lg bg-danger me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                    <path d="M16 11h6"/>
                                                </svg>
                                            </div>
                                            <h5 class="card-title mb-0 text-danger">Supprimer clients</h5>
                                        </div>
                                        <p class="text-muted small mb-3">Efface <strong><?php echo e($stats_systeme['nb_clients'] ?? 0); ?> clients</strong> (garde client par défaut)</p>
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="reinitClients()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            </svg>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Réinitialisation Utilisateurs -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-1 border-danger h-100 hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="badge badge-lg bg-danger me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                    <path d="M16 5a4 4 0 0 1 4 4v2"/>
                                                </svg>
                                            </div>
                                            <h5 class="card-title mb-0 text-danger">Supprimer utilisateurs</h5>
                                        </div>
                                        <p class="text-muted small mb-3">Efface <strong><?php echo e($stats_systeme['nb_utilisateurs'] ?? 0); ?> utilisateurs</strong> (vous êtes gardé)</p>
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="reinitUtilisateurs()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            </svg>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Réinitialisation Complète -->
                            <div class="col-12 mb-3">
                                <div class="card border-2 border-dark h-100" style="background: linear-gradient(135deg, rgba(0,0,0,.05) 0%, rgba(0,0,0,.02) 100%);">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="badge badge-lg bg-dark me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0 -18"/>
                                                    <path d="M12 9a3 3 0 1 0 0 6a3 3 0 0 0 0 -6"/>
                                                </svg>
                                            </div>
                                            <h5 class="card-title mb-0"> RÉINITIALISATION COMPLÈTE</h5>
                                        </div>
                                        <p class="text-muted small mb-3"><strong>DANGER ULTIME !</strong> Efface <strong>TOUT</strong> : ventes, produits, clients, utilisateurs, catégories, paramètres. Le système sera ramené à zéro (état neuf).</p>
                                        <button type="button" class="btn btn-dark w-100" onclick="reinitComplet()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 12a9 9 0 1 0 9 -9a9 9 0 0 0 -9 9"/>
                                                <path d="M3.6 9h16.8"/>
                                                <path d="M3.6 15h16.8"/>
                                            </svg>
                                            RÉINITIALISER LE SYSTÈME
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- FIN TAB RÉINITIALISATIONS -->

        <!-- TAB IMPORT/EXPORT -->
        <div class="tab-pane fade" id="importTab" role="tabpanel" aria-labelledby="import-tab">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-primary shadow-lg">
                    <div class="card-header border-primary bg-gradient" style="background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);">
                        <h3 class="card-title mb-0 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                <line x1="12" y1="11" x2="12" y2="17"/>
                                <polyline points="9 14 12 11 15 14"/>
                            </svg>
                            Import / Export de Données
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 rounded-lg mb-4">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-3 mt-1 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                    <polyline points="11 12 12 12 12 16 13 16"/>
                                </svg>
                                <div>
                                    <strong>Gestion des données facilitée</strong><br>
                                    <small>Importez vos données depuis un fichier Excel ou exportez vos données vers Excel pour analyse ou sauvegarde.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Importation -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success-lt">
                                        <h4 class="card-title mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                                <polyline points="7 9 12 4 17 9"/>
                                                <line x1="12" y1="4" x2="12" y2="16"/>
                                            </svg>
                                            Importer des données
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Importez vos produits, clients ou fournisseurs depuis un fichier Excel (.xlsx, .xls) ou CSV.</p>
                                        
                                        <form id="formImport" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label class="form-label">Type de données</label>
                                                <select class="form-select" name="type_import" id="typeImport" required>
                                                    <option value="produits">Produits / Médicaments</option>
                                                    <option value="clients">Clients</option>
                                                    <option value="fournisseurs">Fournisseurs</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Fichier Excel / CSV</label>
                                                <input type="file" class="form-control" name="fichier_excel" id="fichierImport" accept=".xlsx,.xls,.csv" required>
                                                <small class="text-muted">Formats acceptés : .xlsx, .xls, .csv (max 10 MB)</small>
                                            </div>
                                            
                                            <div class="alert alert-warning small mb-3">
                                                <strong>Format attendu :</strong>
                                                <ul class="mb-0 mt-2" id="formatAttendu">
                                                    <li><strong>Produits :</strong> nom_produit, code_barre, categorie, prix_achat, prix_vente, quantite_stock, seuil_alerte</li>
                                                </ul>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-success w-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                                    <polyline points="7 9 12 4 17 9"/>
                                                    <line x1="12" y1="4" x2="12" y2="16"/>
                                                </svg>
                                                Importer maintenant
                                            </button>
                                        </form>
                                        
                                        <div id="importResult" class="mt-3" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Exportation -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info-lt">
                                        <h4 class="card-title mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                                <polyline points="7 11 12 16 17 11"/>
                                                <line x1="12" y1="4" x2="12" y2="16"/>
                                            </svg>
                                            Exporter des données
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Exportez vos données vers Excel pour analyse, sauvegarde ou partage.</p>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Type de données à exporter</label>
                                            <select class="form-select" id="typeExport">
                                                <option value="produits">Produits / Médicaments</option>
                                                <option value="ventes">Ventes</option>
                                                <option value="categories">Catégories</option>
                                                <option value="stock">État du stock</option>
                                                <option value="mouvements_stock">Mouvements de stock</option>
                                                <option value="alertes_stock">Alertes de stock</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3" id="periodeExport">
                                            <label class="form-label">Période (pour ventes/mouvements)</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <input type="date" class="form-control" id="dateDebutExport" value="<?php echo date('Y-m-01'); ?>">
                                                    <small class="text-muted">Du</small>
                                                </div>
                                                <div class="col-6">
                                                    <input type="date" class="form-control" id="dateFinExport" value="<?php echo date('Y-m-d'); ?>">
                                                    <small class="text-muted">Au</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="btn btn-info w-100 mb-2" onclick="exporterExcel()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                <line x1="10" y1="12" x2="10" y2="16"/>
                                                <line x1="14" y1="12" x2="14" y2="16"/>
                                            </svg>
                                            Télécharger en Excel (.xls)
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-info w-100" onclick="exporterPDF()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                <line x1="10" y1="17" x2="10" y2="13"/>
                                                <line x1="14" y1="17" x2="14" y2="13"/>
                                            </svg>
                                            Télécharger en PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modèles Excel à télécharger -->
                        <div class="card border-warning mt-3">
                            <div class="card-header bg-warning-lt">
                                <h5 class="card-title mb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        <line x1="9" y1="14" x2="15" y2="14"/>
                                    </svg>
                                    Modèles Excel à télécharger
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Téléchargez un modèle Excel pré-formaté pour faciliter l'import de vos données.</p>
                                <div class="btn-list">
                                    <a href="ajax/generer_modele.php?type=produits" class="btn btn-outline-warning btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                            <polyline points="7 11 12 16 17 11"/>
                                            <line x1="12" y1="4" x2="12" y2="16"/>
                                        </svg>
                                        Modèle Produits
                                    </a>
                                    <a href="ajax/generer_modele.php?type=clients" class="btn btn-outline-warning btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                            <polyline points="7 11 12 16 17 11"/>
                                            <line x1="12" y1="4" x2="12" y2="16"/>
                                        </svg>
                                        Modèle Clients
                                    </a>
                                    <a href="ajax/generer_modele.php?type=fournisseurs" class="btn btn-outline-warning btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                            <polyline points="7 11 12 16 17 11"/>
                                            <line x1="12" y1="4" x2="12" y2="16"/>
                                        </svg>
                                        Modèle Fournisseurs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- FIN TAB IMPORT/EXPORT -->

        <!-- TAB LOGS -->
        <div class="tab-pane fade" id="logsTab" role="tabpanel" aria-labelledby="logs-tab">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-info shadow-lg">
                    <div class="card-header border-info bg-gradient" style="background: linear-gradient(135deg, #0dcaf0, #0a58ca);">
                        <h3 class="card-title mb-0 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <rect x="9" y="3" width="6" height="4" rx="2" />
                                <line x1="9" y1="12" x2="9.01" y2="12" />
                                <line x1="13" y1="12" x2="15" y2="12" />
                                <line x1="9" y1="16" x2="9.01" y2="16" />
                                <line x1="13" y1="16" x2="15" y2="16" />
                            </svg>
                            Logs d'Activités du Système
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 rounded-lg mb-4">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-3 mt-1 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                    <polyline points="11 12 12 12 12 16 13 16"/>
                                </svg>
                                <div>
                                    <strong>Historique complet des actions</strong><br>
                                    <small>Consultez toutes les activités des utilisateurs : connexions, ventes, modifications, suppressions, etc.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres -->
                        <div class="card border-light mb-4">
                            <div class="card-body bg-light">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Type d'action</label>
                                        <select class="form-select" id="typeActionFilter">
                                            <option value="">Toutes les actions</option>
                                            <option value="CONNEXION">🔑 Connexion</option>
                                            <option value="DECONNEXION">🚪 Déconnexion</option>
                                            <option value="VENTE">💰 Vente</option>
                                            <option value="VENTE_ANNULEE">❌ Vente annulée</option>
                                            <option value="VENTE_SUPPRIMEE">🗑️ Vente supprimée</option>
                                            <option value="PRODUIT_AJOUT">➕ Ajout produit</option>
                                            <option value="PRODUIT_MODIFICATION">✏️ Modification produit</option>
                                            <option value="PRODUIT_SUPPRESSION">🗑️ Suppression produit</option>
                                            <option value="CONFIGURATION_INITIALE">⚙️ Configuration initiale</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Utilisateur</label>
                                        <select class="form-select" id="utilisateurFilter">
                                            <option value="">Tous les utilisateurs</option>
                                            <?php
                                            $users = db_fetch_all("SELECT id_utilisateur, nom_complet FROM utilisateurs WHERE est_actif = 1 ORDER BY nom_complet");
                                            foreach ($users as $u) {
                                                echo "<option value=\"{$u['id_utilisateur']}\">" . e($u['nom_complet']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold">Date début</label>
                                        <input type="date" class="form-control" id="dateDebutLogs" value="<?php echo date('Y-m-01'); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold">Date fin</label>
                                        <input type="date" class="form-control" id="dateFinLogs" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" onclick="chargerLogs(1)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="10" cy="10" r="7" />
                                                <line x1="21" y1="21" x2="15" y2="15" />
                                            </svg>
                                            Filtrer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Légende explicative -->
                        <div class="card border-warning mb-4">
                            <div class="card-header bg-warning-lt">
                                <h5 class="card-title mb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="9"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    Comprendre les colonnes des logs
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <strong>Date & Heure :</strong> 
                                                <small class="text-muted">Moment exact de l'action (format : JJ/MM/AAAA HH:MM)</small>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Utilisateur :</strong> 
                                                <small class="text-muted">Nom de l'utilisateur qui a effectué l'action</small>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Type d'action :</strong> 
                                                <small class="text-muted">Catégorie de l'action (Vente, Connexion, Modification, etc.)</small>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <strong>Description :</strong> 
                                                <small class="text-muted">Détails complets de l'action effectuée</small>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Adresse IP :</strong> 
                                                <small class="text-muted">Adresse IP de l'ordinateur utilisé</small>
                                            </li>
                                            <li class="mb-2">
                                                <strong>Navigateur :</strong> 
                                                <small class="text-muted">Type de navigateur et système d'exploitation</small>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des logs -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="tableLogs">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 140px;">Date & Heure</th>
                                        <th style="width: 150px;">Utilisateur</th>
                                        <th style="width: 150px;">Type</th>
                                        <th>Description</th>
                                        <th style="width: 120px;">IP</th>
                                        <th style="width: 80px;">Navigateur</th>
                                    </tr>
                                </thead>
                                <tbody id="logsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Chargement des logs...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Navigation des logs" id="paginationLogs" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Pagination générée dynamiquement -->
                            </ul>
                        </nav>

                        <!-- Actions rapides -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <span class="badge bg-secondary" id="totalLogs">Total : 0 logs</span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="purgerVieuxLogs()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="4" y1="7" x2="20" y2="7" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                    Purger logs > 90 jours
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- FIN TAB LOGS -->

    </div>
    <!-- FIN DE TAB-CONTENT -->
    </div>
</div>

<!-- Modal de crop d'image -->
<div id="cropModal">
    <div class="crop-container">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="8" y="8" width="12" height="12" rx="1" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /></svg>
            Recadrer votre logo
        </h3>
        <p class="text-muted">Ajustez la zone de sélection pour recadrer votre logo</p>
        <div style="max-height: 60vh; overflow: hidden;">
            <img id="cropImage" alt="Image à rogner">
        </div>
        <div class="crop-buttons">
            <button type="button" class="btn btn-secondary" onclick="cancelCrop()">Annuler</button>
            <button type="button" class="btn btn-primary" onclick="applyCrop()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                Appliquer
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="assets/js/parametres.js"></script>
<script>
let cropper = null;
let currentImageFile = null;

// Fonctions de réinitialisation
let currentReinitType = null;

// Modal pour demander le mot de passe
function showPasswordModal(type, message) {
    const html = `
        <div style="text-align: center;">
            <p class="text-danger mb-3"><strong>${message}</strong></p>
            <div class="mb-3">
                <label class="form-label">Entrez votre mot de passe pour confirmer:</label>
                <input type="password" id="reinitPassword" class="form-control" placeholder="Mot de passe..." autofocus>
            </div>
        </div>
    `;
    
    currentReinitType = type;
    
    // Créer un modal custom avec Bootstrap
    const modalId = 'passwordModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmation de Réinitialisation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${html}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger" onclick="confirmerReinitAvecPassword('${type}', '${modalId}')">
                            Confirmer la réinitialisation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter le modal au DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
    
    // Permettre Entrée pour valider
    document.getElementById('reinitPassword').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmerReinitAvecPassword(type, modalId);
        }
    });
}

function confirmerReinitAvecPassword(type, modalId) {
    const password = document.getElementById('reinitPassword').value;
    
    if (!password) {
        showAlertModal({
            title: 'Erreur',
            message: 'Le mot de passe est requis',
            type: 'error'
        });
        return;
    }
    
    // Fermer le modal
    bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
    
    // Exécuter la réinitialisation
    executerReinit(type, password);
}

function reinitVentes() {
    showPasswordModal('ventes', 'Supprimer TOUTES les ventes ? Cette action est irréversible.');
}

function reinitProduits() {
    showPasswordModal('produits', 'Supprimer TOUS les produits ? Cette action est irréversible.');
}

function reinitClients() {
    showPasswordModal('clients', 'Supprimer TOUS les clients ? Cette action est irréversible.');
}

function reinitUtilisateurs() {
    showPasswordModal('utilisateurs', 'Supprimer TOUS les utilisateurs ? Cette action est irréversible.');
}

function reinitComplet() {
    showPasswordModal('complet', '⚠️ RÉINITIALISATION COMPLÈTE ! Tout sera effacé (ventes, produits, clients, paramètres). Cette action est irréversible !');
}

function executerReinit(type, password) {
    fetch('ajax/reinitialiser_donnees.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=' + encodeURIComponent(type) + '&password=' + encodeURIComponent(password)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlertModal({
                title: 'Succès ✅',
                message: data.message + ' La page va se rafraîchir...',
                type: 'success',
                onClose: function() {
                    // Rafraîchir la page après 1.5 secondes
                    setTimeout(function() {
                        window.location.href = window.location.pathname;
                    }, 1500);
                }
            });
            // Fallback : rafraîchir même si le modal ne se ferme pas
            setTimeout(function() {
                window.location.href = window.location.pathname;
            }, 2000);
        } else {
            showAlertModal({
                title: 'Erreur ❌',
                message: data.message,
                type: 'error'
            });
        }
    })
    .catch(err => {
        showAlertModal({
            title: 'Erreur',
            message: 'Erreur lors de la réinitialisation: ' + err.message,
            type: 'error'
        });
    });
}

// Sélection du logo
function selectLogo(input) {
    if (input.files && input.files[0]) {
        currentImageFile = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const cropImage = document.getElementById('cropImage');
            cropImage.src = e.target.result;
            
            // Afficher le modal
            document.getElementById('cropModal').classList.add('active');
            
            // Initialiser Cropper
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(cropImage, {
                aspectRatio: NaN, // Libre
                viewMode: 1,
                autoCropArea: 0.8,
                responsive: true,
                background: false,
                zoomable: true,
                scalable: true,
                cropBoxResizable: true,
                cropBoxMovable: true,
            });
        };
        
        reader.readAsDataURL(currentImageFile);
    }
}

// Appliquer le crop
function applyCrop() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 800,
            maxHeight: 800,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        // Convertir en base64
        const croppedDataUrl = canvas.toDataURL('image/png');
        
        // Sauvegarder dans le champ caché
        document.getElementById('logoCroppedData').value = croppedDataUrl;
        
        // Afficher l'aperçu
        const preview = document.getElementById('logoPreview');
        preview.src = croppedDataUrl;
        document.getElementById('logoPreviewContainer').style.display = 'block';
        
        // Fermer le modal
        document.getElementById('cropModal').classList.remove('active');
        
        // Détruire cropper
        cropper.destroy();
        cropper = null;
    }
}

// Annuler le crop
function cancelCrop() {
    document.getElementById('cropModal').classList.remove('active');
    document.getElementById('logoInput').value = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

// Rouvrir le crop
function reopenCrop() {
    if (currentImageFile) {
        const input = document.getElementById('logoInput');
        const dt = new DataTransfer();
        dt.items.add(currentImageFile);
        input.files = dt.files;
        selectLogo(input);
    }
}

// Mettre à jour le champ texte de couleur
function updateColorText(inputId) {
    const color = document.getElementById(inputId).value;
    document.getElementById(inputId + '_text').value = color;
}

// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Gérer le changement de type d'import pour afficher le format attendu
    document.getElementById('typeImport').addEventListener('change', function() {
        const type = this.value;
        const formatDiv = document.getElementById('formatAttendu');
        
        const formats = {
            'produits': '<li><strong>Produits :</strong> nom_produit, code_barre, categorie, prix_achat, prix_vente, quantite_stock, seuil_alerte</li>',
            'clients': '<li><strong>Clients :</strong> nom_client, telephone, email, adresse</li>',
            'fournisseurs': '<li><strong>Fournisseurs :</strong> nom_fournisseur, telephone, email, adresse</li>'
        };
        
        formatDiv.innerHTML = formats[type] || '';
    });
});

// Gérer l'import de fichier Excel
document.getElementById('formImport').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('importResult');
    
    // Désactiver le bouton
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Import en cours...';
    
    fetch('ajax/import_excel.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>Importer maintenant';
        
        if (data.success) {
            resultDiv.className = 'alert alert-success mt-3';
            resultDiv.innerHTML = `
                <strong>✅ ${data.message}</strong>
                ${data.stats ? `
                    <ul class="mb-0 mt-2">
                        <li>Ajoutés : ${data.stats.ajoutes}</li>
                        <li>Mis à jour : ${data.stats.mis_a_jour}</li>
                        <li>Erreurs : ${data.stats.erreurs}</li>
                    </ul>
                ` : ''}
            `;
            resultDiv.style.display = 'block';
            
            // Réinitialiser le formulaire
            document.getElementById('formImport').reset();
            
            // Masquer le message après 10 secondes
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 10000);
        } else {
            resultDiv.className = 'alert alert-danger mt-3';
            resultDiv.innerHTML = `<strong>❌ Erreur :</strong> ${data.message}`;
            resultDiv.style.display = 'block';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>Importer maintenant';
        
        resultDiv.className = 'alert alert-danger mt-3';
        resultDiv.innerHTML = `<strong>❌ Erreur :</strong> ${error.message}`;
        resultDiv.style.display = 'block';
    });
});

// Export Excel
function exporterExcel() {
    const type = document.getElementById('typeExport').value;
    const dateDebut = document.getElementById('dateDebutExport').value;
    const dateFin = document.getElementById('dateFinExport').value;
    
    const url = `ajax/export_excel.php?type=${type}&date_debut=${dateDebut}&date_fin=${dateFin}`;
    window.open(url, '_blank');
}

// Export PDF
function exporterPDF() {
    const type = document.getElementById('typeExport').value;
    const dateDebut = document.getElementById('dateDebutExport').value;
    const dateFin = document.getElementById('dateFinExport').value;
    
    const url = `ajax/export_pdf.php?type=${type}&date_debut=${dateDebut}&date_fin=${dateFin}`;
    window.open(url, '_blank');
}

// ============================================
// GESTION DES LOGS D'ACTIVITÉS
// ============================================
let currentLogsPage = 1;

// Charger les logs depuis le serveur
function chargerLogs(page = 1) {
    const typeAction = document.getElementById('typeActionFilter').value;
    const utilisateur = document.getElementById('utilisateurFilter').value;
    const dateDebut = document.getElementById('dateDebutLogs').value;
    const dateFin = document.getElementById('dateFinLogs').value;
    
    // Afficher un spinner de chargement
    const tableBody = document.getElementById('logsTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Récupération des logs...</p>
            </td>
        </tr>
    `;
    
    // Construire l'URL avec les paramètres
    const params = new URLSearchParams({
        page: page,
        type_action: typeAction,
        id_utilisateur: utilisateur,
        date_debut: dateDebut,
        date_fin: dateFin
    });
    
    // Faire la requête
    fetch(`ajax/get_logs.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentLogsPage = page;
                renderLogs(data.logs);
                renderPagination(data.total, data.limit, data.page, data.total_pages);
                
                // Mettre à jour le compteur total
                document.getElementById('totalLogs').textContent = `${data.total} log(s)`;
                
                // Message de succès discret
                if (data.logs.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Aucun log trouvé pour ces critères</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } else {
                showAlertModal({
                    title: 'Erreur',
                    message: data.message,
                    type: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlertModal({
                title: 'Erreur',
                message: 'Impossible de récupérer les logs',
                type: 'error'
            });
        });
}

// Afficher les logs dans le tableau
function renderLogs(logs) {
    const tableBody = document.getElementById('logsTableBody');
    
    if (logs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2">Aucun log trouvé</p>
                </td>
            </tr>
        `;
        return;
    }
    
    const html = logs.map(log => `
        <tr>
            <td class="align-middle">
                <small class="text-muted">${log.date}</small>
            </td>
            <td class="align-middle">
                <strong>${log.utilisateur}</strong>
            </td>
            <td class="align-middle">
                <span class="badge bg-${log.type_badge}">
                    ${log.type_icon} ${log.type_action}
                </span>
            </td>
            <td class="align-middle">
                <div class="text-truncate" style="max-width: 300px;" title="${escapeHtml(log.description)}">
                    ${escapeHtml(log.description)}
                </div>
            </td>
            <td class="align-middle">
                <code class="text-muted">${log.ip}</code>
            </td>
            <td class="align-middle">
                <small class="text-muted" title="${escapeHtml(log.user_agent_full)}">
                    ${log.browser}
                </small>
            </td>
        </tr>
    `).join('');
    
    tableBody.innerHTML = html;
}

// Générer la pagination
function renderPagination(total, limit, currentPage, totalPages) {
    const paginationContainer = document.getElementById('paginationLogs');
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let html = '<ul class="pagination pagination-sm mb-0">';
    
    // Bouton Précédent
    if (currentPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(${currentPage - 1}); return false;">
                    &laquo; Précédent
                </a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">&laquo; Précédent</span>
            </li>
        `;
    }
    
    // Numéros de pages
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(1); return false;">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `
                <li class="page-item active">
                    <span class="page-link">${i}</span>
                </li>
            `;
        } else {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="chargerLogs(${i}); return false;">${i}</a>
                </li>
            `;
        }
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(${totalPages}); return false;">${totalPages}</a>
            </li>
        `;
    }
    
    // Bouton Suivant
    if (currentPage < totalPages) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(${currentPage + 1}); return false;">
                    Suivant &raquo;
                </a>
            </li>
        `;
    } else {
        html += `
            <li class="page-item disabled">
                <span class="page-link">Suivant &raquo;</span>
            </li>
        `;
    }
    
    html += '</ul>';
    paginationContainer.innerHTML = html;
}

// Purger les logs de plus de 90 jours
function purgerVieuxLogs() {
    showConfirmModal({
        title: 'Purger les vieux logs?',
        message: 'Cette action va supprimer définitivement tous les logs de plus de 90 jours. Voulez-vous continuer?',
        onConfirm: () => {
            // Afficher un spinner dans le bouton
            const btn = document.querySelector('button[onclick="purgerVieuxLogs()"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Purge en cours...';
            
            fetch('ajax/purger_logs.php', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                if (data.success) {
                    showAlertModal({
                        title: 'Succès',
                        message: data.message,
                        type: 'success'
                    });
                    
                    // Recharger les logs
                    chargerLogs(1);
                } else {
                    showAlertModal({
                        title: 'Erreur',
                        message: data.message,
                        type: 'error'
                    });
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                console.error('Erreur:', error);
                showAlertModal({
                    title: 'Erreur',
                    message: 'Impossible de purger les logs',
                    type: 'error'
                });
            });
        }
    });
}

// Fonction utilitaire pour échapper le HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Charger les logs quand on clique sur le tab Logs
document.getElementById('logs-tab').addEventListener('click', function() {
    const logsTableBody = document.getElementById('logsTableBody');
    // Ne charger qu'une seule fois
    if (logsTableBody && !logsTableBody.dataset.loaded) {
        chargerLogs(1);
        logsTableBody.dataset.loaded = 'true';
    }
});
</script>

<?php include 'footer.php'; ?>
