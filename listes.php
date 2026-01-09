<?php
require_once('protection_pages.php');
$page_title = 'Listes';
$page = isset($_GET['page']) ? $_GET['page'] : 'produits';
$available_pages = ['produits', 'clients', 'categories', 'mouvements', 'ventes'];
if (!in_array($page, $available_pages)) $page = 'produits';

switch($page) {
    case 'produits': $page_title = 'Gestion des Produits'; break;
    case 'clients': $page_title = 'Gestion des Clients'; break;
    case 'categories': $page_title = 'Gestion des Catégories'; break;
    case 'mouvements': $page_title = 'Mouvements de Stock'; break;
    case 'ventes': $page_title = 'Historique des Ventes'; break;
}

$is_vendeur = ($user_niveau == NIVEAU_VENDEUR);
$page_hints = [
    'produits' => 'Astuce : classez vos produits par catégorie et surveillez les seuils pour éviter les ruptures.',
    'clients' => 'Astuce : complétez téléphone et email pour mieux relancer vos clients.',
    'categories' => 'Astuce : des catégories claires accélèrent la recherche en caisse.',
    'mouvements' => 'Astuce : notez un motif précis à chaque ajustement de stock.',
    'ventes' => 'Astuce : contrôlez montants et clients avant d\'imprimer les factures.'
];
$page_hint = $page_hints[$page];

require_once('header.php');
?>

<style>
.list-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: none;
}
.list-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
.btn-action {
    transition: all 0.2s ease;
}
.btn-action:hover {
    transform: scale(1.05);
}
.table-hover tbody tr {
    transition: all 0.2s ease;
}
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateX(4px);
}
.table.card-table th, .table.card-table td { color: #2f2f2f; vertical-align: middle; }
.badge-category { background: #e7f1ff; color: #0b57d0; font-weight: 600; }
.page-header-title {
    font-weight: 700;
    background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.nav-tabs-modern {
    border-bottom: 2px solid #e9ecef;
    gap: 0.5rem;
}
.nav-tabs-modern .nav-link {
    border: none;
    border-radius: 8px 8px 0 0;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    position: relative;
}
.nav-tabs-modern .nav-link:hover {
    color: <?php echo $couleur_primaire; ?>;
    background: #f8f9fa;
}
.nav-tabs-modern .nav-link.active {
    color: white;
    background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.nav-tabs-modern .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: <?php echo $couleur_primaire; ?>;
}
.badge-stock {
    font-size: 0.875rem;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
}
.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}
</style>

<div class="container-xl">
    <!-- En-tête de page amélioré -->
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h2 class="page-title page-header-title mb-2"><?php echo $page_title; ?></h2>
                <p class="text-muted mb-0"><?php echo $page_hint; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="accueil.php" class="btn btn-outline-secondary btn-action">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
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

    <!-- Navigation moderne avec onglets -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs-modern" role="tablist">
                <li class="nav-item"><a href="?page=produits" class="nav-link <?php echo $page === 'produits' ? 'active' : ''; ?>">Produits</a></li>
                <li class="nav-item"><a href="?page=clients" class="nav-link <?php echo $page === 'clients' ? 'active' : ''; ?>">Clients</a></li>
                <li class="nav-item"><a href="?page=categories" class="nav-link <?php echo $page === 'categories' ? 'active' : ''; ?>">Catégories</a></li>
                <li class="nav-item"><a href="?page=mouvements" class="nav-link <?php echo $page === 'mouvements' ? 'active' : ''; ?>">Mouvements</a></li>
                <li class="nav-item"><a href="?page=ventes" class="nav-link <?php echo $page === 'ventes' ? 'active' : ''; ?>">Ventes</a></li>
            </ul>
        </div>
    </div>

    <?php
    switch ($page) {
        case 'produits':
            $produits = db_fetch_all("
                SELECT p.*, c.nom_categorie
                FROM produits p
                LEFT JOIN categories c ON p.id_categorie = c.id_categorie
                WHERE p.est_actif = 1
                ORDER BY p.nom_produit ASC
            ");
            $categories = db_fetch_all("SELECT * FROM categories WHERE est_actif = 1 ORDER BY nom_categorie ASC");
            
            // Calculer statistiques
            $total_produits = count($produits);
            $stock_faible = 0;
            $valeur_stock = 0;
            foreach($produits as $p) {
                if($p['quantite_stock'] <= $p['stock_minimum']) $stock_faible++;
                $valeur_stock += $p['quantite_stock'] * $p['prix_achat'];
            }
            ?>
            
            <!-- Cartes statistiques -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1" data-bs-toggle="tooltip" title="Nombre total de produits actifs dans le catalogue">Total Produits</h6>
                                <h3 class="mb-0"><?php echo $total_produits; ?></h3>
                            </div>
                            <div class="text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                    <path d="M12 12l8 -4.5" />
                                    <path d="M12 12l0 9" />
                                    <path d="M12 12l-8 -4.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1" data-bs-toggle="tooltip" title="Valeur totale du stock : somme (quantité × prix d'achat) de tous les produits">Valeur Stock</h6>
                                <h3 class="mb-0"><?php echo format_montant($valeur_stock, $devise); ?></h3>
                            </div>
                            <div class="text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 3a9 9 0 1 0 9 9" />
                                    <path d="M12 7v10" />
                                    <path d="M9 10c0 -1.657 1.343 -3 3 -3h2" />
                                    <path d="M15 14c0 1.657 -1.343 3 -3 3h-2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1" data-bs-toggle="tooltip" title="Produits dont le stock est inférieur ou égal au seuil minimum défini">Stock Faible</h6>
                                <h3 class="mb-0 <?php echo $stock_faible > 0 ? 'text-danger' : 'text-success'; ?>"><?php echo $stock_faible; ?></h3>
                            </div>
                            <div class="<?php echo $stock_faible > 0 ? 'text-danger' : 'text-success'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 9v4" />
                                    <path d="M12 17v.01" />
                                    <path d="M5.07 19h13.86a1 1 0 0 0 .87 -1.5l-6.93 -12a1 1 0 0 0 -1.74 0l-6.93 12a1 1 0 0 0 .87 1.5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card list-card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                        </svg>
                        Liste des produits
                        <span class="badge bg-primary ms-2"><?php echo $total_produits; ?></span>
                    </h3>
                    <div>
                        <?php if (!$is_vendeur): ?>
                        <button type="button" id="btnAddProduct" class="btn btn-primary btn-action" data-bs-toggle="tooltip" title="Créer un nouveau produit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>
                            Nouveau produit
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter table-hover">
                        <thead>
                            <tr>
                                <th data-bs-toggle="tooltip" title="Image ou initiales du produit">Image</th>
                                <th data-bs-toggle="tooltip" title="Nom complet du produit">Nom</th>
                                <th data-bs-toggle="tooltip" title="Catégorie d'appartenance">Catégorie</th>
                                <?php if (!$is_vendeur): ?><th class="text-end" data-bs-toggle="tooltip" title="Coût d'achat (réservé gestionnaire)">Prix Achat</th><?php endif; ?>
                                <th class="text-end" data-bs-toggle="tooltip" title="Tarif de vente conseillé">Prix Vente</th>
                                <th class="text-center" data-bs-toggle="tooltip" title="Quantité actuelle en stock">Stock</th>
                                <th class="text-center" data-bs-toggle="tooltip" title="Actions disponibles">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($produits)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <div style="font-size: 3rem;">📦</div>
                                        <p class="mb-0">Aucun produit trouvé</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($produits as $p): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($p['image_produit'])): ?>
                                            <img src="uploads/produits/<?php echo e($p['image_produit']); ?>" alt="<?php echo e($p['nom_produit']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                <?php echo strtoupper(substr($p['nom_produit'], 0, 2)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo e($p['nom_produit']); ?></div>
                                        <?php if (!empty($p['code_barre'])): ?>
                                            <small class="text-muted"><?php echo e($p['code_barre']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['nom_categorie'])): ?>
                                            <span class="badge badge-category" data-bs-toggle="tooltip" title="Catégorie du produit"><?php echo e($p['nom_categorie']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted" data-bs-toggle="tooltip" title="Aucune catégorie assignée">Sans catégorie</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (!$is_vendeur): ?><td class="text-end" data-bs-toggle="tooltip" title="Coût d'achat pour le stock"><?php echo format_montant($p['prix_achat'], $devise); ?></td><?php endif; ?>
                                    <td class="text-end fw-bold" data-bs-toggle="tooltip" title="Tarif de vente conseillé"><?php echo format_montant($p['prix_vente'], $devise); ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $stock_class = 'bg-success';
                                        if ($p['quantite_stock'] == 0) $stock_class = 'bg-danger';
                                        elseif ($p['quantite_stock'] <= $p['stock_minimum']) $stock_class = 'bg-warning';
                                        ?>
                                        <span class="badge badge-stock <?php echo $stock_class; ?>" data-bs-toggle="tooltip" title="Quantité actuelle">
                                            <?php echo $p['quantite_stock']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($is_vendeur): ?>
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-action btn-adjust-stock" data-id="<?php echo $p['id_produit']; ?>" data-name="<?php echo e($p['nom_produit']); ?>" title="Ajuster le stock">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 5v14" />
                                                    <path d="M5 12h14" />
                                                    <path d="M9 7h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2z" />
                                                </svg>
                                            </button>
                                        <?php else: ?>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-action btn-edit-product" data-product='<?php echo json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>' title="Modifier">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-action btn-adjust-stock" data-id="<?php echo $p['id_produit']; ?>" data-name="<?php echo e($p['nom_produit']); ?>" title="Ajuster le stock">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M12 5v14" />
                                                        <path d="M5 12h14" />
                                                        <path d="M9 7h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2z" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-action btn-delete-product" data-id="<?php echo $p['id_produit']; ?>" data-name="<?php echo e($p['nom_produit']); ?>" title="Supprimer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <line x1="4" y1="7" x2="20" y2="7" />
                                                        <line x1="10" y1="11" x2="10" y2="17" />
                                                        <line x1="14" y1="11" x2="14" y2="17" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Produit -->
            <div class="modal fade" id="modalProduit" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalProduitTitle">Nouveau produit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="productForm">
                            <div class="modal-body">
                                <input type="hidden" id="product_id" name="product_id">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Nom complet du produit">Nom du produit *</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Code-barres pour scanner rapide en caisse">Code-barres</label>
                                        <input type="text" class="form-control" id="product_barcode" name="product_barcode" placeholder="Ex: 3760123456789">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Classement du produit pour faciliter la recherche">Catégorie *</label>
                                        <select class="form-select" id="product_category" name="product_category" required>
                                            <option value="">Sélectionner...</option>
                                            <?php foreach($categories as $cat): ?>
                                                <option value="<?php echo $cat['id_categorie']; ?>"><?php echo e($cat['nom_categorie']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Unité de mesure (ex: pièce, kg, litre, boîte, carton)">Unité *</label>
                                        <input type="text" class="form-control" id="product_unit" name="product_unit" value="pièce" required placeholder="Ex: pièce, kg, litre...">
                                    </div>
                                    <?php if ($is_vendeur): ?>
                                        <input type="hidden" id="product_purchase_price" name="product_purchase_price" value="0">
                                    <?php else: ?>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Coût d'achat (réservé gestionnaire)">Prix d'achat (<?php echo $devise; ?>) *</label>
                                        <input type="number" class="form-control" id="product_purchase_price" name="product_purchase_price" step="0.01" required placeholder="0.00">
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-<?php echo $is_vendeur ? '6' : '4'; ?> mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Tarif de vente conseillé">Prix de vente (<?php echo $devise; ?>) *</label>
                                        <input type="number" class="form-control" id="product_sale_price" name="product_sale_price" step="0.01" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Quantité au moment de la création">Stock initial *</label>
                                        <input type="number" class="form-control" id="product_stock" name="product_stock" value="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Seuil d'alerte avant rupture">Stock minimum *</label>
                                        <input type="number" class="form-control" id="product_min_stock" name="product_min_stock" value="5" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Photo du produit (sera redimensionnée automatiquement)">Image</label>
                                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" onchange="previewProductImage(this)">
                                        <small class="text-muted">Format recommandé : carré, max 2MB</small>
                                        <div id="product_image_preview" class="mt-2" style="display:none;">
                                            <img id="product_image_preview_img" src="" alt="Aperçu" style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid #e9ecef;">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Détails utiles pour la vente (format, couleur, etc.)">Description</label>
                                        <textarea class="form-control" id="product_description" name="product_description" rows="3" placeholder="Ex: Savon parfumé 250g, format familial, couleur blanche avec emballage recyclable"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Ajustement Stock -->
            <div class="modal fade" id="modalAjustStock" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajuster le stock</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="stockForm">
                            <div class="modal-body">
                                <input type="hidden" id="stock_product_id" name="stock_product_id">
                                <p class="mb-3">Produit: <strong id="stock_product_name"></strong></p>
                                <div class="mb-3">
                                    <label class="form-label">Type de mouvement *</label>
                                    <select class="form-select" id="stock_type" name="stock_type" required>
                                        <option value="entree">Entrée (ajout au stock)</option>
                                        <option value="sortie">Sortie (retrait du stock)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quantité *</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Motif</label>
                                    <textarea class="form-control" id="stock_reason" name="stock_reason" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Valider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            break;

        case 'clients':
            $clients = db_fetch_all("
                SELECT c.*, 
                       COUNT(DISTINCT v.id_vente) as nb_ventes,
                       COALESCE(SUM(v.montant_total), 0) as total_achats
                FROM clients c
                LEFT JOIN ventes v ON c.id_client = v.id_client
                WHERE c.est_actif = 1
                GROUP BY c.id_client
                ORDER BY c.nom_client ASC
            ");
            
            $total_clients = count($clients);
            $clients_actifs = 0;
            $ca_total = 0;
            foreach($clients as $cli) {
                if($cli['nb_ventes'] > 0) $clients_actifs++;
                $ca_total += $cli['total_achats'];
            }
            ?>
            
            <!-- Statistiques Clients -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre total de clients enregistrés dans le système">Total Clients</div>
                                <h3 class="mb-0"><?php echo $total_clients; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Clients ayant effectué au moins une vente">Clients Actifs</div>
                                <h3 class="mb-0"><?php echo $clients_actifs; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Chiffre d'Affaires : somme totale des ventes de tous les clients">CA Total</div>
                                <h3 class="mb-0"><?php echo format_montant($ca_total, $devise); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions et tableau -->
            <div class="card list-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des Clients</h5>
                    <button type="button" id="btnAddClient" class="btn btn-primary btn-action">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouveau client
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover card-table">
                            <thead>
                                <tr>
                                    <th data-bs-toggle="tooltip" title="Nom complet du client">Nom</th>
                                    <th data-bs-toggle="tooltip" title="Numéro de téléphone">Téléphone</th>
                                    <th data-bs-toggle="tooltip" title="Adresse email">Email</th>
                                    <th data-bs-toggle="tooltip" title="Adresse physique">Adresse</th>
                                    <th data-bs-toggle="tooltip" title="Nombre de ventes effectuées">Ventes</th>
                                    <th data-bs-toggle="tooltip" title="Chiffre d'affaires généré">CA Total</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($clients)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun client enregistré</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($clients as $client): ?>
                                <tr>
                                    <td><strong><?php echo e($client['nom_client']); ?></strong></td>
                                    <td><?php echo e($client['telephone'] ?: '—'); ?></td>
                                    <td><?php echo e($client['email'] ?: '—'); ?></td>
                                    <td><?php echo e($client['adresse'] ?: '—'); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $client['nb_ventes']; ?></span>
                                    </td>
                                    <td><?php echo format_montant($client['total_achats'], $devise); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-client" 
                                                    data-client='<?php echo json_encode($client); ?>'
                                                    data-bs-toggle="tooltip" title="Modifier">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-delete-client" 
                                                    data-id="<?php echo $client['id_client']; ?>"
                                                    data-name="<?php echo e($client['nom_client']); ?>"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Client -->
            <div class="modal fade" id="modalClient" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalClientTitle">Nouveau client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="clientForm">
                            <div class="modal-body">
                                <input type="hidden" id="client_id" name="client_id">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Nom complet du client">Nom du client *</label>
                                        <input type="text" class="form-control" id="client_name" name="client_name" required placeholder="Ex: Jean Dupont">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Téléphone pour contact et rappels">Téléphone</label>
                                        <input type="tel" class="form-control" id="client_telephone" name="client_telephone" placeholder="Ex: +243 900 000 000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Email pour factures et communications">Email</label>
                                        <input type="email" class="form-control" id="client_email" name="client_email" placeholder="Ex: client@exemple.com">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Adresse physique ou quartier">Adresse</label>
                                        <input type="text" class="form-control" id="client_adresse" name="client_adresse" placeholder="Ex: Avenue Lumumba, Q. Lubumbashi">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php
            break;
        case 'categories':
            $categories = db_fetch_all("
                SELECT c.*, 
                       COUNT(p.id_produit) as nb_produits
                FROM categories c
                LEFT JOIN produits p ON c.id_categorie = p.id_categorie AND p.est_actif = 1
                WHERE c.est_actif = 1
                GROUP BY c.id_categorie
                ORDER BY c.nom_categorie ASC
            ");
            
            $total_categories = count($categories);
            $categories_utilisees = 0;
            $total_produits = 0;
            foreach($categories as $cat) {
                if($cat['nb_produits'] > 0) $categories_utilisees++;
                $total_produits += $cat['nb_produits'];
            }
            ?>
            
            <!-- Statistiques Catégories -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre total de catégories créées">Total Catégories</div>
                                <h3 class="mb-0"><?php echo $total_categories; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Catégories contenant au moins un produit">Catégories Utilisées</div>
                                <h3 class="mb-0"><?php echo $categories_utilisees; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#17a2b8" stroke-width="2">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre total de produits classés dans toutes les catégories">Total Produits</div>
                                <h3 class="mb-0"><?php echo $total_produits; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions et tableau -->
            <div class="card list-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des Catégories</h5>
                    <button type="button" id="btnAddCategory" class="btn btn-primary btn-action">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouvelle catégorie
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover card-table">
                            <thead>
                                <tr>
                                    <th data-bs-toggle="tooltip" title="Nom de la catégorie">Catégorie</th>
                                    <th data-bs-toggle="tooltip" title="Description ou notes">Description</th>
                                    <th data-bs-toggle="tooltip" title="Nombre de produits dans cette catégorie">Produits</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucune catégorie créée</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($categories as $category): ?>
                                <tr>
                                    <td>
                                        <span class="badge-category"><?php echo e($category['nom_categorie']); ?></span>
                                    </td>
                                    <td><?php echo e($category['description'] ?: '—'); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $category['nb_produits']; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-category" 
                                                    data-category='<?php echo json_encode($category); ?>'
                                                    data-bs-toggle="tooltip" title="Modifier">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-delete-category" 
                                                    data-id="<?php echo $category['id_categorie']; ?>"
                                                    data-name="<?php echo e($category['nom_categorie']); ?>"
                                                    data-products="<?php echo $category['nb_produits']; ?>"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Catégorie -->
            <div class="modal fade" id="modalCategory" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCategoryTitle">Nouvelle catégorie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="categoryForm">
                            <div class="modal-body">
                                <input type="hidden" id="category_id" name="category_id">
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="Nom court et descriptif">Nom de la catégorie *</label>
                                    <input type="text" class="form-control" id="category_name" name="category_name" required placeholder="Ex: Boissons, Épicerie, Hygiène">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="Description ou notes pour usage interne">Description</label>
                                    <textarea class="form-control" id="category_description" name="category_description" rows="3" placeholder="Ex: Produits d'épicerie sèche et conserves"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php
            break;
        case 'mouvements':
            $mouvements = db_fetch_all("
                SELECT m.*, p.nom_produit, p.unite, u.nom_complet
                FROM mouvements_stock m
                LEFT JOIN produits p ON m.id_produit = p.id_produit
                LEFT JOIN utilisateurs u ON m.id_utilisateur = u.id_utilisateur
                ORDER BY m.date_mouvement DESC
                LIMIT 200
            ");
            
            $entrees = 0;
            $sorties = 0;
            $ajustements = 0;
            foreach($mouvements as $mv) {
                if($mv['type_mouvement'] == 'entree') $entrees++;
                elseif($mv['type_mouvement'] == 'sortie') $sorties++;
                elseif($mv['type_mouvement'] == 'ajustement') $ajustements++;
            }
            ?>
            
            <!-- Statistiques Mouvements -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <polyline points="5 12 12 19 19 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre de mouvements d'entrée en stock (achats, réceptions)">Entrées</div>
                                <h3 class="mb-0 text-success"><?php echo $entrees; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                                    <line x1="12" y1="19" x2="12" y2="5"></line>
                                    <polyline points="5 12 12 5 19 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre de mouvements de sortie de stock (ventes, pertes)">Sorties</div>
                                <h3 class="mb-0 text-danger"><?php echo $sorties; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="text-muted" data-bs-toggle="tooltip" title="Nombre de corrections manuelles de stock (inventaire, erreurs)">Ajustements</div>
                                <h3 class="mb-0 text-warning"><?php echo $ajustements; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des mouvements -->
            <div class="card list-card">
                <div class="card-header">
                    <h5 class="mb-0">Historique des Mouvements (200 derniers)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover card-table">
                            <thead>
                                <tr>
                                    <th data-bs-toggle="tooltip" title="Date et heure du mouvement">Date</th>
                                    <th data-bs-toggle="tooltip" title="Produit concerné">Produit</th>
                                    <th data-bs-toggle="tooltip" title="Type d'opération">Type</th>
                                    <th data-bs-toggle="tooltip" title="Quantité modifiée">Quantité</th>
                                    <th data-bs-toggle="tooltip" title="Stock après l'opération">Nouveau Stock</th>
                                    <th data-bs-toggle="tooltip" title="Raison du mouvement">Motif</th>
                                    <th data-bs-toggle="tooltip" title="Utilisateur ayant effectué l'opération">Utilisateur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mouvements)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun mouvement enregistré</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($mouvements as $mv): ?>
                                <tr>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i', strtotime($mv['date_mouvement'])); ?></small>
                                    </td>
                                    <td><strong><?php echo e($mv['nom_produit']); ?></strong></td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        $type_label = $mv['type_mouvement'];
                                        if($mv['type_mouvement'] == 'entree') {
                                            $badge_class = 'success';
                                            $type_label = 'Entrée';
                                        } elseif($mv['type_mouvement'] == 'sortie') {
                                            $badge_class = 'danger';
                                            $type_label = 'Sortie';
                                        } elseif($mv['type_mouvement'] == 'ajustement') {
                                            $badge_class = 'warning';
                                            $type_label = 'Ajustement';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>"><?php echo $type_label; ?></span>
                                    </td>
                                    <td>
                                        <strong class="<?php echo $mv['quantite'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo ($mv['quantite'] > 0 ? '+' : '') . $mv['quantite']; ?> <?php echo e($mv['unite']); ?>
                                        </strong>
                                    </td>
                                    <td><?php echo $mv['stock_apres']; ?> <?php echo e($mv['unite']); ?></td>
                                    <td><?php echo e($mv['motif'] ?: '—'); ?></td>
                                    <td><small><?php echo e($mv['nom_complet']); ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php
            break;
        case 'ventes':
            echo '<div class="alert alert-info">Section Ventes - À implémenter</div>';
            break;
    }
    ?>
</div>

<script>
// ===== DEBUG =====
console.log('=== Script chargé ===');
console.log('jQuery disponible:', typeof $ !== 'undefined');
console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
console.log('showConfirmModal disponible:', typeof showConfirmModal !== 'undefined');
console.log('showAlertModal disponible:', typeof showAlertModal !== 'undefined');
const IS_VENDEUR = <?php echo $is_vendeur ? 'true' : 'false'; ?>;

function refuseIfVendeur() {
    if (!IS_VENDEUR) return false;
    if (typeof showAlertModal === 'function') {
        showAlertModal({
            title: 'Action non autorisée',
            message: 'Réservé aux gestionnaires.',
            type: 'warning'
        });
    } else {
        alert('Action réservée aux gestionnaires.');
    }
    return true;
}

// ===== GESTION PRODUITS =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation des événements...');
    
    // Bouton Nouveau produit
    const btnAddProduct = document.getElementById('btnAddProduct');
    if (btnAddProduct) {
        console.log('Bouton Nouveau produit trouvé');
        btnAddProduct.addEventListener('click', function() {
            if (refuseIfVendeur()) return;
            console.log('Click sur Nouveau produit');
            openProductModal();
        });
    }
    
    // Boutons Modifier
    document.querySelectorAll('.btn-edit-product').forEach(btn => {
        btn.addEventListener('click', function() {
            if (refuseIfVendeur()) return;
            console.log('Click sur Modifier');
            const productData = JSON.parse(this.getAttribute('data-product'));
            editProduct(productData);
        });
    });
    
    // Boutons Ajuster stock
    document.querySelectorAll('.btn-adjust-stock').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Click sur Ajuster stock');
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            adjustStock(id, name);
        });
    });
    
    // Boutons Supprimer
    document.querySelectorAll('.btn-delete-product').forEach(btn => {
        btn.addEventListener('click', function() {
            if (refuseIfVendeur()) return;
            console.log('Click sur Supprimer');
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            deleteProduct(id, name);
        });
    });
    
    // Formulaire produit
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            if (refuseIfVendeur()) { e.preventDefault(); return; }
            handleProductSubmit.call(this, e);
        });
    }
    
    // Formulaire stock
    const stockForm = document.getElementById('stockForm');
    if (stockForm) {
        stockForm.addEventListener('submit', handleStockSubmit);
    }
    
    // Initialiser tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    console.log('Initialisation terminée');
});

// Ouvrir modal nouveau produit
function openProductModal() {
    if (refuseIfVendeur()) return;
    console.log('openProductModal() appelé');
    document.getElementById('modalProduitTitle').textContent = 'Nouveau produit';
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalProduit'));
    modal.show();
    console.log('Modal affiché');
}

// Éditer un produit
function editProduct(product) {
    if (refuseIfVendeur()) return;
    console.log('editProduct() appelé', product);
    document.getElementById('modalProduitTitle').textContent = 'Modifier le produit';
    document.getElementById('product_id').value = product.id_produit;
    document.getElementById('product_name').value = product.nom_produit;
    document.getElementById('product_barcode').value = product.code_barre || '';
    document.getElementById('product_category').value = product.id_categorie;
    document.getElementById('product_unit').value = product.unite;
    document.getElementById('product_purchase_price').value = product.prix_achat;
    document.getElementById('product_sale_price').value = product.prix_vente;
    document.getElementById('product_stock').value = product.quantite_stock;
    document.getElementById('product_min_stock').value = product.stock_minimum;
    document.getElementById('product_description').value = product.description || '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalProduit'));
    modal.show();
}

// Ajuster stock
function adjustStock(id, name) {
    console.log('adjustStock() appelé', id, name);
    document.getElementById('stock_product_id').value = id;
    document.getElementById('stock_product_name').textContent = name;
    document.getElementById('stockForm').reset();
    document.getElementById('stock_product_id').value = id;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAjustStock'));
    modal.show();
}

// Supprimer produit
function deleteProduct(id, name) {
    if (refuseIfVendeur()) return;
    console.log('deleteProduct() appelé', id, name);
    
    if (typeof showConfirmModal === 'function') {
        showConfirmModal({
            title: 'Confirmer la suppression',
            message: `Êtes-vous sûr de vouloir supprimer "${name}" ?`,
            onConfirm: () => {
                fetch('ajax/produits.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'delete',
                        id: id
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showAlertModal === 'function') {
                            showAlertModal({
                                title: 'Succès',
                                message: data.message,
                                type: 'success',
                                onClose: () => location.reload()
                            });
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    } else {
                        if (typeof showAlertModal === 'function') {
                            showAlertModal({
                                title: 'Erreur',
                                message: data.message,
                                type: 'error'
                            });
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erreur réseau');
                });
            }
        });
    } else {
        if (confirm(`Êtes-vous sûr de vouloir supprimer "${name}" ?`)) {
            // Même code fetch...
        }
    }
}

// Soumettre formulaire produit
function handleProductSubmit(e) {
    e.preventDefault();
    console.log('Soumission formulaire produit');
    
    const formData = new FormData(this);
    const productId = document.getElementById('product_id').value;
    formData.append('action', productId ? 'update' : 'create');
    
    fetch('ajax/produits.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalProduit')).hide();
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: 'Succès',
                    message: data.message,
                    type: 'success',
                    onClose: () => location.reload()
                });
            } else {
                alert(data.message);
                location.reload();
            }
        } else {
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: 'Erreur',
                    message: data.message,
                    type: 'error'
                });
            } else {
                alert(data.message);
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erreur réseau');
    });
}

// Aperçu image produit
function previewProductImage(input) {
    const preview = document.getElementById('product_image_preview');
    const previewImg = document.getElementById('product_image_preview_img');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Vérifier taille (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: 'Image trop volumineuse',
                    message: 'La taille maximale est de 2MB. Compressez l\'image avant de la télécharger.',
                    type: 'warning'
                });
            } else {
                alert('Image trop volumineuse (max 2MB)');
            }
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Soumettre formulaire stock
function handleStockSubmit(e) {
    e.preventDefault();
    console.log('Soumission formulaire stock');
    
    const formData = new FormData(this);
    formData.append('action', 'adjust_stock');
    
    fetch('ajax/produits.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAjustStock')).hide();
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: 'Succès',
                    message: data.message,
                    type: 'success',
                    onClose: () => location.reload()
                });
            } else {
                alert(data.message);
                location.reload();
            }
        } else {
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: 'Erreur',
                    message: data.message,
                    type: 'error'
                });
            } else {
                alert(data.message);
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erreur réseau');
    });
}

// ===== GESTION CLIENTS =====
const btnAddClient = document.getElementById('btnAddClient');
const modalClient = document.getElementById('modalClient');
const clientForm = document.getElementById('clientForm');

if (btnAddClient) {
    btnAddClient.addEventListener('click', () => openClientModal());
}
if (clientForm) {
    clientForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveClient();
    });
}
document.querySelectorAll('.btn-edit-client').forEach(btn => {
    btn.addEventListener('click', function() {
        editClient(JSON.parse(this.getAttribute('data-client')));
    });
});
document.querySelectorAll('.btn-delete-client').forEach(btn => {
    btn.addEventListener('click', function() {
        deleteClient(this.getAttribute('data-id'), this.getAttribute('data-name'));
    });
});

function openClientModal(client = null) {
    if (client) {
        document.getElementById('modalClientTitle').textContent = 'Modifier le client';
        document.getElementById('client_id').value = client.id_client;
        document.getElementById('client_name').value = client.nom_client;
        document.getElementById('client_telephone').value = client.telephone || '';
        document.getElementById('client_email').value = client.email || '';
        document.getElementById('client_adresse').value = client.adresse || '';
    } else {
        document.getElementById('modalClientTitle').textContent = 'Nouveau client';
        clientForm.reset();
        document.getElementById('client_id').value = '';
    }
    new bootstrap.Modal(modalClient).show();
}

function editClient(client) {
    openClientModal(client);
}

function deleteClient(id, name) {
    const doDelete = () => {
        fetch('ajax/clients.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'delete_client', id_client: id })
        })
        .then(r => r.json())
        .then(data => {
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'error',
                    onClose: () => { if (data.success) location.reload(); }
                });
            } else {
                alert(data.message);
                if (data.success) location.reload();
            }
        });
    };
    
    if (typeof showConfirmModal === 'function') {
        showConfirmModal({
            title: 'Confirmer la suppression',
            message: `Supprimer le client "${name}" ?`,
            onConfirm: doDelete
        });
    } else if (confirm(`Supprimer le client "${name}" ?`)) {
        doDelete();
    }
}

function saveClient() {
    const formData = new URLSearchParams({
        action: document.getElementById('client_id').value ? 'update_client' : 'add_client',
        id_client: document.getElementById('client_id').value,
        nom_client: document.getElementById('client_name').value,
        telephone: document.getElementById('client_telephone').value,
        email: document.getElementById('client_email').value,
        adresse: document.getElementById('client_adresse').value
    });
    
    fetch('ajax/clients.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(modalClient).hide();
        }
        if (typeof showAlertModal === 'function') {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'error',
                onClose: () => { if (data.success) location.reload(); }
            });
        } else {
            alert(data.message);
            if (data.success) location.reload();
        }
    })
    .catch(err => { console.error(err); alert('Erreur réseau'); });
}

// ===== GESTION CATÉGORIES =====
const btnAddCategory = document.getElementById('btnAddCategory');
const modalCategory = document.getElementById('modalCategory');
const categoryForm = document.getElementById('categoryForm');

if (btnAddCategory) {
    btnAddCategory.addEventListener('click', () => openCategoryModal());
}
if (categoryForm) {
    categoryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveCategory();
    });
}
document.querySelectorAll('.btn-edit-category').forEach(btn => {
    btn.addEventListener('click', function() {
        editCategory(JSON.parse(this.getAttribute('data-category')));
    });
});
document.querySelectorAll('.btn-delete-category').forEach(btn => {
    btn.addEventListener('click', function() {
        deleteCategory(this.getAttribute('data-id'), this.getAttribute('data-name'), parseInt(this.getAttribute('data-products')));
    });
});

function openCategoryModal(category = null) {
    if (category) {
        document.getElementById('modalCategoryTitle').textContent = 'Modifier la catégorie';
        document.getElementById('category_id').value = category.id_categorie;
        document.getElementById('category_name').value = category.nom_categorie;
        document.getElementById('category_description').value = category.description || '';
    } else {
        document.getElementById('modalCategoryTitle').textContent = 'Nouvelle catégorie';
        categoryForm.reset();
        document.getElementById('category_id').value = '';
    }
    new bootstrap.Modal(modalCategory).show();
}

function editCategory(category) {
    openCategoryModal(category);
}

function deleteCategory(id, name, nbProducts) {
    if (nbProducts > 0) {
        if (typeof showAlertModal === 'function') {
            showAlertModal({
                title: 'Suppression impossible',
                message: `La catégorie "${name}" contient ${nbProducts} produit(s).`,
                type: 'error'
            });
        } else {
            alert(`Impossible : ${nbProducts} produit(s) dans cette catégorie.`);
        }
        return;
    }
    
    const doDelete = () => {
        fetch('ajax/categories.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'delete_category', id_categorie: id })
        })
        .then(r => r.json())
        .then(data => {
            if (typeof showAlertModal === 'function') {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'error',
                    onClose: () => { if (data.success) location.reload(); }
                });
            } else {
                alert(data.message);
                if (data.success) location.reload();
            }
        });
    };
    
    if (typeof showConfirmModal === 'function') {
        showConfirmModal({
            title: 'Confirmer la suppression',
            message: `Supprimer la catégorie "${name}" ?`,
            onConfirm: doDelete
        });
    } else if (confirm(`Supprimer la catégorie "${name}" ?`)) {
        doDelete();
    }
}

function saveCategory() {
    const formData = new URLSearchParams({
        action: document.getElementById('category_id').value ? 'update_category' : 'add_category',
        id_categorie: document.getElementById('category_id').value,
        nom_categorie: document.getElementById('category_name').value,
        description: document.getElementById('category_description').value
    });
    
    fetch('ajax/categories.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(modalCategory).hide();
        }
        if (typeof showAlertModal === 'function') {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'error',
                onClose: () => { if (data.success) location.reload(); }
            });
        } else {
            alert(data.message);
            if (data.success) location.reload();
        }
    })
    .catch(err => { console.error(err); alert('Erreur réseau'); });
}

</script>

<?php require_once('footer.php'); ?>
