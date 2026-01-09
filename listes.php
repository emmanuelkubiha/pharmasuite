<?php
/**
 * PAGE LISTES - STORE SUITE
 * Routeur pour toutes les listes (produits, clients, catégories, utilisateurs, mouvements, ventes)
 */
require_once 'protection_pages.php';

$page = $_GET['page'] ?? 'produits';
$pages_disponibles = ['produits', 'clients', 'categories', 'utilisateurs', 'mouvements', 'ventes'];

if (!in_array($page, $pages_disponibles)) {
    header('Location: listes.php?page=produits');
    exit;
}

// Titres de pages
$page_titles = [
    'produits' => 'Gestion des Produits',
    'clients' => 'Gestion des Clients',
    'categories' => 'Gestion des Catégories',
    'utilisateurs' => 'Gestion des Utilisateurs',
    'mouvements' => 'Mouvements de Stock',
    'ventes' => 'Historique des Ventes'
];

$page_title = $page_titles[$page];
include 'header.php';
?>

<div class="container-xl">
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?php echo $page_title; ?></h2>
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group w-100" role="group">
                <a href="?page=produits" class="btn btn-outline-primary <?php echo $page === 'produits' ? 'active' : ''; ?>">Produits</a>
                <a href="?page=clients" class="btn btn-outline-primary <?php echo $page === 'clients' ? 'active' : ''; ?>">Clients</a>
                <a href="?page=categories" class="btn btn-outline-primary <?php echo $page === 'categories' ? 'active' : ''; ?>">Catégories</a>
                <?php if ($is_admin): ?>
                <a href="?page=utilisateurs" class="btn btn-outline-primary <?php echo $page === 'utilisateurs' ? 'active' : ''; ?>">Utilisateurs</a>
                <?php endif; ?>
                <a href="?page=mouvements" class="btn btn-outline-primary <?php echo $page === 'mouvements' ? 'active' : ''; ?>">Mouvements</a>
                <a href="?page=ventes" class="btn btn-outline-primary <?php echo $page === 'ventes' ? 'active' : ''; ?>">Ventes</a>
            </div>
        </div>
    </div>

    <?php
    // Inclusion de la page appropriée
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
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des produits</h3>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-primary" onclick="openProductModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nouveau produit
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix achat</th>
                                <th>Prix vente</th>
                                <th>Stock</th>
                                <th>Seuil</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits as $p): ?>
                            <tr>
                                <td><strong><?php echo e($p['nom_produit']); ?></strong></td>
                                <td><?php echo e($p['nom_categorie'] ?? 'Sans catégorie'); ?></td>
                                <td><?php echo format_montant($p['prix_achat']); ?></td>
                                <td><?php echo format_montant($p['prix_vente']); ?></td>
                                <td>
                                    <span class="badge <?php echo $p['quantite_stock'] <= $p['seuil_alerte'] ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $p['quantite_stock']; ?>
                                    </span>
                                </td>
                                <td><?php echo $p['seuil_alerte']; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-icon btn-outline-primary" onclick='editProduct(<?php echo json_encode($p); ?>)' title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-outline-success" onclick="adjustStock(<?php echo $p['id_produit']; ?>, '<?php echo e($p['nom_produit']); ?>')" title="Ajuster stock">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteProduct(<?php echo $p['id_produit']; ?>, '<?php echo e($p['nom_produit']); ?>')" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
                        <form id="formProduit">
                            <input type="hidden" id="product_id" name="id_produit">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label required" data-bs-toggle="tooltip" title="Nom complet du produit (ex: Coca-Cola 1.5L, Samsung Galaxy S23, Pain au chocolat)">
                                            Nom du produit <i class="fa fa-info-circle text-muted ms-1"></i>
                                        </label>
                                        <input type="text" class="form-control" name="nom_produit" id="product_name" required placeholder="Ex: Coca-Cola 1.5L">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Catégorie pour organiser vos produits (Boissons, Électronique, Alimentaire, etc.)">
                                            Catégorie <i class="fa fa-info-circle text-muted ms-1"></i>
                                        </label>
                                        <select class="form-select" name="id_categorie" id="product_category">
                                            <option value="">Sans catégorie</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id_categorie']; ?>"><?php echo e($cat['nom_categorie']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Prix d'achat pour calculer les bénéfices. Visible uniquement par l'administrateur dans les statistiques.">
                                            Prix d'achat (<?php echo $devise; ?>) <i class="fa fa-lock text-muted ms-1"></i>
                                        </label>
                                        <input type="number" class="form-control" name="prix_achat" id="product_prix_achat" step="0.01" min="0" placeholder="0">
                                        <small class="text-muted">Pour les statistiques (confidentiel)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required" data-bs-toggle="tooltip" title="Prix de vente indicatif. Le vendeur peut ajuster ce prix lors de la vente selon les promotions ou négociations.">
                                            Prix de vente (<?php echo $devise; ?>) <i class="fa fa-info-circle text-muted ms-1"></i>
                                        </label>
                                        <input type="number" class="form-control" name="prix_vente" id="product_prix_vente" step="0.01" min="0" required placeholder="0">
                                        <small class="text-muted">Prix suggéré (modifiable à la vente)</small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Quantité en stock au moment de la création. Utilisez 'Ajuster stock' pour les modifications ultérieures.">
                                            Quantité initiale <i class="fa fa-info-circle text-muted ms-1"></i>
                                        </label>
                                        <input type="number" class="form-control" name="quantite_stock" id="product_stock" min="0" value="0" placeholder="0">
                                        <small class="text-muted">Stock de départ</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" data-bs-toggle="tooltip" title="Niveau minimum de stock avant alerte. Vous serez notifié quand le stock descend en dessous de ce seuil.">
                                            Seuil d'alerte <i class="fa fa-info-circle text-muted ms-1"></i>
                                        </label>
                                        <input type="number" class="form-control" name="seuil_alerte" id="product_seuil" min="0" value="5" placeholder="5">
                                        <small class="text-muted">Alerte de réapprovisionnement</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="Caractéristiques, détails ou informations supplémentaires (ex: couleur, taille, composition, date d'expiration)">
                                        Description <i class="fa fa-info-circle text-muted ms-1"></i>
                                    </label>
                                    <textarea class="form-control" name="description" id="product_description" rows="3" placeholder="Ex: Boisson gazeuse, format familial, goût original"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Modal Ajustement Stock -->
            <div class="modal fade" id="modalStock" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajuster le stock</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formStock">
                            <input type="hidden" id="stock_product_id">
                            <div class="modal-body">
                                <h6 id="stock_product_name" class="mb-3"></h6>
                                <div class="mb-3">
                                    <label class="form-label required">Type de mouvement</label>
                                    <select class="form-select" name="type_mouvement" required>
                                        <option value="entree">Entrée (ajout au stock)</option>
                                        <option value="sortie">Sortie (retrait du stock)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Quantité</label>
                                    <input type="number" class="form-control" name="quantite" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Motif</label>
                                    <input type="text" class="form-control" name="motif" required placeholder="Ex: Réapprovisionnement, Inventaire, Casse...">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success">Valider</button>
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
                (SELECT COUNT(*) FROM ventes WHERE id_client = c.id_client) as nb_ventes
                FROM clients c 
                WHERE est_actif = 1 
                ORDER BY nom_client ASC
            ");
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Liste des clients 
                        <span class="text-muted ms-2" data-bs-toggle="tooltip" title="Gérez votre base clients : ajoutez, modifiez ou recherchez rapidement">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                        </span>
                    </h3>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-primary" onclick="openClientModal()" data-bs-toggle="tooltip" title="Ajouter un nouveau client">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nouveau client
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Ventes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun client enregistré</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><strong><?php echo e($c['nom_client']); ?></strong></td>
                                <td><?php echo e($c['telephone'] ?? '-'); ?></td>
                                <td><?php echo e($c['email'] ?? '-'); ?></td>
                                <td><?php echo e($c['adresse'] ?? '-'); ?></td>
                                <td><span class="badge bg-info"><?php echo $c['nb_ventes']; ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-icon btn-outline-primary" onclick='editClient(<?php echo json_encode($c); ?>)' data-bs-toggle="tooltip" title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteClient(<?php echo $c['id_client']; ?>, '<?php echo e($c['nom_client']); ?>', <?php echo $c['nb_ventes']; ?>)" data-bs-toggle="tooltip" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
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
            
            <!-- Modal Client -->
            <div class="modal fade" id="modalClient" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalClientTitle">Nouveau client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formClient">
                            <input type="hidden" id="client_id" name="id_client">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label required">Nom du client</label>
                                    <input type="text" class="form-control" name="nom_client" id="client_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" name="telephone" id="client_phone">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="client_email">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Adresse</label>
                                    <textarea class="form-control" name="adresse" id="client_address" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
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
                (SELECT COUNT(*) FROM produits WHERE id_categorie = c.id_categorie AND est_actif = 1) as nb_produits
                FROM categories c 
                WHERE c.est_actif = 1 
                ORDER BY c.nom_categorie ASC
            ");
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des catégories</h3>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-primary" onclick="openCategoryModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nouvelle catégorie
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nb produits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><strong><?php echo e($cat['nom_categorie']); ?></strong></td>
                                <td><?php echo e($cat['description'] ?? '-'); ?></td>
                                <td><span class="badge bg-info"><?php echo $cat['nb_produits']; ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-icon btn-outline-primary" onclick='editCategory(<?php echo json_encode($cat); ?>)' title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteCategory(<?php echo $cat['id_categorie']; ?>, '<?php echo e($cat['nom_categorie']); ?>', <?php echo $cat['nb_produits']; ?>)" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                        <form id="formCategory">
                            <input type="hidden" id="category_id" name="id_categorie">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label required">Nom de la catégorie</label>
                                    <input type="text" class="form-control" name="nom_categorie" id="category_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="category_description" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            break;

        case 'utilisateurs':
            if (!$is_admin) {
                echo '<div class="alert alert-danger">Accès refusé</div>';
                break;
            }
            $utilisateurs = db_fetch_all("
                SELECT u.*, 
                (SELECT COUNT(*) FROM ventes WHERE id_vendeur = u.id_utilisateur) as nb_ventes
                FROM utilisateurs u 
                WHERE u.est_actif = 1 
                ORDER BY u.nom_utilisateur ASC
            ");
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des utilisateurs</h3>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-primary" onclick="openUserModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nouvel utilisateur
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Ventes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $u): ?>
                            <tr>
                                <td><strong><?php echo e($u['nom_utilisateur']); ?></strong></td>
                                <td><?php echo e($u['login']); ?></td>
                                <td><?php echo e($u['email'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php echo $u['role'] === 'admin' ? 'bg-primary' : 'bg-secondary'; ?>">
                                        <?php echo e($u['role']); ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-info"><?php echo $u['nb_ventes']; ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-icon btn-outline-primary" onclick='editUser(<?php echo json_encode($u); ?>)' title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <?php if ($u['id_utilisateur'] != $user_id): ?>
                                        <button class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteUser(<?php echo $u['id_utilisateur']; ?>, '<?php echo e($u['nom_utilisateur']); ?>', <?php echo $u['nb_ventes']; ?>)" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal Utilisateur -->
            <div class="modal fade" id="modalUser" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUserTitle">Nouvel utilisateur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="formUser">
                            <input type="hidden" id="user_id_field" name="id_utilisateur">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label required">Nom complet</label>
                                    <input type="text" class="form-control" name="nom_utilisateur" id="user_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Login</label>
                                    <input type="text" class="form-control" name="login" id="user_login" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="user_email">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Mot de passe <span id="password_optional" style="display:none;" class="text-muted">(laisser vide pour ne pas modifier)</span></label>
                                    <input type="password" class="form-control" name="password" id="user_password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Rôle</label>
                                    <select class="form-select" name="role" id="user_role" required>
                                        <option value="vendeur">Vendeur</option>
                                        <option value="admin">Administrateur</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
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
                SELECT m.*, p.nom_produit, u.nom_utilisateur
                FROM mouvements m
                LEFT JOIN produits p ON m.id_produit = p.id_produit
                LEFT JOIN utilisateurs u ON m.id_utilisateur = u.id_utilisateur
                ORDER BY m.date_mouvement DESC
                LIMIT 100
            ");
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Historique des mouvements de stock
                        <span class="text-muted ms-2" data-bs-toggle="tooltip" title="Traçabilité complète des entrées et sorties de stock (100 derniers mouvements)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                        </span>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Utilisateur</th>
                                <th>Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mouvements)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucun mouvement de stock enregistré</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($mouvements as $m): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($m['date_mouvement'])); ?></td>
                                <td><strong><?php echo e($m['nom_produit'] ?? 'N/A'); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $m['type_mouvement'] === 'entree' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo strtoupper(e($m['type_mouvement'])); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $m['quantite']; ?></strong></td>
                                <td><?php echo e($m['nom_utilisateur'] ?? '-'); ?></td>
                                <td><?php echo e($m['motif'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            break;

        case 'ventes':
            $ventes = db_fetch_all("
                SELECT v.*, c.nom_client, u.nom_utilisateur
                FROM ventes v
                LEFT JOIN clients c ON v.id_client = c.id_client
                LEFT JOIN utilisateurs u ON v.id_vendeur = u.id_utilisateur
                WHERE v.statut = 'validee'
                ORDER BY v.date_vente DESC
                LIMIT 100
            ");
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Historique des ventes
                        <span class="text-muted ms-2" data-bs-toggle="tooltip" title="Consultez l'historique de toutes les ventes validées (100 dernières). Cliquez sur une facture pour l'imprimer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                        </span>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>N° Facture</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Vendeur</th>
                                <th>Montant</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ventes)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Aucune vente enregistrée</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($ventes as $v): ?>
                            <tr>
                                <td><strong><?php echo e($v['numero_facture']); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($v['date_vente'])); ?></td>
                                <td><?php echo e($v['nom_client'] ?? 'Client Comptoir'); ?></td>
                                <td><?php echo e($v['nom_utilisateur']); ?></td>
                                <td><strong><?php echo format_montant($v['montant_total']); ?></strong></td>
                                <td>
                                    <a href="facture_impression.php?id=<?php echo $v['id_vente']; ?>" class="btn btn-sm btn-outline-primary" target="_blank" data-bs-toggle="tooltip" title="Ouvrir la facture">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><line x1="9" y1="9" x2="10" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                                        Facture
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            break;
    }
    ?>
</div>

<script>
// Initialiser les tooltips Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============ GESTION DES CLIENTS ============
function openClientModal() {
    document.getElementById('modalClientTitle').textContent = 'Nouveau client';
    document.getElementById('formClient').reset();
    document.getElementById('client_id').value = '';
    new bootstrap.Modal(document.getElementById('modalClient')).show();
}

function editClient(client) {
    document.getElementById('modalClientTitle').textContent = 'Modifier le client';
    document.getElementById('client_id').value = client.id_client;
    document.getElementById('client_name').value = client.nom_client;
    document.getElementById('client_phone').value = client.telephone || '';
    document.getElementById('client_email').value = client.email || '';
    document.getElementById('client_address').value = client.adresse || '';
    new bootstrap.Modal(document.getElementById('modalClient')).show();
}

function deleteClient(id, nom, nbVentes) {
    let message = `⚠️ ATTENTION\n\nÊtes-vous sûr de vouloir supprimer le client "${nom}" ?\n\n`;
    
    if (nbVentes > 0) {
        message += `⚠️ IMPORTANT : Ce client a ${nbVentes} vente(s) enregistrée(s).\n\n`;
        message += `Le client sera DÉSACTIVÉ (et non supprimé) pour préserver l'historique.\n\nContinuer ?`;
    } else {
        message += `Ce client n'a aucune vente associée.\nIl sera DÉFINITIVEMENT supprimé.\n\nContinuer ?`;
    }
    
    showConfirmModal({
        title: 'Supprimer le client',
        message: message,
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, supprimer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_client');
            formData.append('id_client', id);
            
            fetch('ajax/clients.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'danger',
                    icon: data.success ? 'success' : 'danger'
                }).then(() => {
                    if (data.success) location.reload();
                });
            })
            .catch(e => {
                showAlertModal({
                    title: 'Erreur',
                    message: 'Erreur: ' + e,
                    type: 'danger',
                    icon: 'danger'
                });
            });
        }
    });
}

document.getElementById('formClient')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = document.getElementById('client_id').value !== '';
    formData.append('action', isEdit ? 'update_client' : 'add_client');
    
    showConfirmModal({
        title: isEdit ? 'Modifier le client' : 'Ajouter un client',
        message: `Confirmer ${isEdit ? 'la modification' : 'l\'ajout'} du client ?`,
        icon: 'info',
        type: 'primary',
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (!confirmed) return;
        
        fetch('ajax/clients.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'danger',
                icon: data.success ? 'success' : 'danger'
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(e => {
            showAlertModal({
                title: 'Erreur',
                message: 'Erreur: ' + e,
                type: 'danger',
                icon: 'danger'
            });
        });
    });
});
        if (data.success) location.reload();
    })
    .catch(e => alert('Erreur: ' + e));
});

// ============ GESTION DES PRODUITS ============
function openProductModal() {
    document.getElementById('modalProduitTitle').textContent = 'Nouveau produit';
    document.getElementById('formProduit').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('product_stock').disabled = false;
    new bootstrap.Modal(document.getElementById('modalProduit')).show();
}

function editProduct(product) {
    document.getElementById('modalProduitTitle').textContent = 'Modifier le produit';
    document.getElementById('product_id').value = product.id_produit;
    document.getElementById('product_name').value = product.nom_produit;
    document.getElementById('product_category').value = product.id_categorie || '';
    document.getElementById('product_prix_achat').value = product.prix_achat;
    document.getElementById('product_prix_vente').value = product.prix_vente;
    document.getElementById('product_seuil').value = product.seuil_alerte;
    document.getElementById('product_description').value = product.description || '';
    document.getElementById('product_stock').disabled = true; // Ne pas modifier le stock ici
    new bootstrap.Modal(document.getElementById('modalProduit')).show();
}

function deleteProduct(id, nom) {
    showConfirmModal({
        title: '⚠️ Supprimer le produit',
        message: `Êtes-vous sûr de vouloir supprimer "${nom}" ?\n\nSi ce produit a des ventes associées, il sera désactivé au lieu d'être supprimé pour préserver l'historique.`,
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, supprimer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_product');
            formData.append('id_produit', id);
            
            fetch('ajax/produits.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'danger',
                    icon: data.success ? 'success' : 'danger'
                }).then(() => {
                    if (data.success) location.reload();
                });
            })
            .catch(e => {
                showAlertModal({
                    title: 'Erreur',
                    message: 'Erreur: ' + e,
                    type: 'danger',
                    icon: 'danger'
                });
            });
        }
    });
}

function adjustStock(id, nom) {
    document.getElementById('stock_product_id').value = id;
    document.getElementById('stock_product_name').textContent = nom;
    document.getElementById('formStock').reset();
    new bootstrap.Modal(document.getElementById('modalStock')).show();
}

document.getElementById('formProduit')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = document.getElementById('product_id').value !== '';
    formData.append('action', isEdit ? 'update_product' : 'add_product');
    
    showConfirmModal({
        title: isEdit ? 'Modifier le produit' : 'Ajouter un produit',
        message: `Confirmer ${isEdit ? 'la modification' : 'l\'ajout'} du produit ?`,
        icon: 'info',
        type: 'primary',
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (!confirmed) return;
        
        fetch('ajax/produits.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'danger',
                icon: data.success ? 'success' : 'danger'
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(e => {
            showAlertModal({
                title: 'Erreur',
                message: 'Erreur: ' + e,
                type: 'danger',
                icon: 'danger'
            });
        });
    });
});

document.getElementById('formStock')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'adjust_stock');
    formData.append('id_produit', document.getElementById('stock_product_id').value);
    
    showConfirmModal({
        title: 'Ajuster le stock',
        message: 'Confirmer l\'ajustement du stock ?',
        icon: 'warning',
        type: 'primary',
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (!confirmed) return;
        
        fetch('ajax/produits.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'danger',
                icon: data.success ? 'success' : 'danger'
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(e => {
            showAlertModal({
                title: 'Erreur',
                message: 'Erreur: ' + e,
                type: 'danger',
                icon: 'danger'
            });
        });
    });
});)
    .catch(e => alert('Erreur: ' + e));
});

// ============ GESTION DES CATÉGORIES ============
function openCategoryModal() {
    document.getElementById('modalCategoryTitle').textContent = 'Nouvelle catégorie';
    document.getElementById('formCategory').reset();
    document.getElementById('category_id').value = '';
    new bootstrap.Modal(document.getElementById('modalCategory')).show();
}

function editCategory(category) {
    document.getElementById('modalCategoryTitle').textContent = 'Modifier la catégorie';
    document.getElementById('category_id').value = category.id_categorie;
    document.getElementById('category_name').value = category.nom_categorie;
    document.getElementById('category_description').value = category.description || '';
    new bootstrap.Modal(document.getElementById('modalCategory')).show();
}

function deleteCategory(id, nom, nbProduits) {
    if (nbProduits > 0) {
        showAlertModal({
            title: '❌ Impossible de supprimer',
            message: `Impossible de supprimer "${nom}"\n\nCette catégorie contient ${nbProduits} produit(s) actif(s).\nSupprimez ou réaffectez les produits avant de supprimer la catégorie.`,
            type: 'danger',
            icon: 'danger'
        });
        return;
    }
    
    showConfirmModal({
        title: '⚠️ Supprimer la catégorie',
        message: `Êtes-vous sûr de vouloir supprimer la catégorie "${nom}" ?`,
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, supprimer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_category');
            formData.append('id_categorie', id);
            
            fetch('ajax/categories.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'danger',
                    icon: data.success ? 'success' : 'danger'
                }).then(() => {
                    if (data.success) location.reload();
                });
            })
            .catch(e => {
                showAlertModal({
                    title: 'Erreur',
                    message: 'Erreur: ' + e,
                    type: 'danger',
                    icon: 'danger'
                });
            });
        }
    });
}

document.getElementById('formCategory')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = document.getElementById('category_id').value !== '';
    formData.append('action', isEdit ? 'update_category' : 'add_category');
    
    showConfirmModal({
        title: isEdit ? 'Modifier la catégorie' : 'Ajouter une catégorie',
        message: `Confirmer ${isEdit ? 'la modification' : 'l\'ajout'} de la catégorie ?`,
        icon: 'info',
        type: 'primary',
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (!confirmed) return;
        
        fetch('ajax/categories.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'danger',
                icon: data.success ? 'success' : 'danger'
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(e => {
            showAlertModal({
                title: 'Erreur',
                message: 'Erreur: ' + e,
                type: 'danger',
                icon: 'danger'
            });
        });
    });
});

// ============ GESTION DES UTILISATEURS ============
function openUserModal() {
    document.getElementById('modalUserTitle').textContent = 'Nouvel utilisateur';
    document.getElementById('formUser').reset();
    document.getElementById('user_id_field').value = '';
    document.getElementById('user_password').required = true;
    document.getElementById('password_optional').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}

function editUser(user) {
    document.getElementById('modalUserTitle').textContent = 'Modifier l\'utilisateur';
    document.getElementById('user_id_field').value = user.id_utilisateur;
    document.getElementById('user_name').value = user.nom_utilisateur;
    document.getElementById('user_login').value = user.login;
    document.getElementById('user_email').value = user.email || '';
    document.getElementById('user_role').value = user.role;
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = false;
    document.getElementById('password_optional').style.display = 'inline';
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}

function deleteUser(id, nom, nbVentes) {
    let message = `Êtes-vous sûr de vouloir supprimer l'utilisateur "${nom}" ?\n\n`;
    
    if (nbVentes > 0) {
        message += `⚠️ IMPORTANT : Cet utilisateur a ${nbVentes} vente(s) enregistrée(s).\n\n`;
        message += `Le compte sera DÉSACTIVÉ (et non supprimé) pour préserver l'historique des ventes.`;
    } else {
        message += `Cet utilisateur n'a aucune vente associée.\nIl sera DÉFINITIVEMENT supprimé.`;
    }
    
    showConfirmModal({
        title: '⚠️ Supprimer l\'utilisateur',
        message: message,
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, supprimer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('id_utilisateur', id);
            
            fetch('ajax/utilisateurs.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                showAlertModal({
                    title: data.success ? 'Succès' : 'Erreur',
                    message: data.message,
                    type: data.success ? 'success' : 'danger',
                    icon: data.success ? 'success' : 'danger'
                }).then(() => {
                    if (data.success) location.reload();
                });
            })
            .catch(e => {
                showAlertModal({
                    title: 'Erreur',
                    message: 'Erreur: ' + e,
                    type: 'danger',
                    icon: 'danger'
                });
            });
        }
    });
}

document.getElementById('formUser')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const isEdit = document.getElementById('user_id_field').value !== '';
    formData.append('action', isEdit ? 'update_user' : 'add_user');
    
    showConfirmModal({
        title: isEdit ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur',
        message: `Confirmer ${isEdit ? 'la modification' : 'l\'ajout'} de l'utilisateur ?`,
        icon: 'info',
        type: 'primary',
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (!confirmed) return;
        
        fetch('ajax/utilisateurs.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            showAlertModal({
                title: data.success ? 'Succès' : 'Erreur',
                message: data.message,
                type: data.success ? 'success' : 'danger',
                icon: data.success ? 'success' : 'danger'
            }).then(() => {
                if (data.success) location.reload();
            });
        })
        .catch(e => {
            showAlertModal({
                title: 'Erreur',
                message: 'Erreur: ' + e,
                type: 'danger',
                icon: 'danger'
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>
