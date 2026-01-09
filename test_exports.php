<?php
/**
 * TEST EXPORTS - STORE SUITE
 * Page de test pour vérifier que les exports fonctionnent
 */
require_once 'protection_pages.php';
$page_title = 'Test des Exports';
include 'header.php';
?>

<div class="container-xl">
    <div class="page-header mb-4">
        <h2 class="page-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="2"/>
            </svg>
            Test des Exports
        </h2>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Vérification de la configuration</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Nom boutique :</strong> <?php echo e($nom_boutique); ?>
            </div>
            <div class="mb-3">
                <strong>Devise :</strong> <?php echo e($devise); ?>
            </div>
            <div class="mb-3">
                <strong>Adresse :</strong> <?php echo e($config['adresse_boutique'] ?? 'Non définie'); ?>
            </div>
            <div class="mb-3">
                <strong>Téléphone :</strong> <?php echo e($config['telephone_boutique'] ?? 'Non défini'); ?>
            </div>
            <div class="mb-3">
                <strong>Email :</strong> <?php echo e($config['email_boutique'] ?? 'Non défini'); ?>
            </div>
            <div class="mb-3">
                <strong>Logo :</strong> 
                <?php if (!empty($config['logo_boutique']) && file_exists(__DIR__ . '/uploads/logos/' . $config['logo_boutique'])): ?>
                    <img src="<?php echo BASE_URL . 'uploads/logos/' . e($config['logo_boutique']); ?>" alt="Logo" style="max-height: 50px;">
                <?php else: ?>
                    <span class="text-muted">Aucun logo</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Test des liens d'export</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Liste des produits</h4>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="ajax/export_excel.php?type=produits" class="btn btn-success" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/></svg>
                                    Excel
                                </a>
                                <a href="ajax/export_pdf.php?type=produits" class="btn btn-danger" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"/></svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Rapport des ventes</h4>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="ajax/export_excel.php?type=ventes&date_debut=<?php echo date('Y-m-01'); ?>&date_fin=<?php echo date('Y-m-d'); ?>" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=ventes&date_debut=<?php echo date('Y-m-01'); ?>&date_fin=<?php echo date('Y-m-d'); ?>" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Rapport bénéfices</h4>
                            <div class="btn-group btn-group-sm mt-2">
                                <a href="ajax/export_excel.php?type=benefices&date_debut=<?php echo date('Y-m-01'); ?>&date_fin=<?php echo date('Y-m-d'); ?>" class="btn btn-success" target="_blank">Excel</a>
                                <a href="ajax/export_pdf.php?type=benefices&date_debut=<?php echo date('Y-m-01'); ?>&date_fin=<?php echo date('Y-m-d'); ?>" class="btn btn-danger" target="_blank">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                <strong>Instructions :</strong>
                <ul class="mb-0">
                    <li>Cliquez sur les boutons pour tester les exports</li>
                    <li>Si vous voyez une erreur, notez le message exact</li>
                    <li>Les exports Excel téléchargent un fichier .xls</li>
                    <li>Les exports PDF s'ouvrent dans un nouvel onglet (HTML pour l'instant, installer mPDF pour vrais PDFs)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
