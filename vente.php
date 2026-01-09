<?php
/**
 * PAGE DE VENTE - STORE SUITE
 * Interface moderne pour enregistrer les ventes
 */
require_once 'protection_pages.php';
$page_title = 'Nouvelle Vente';

// Récupération des produits actifs
$produits = db_fetch_all("
    SELECT p.*, c.nom_categorie
    FROM produits p
    LEFT JOIN categories c ON p.id_categorie = c.id_categorie
    WHERE p.est_actif = 1 AND p.quantite_stock > 0
    ORDER BY p.nom_produit ASC
");

// Récupération des clients
$clients = db_fetch_all("
    SELECT * FROM clients 
    WHERE est_actif = 1 
    ORDER BY nom_client ASC
");

include 'header.php';
?>

<style>
.product-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: <?php echo $couleur_primaire; ?>;
}

.cart-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.cart-total {
    background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
}
</style>

<div class="container-xl">
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="6" cy="19" r="2"/>
                        <circle cx="17" cy="19" r="2"/>
                        <path d="M17 17h-11v-14h-2"/>
                        <path d="M6 5l14 1l-1 7h-13"/>
                    </svg>
                    Nouvelle Vente
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sélection des produits</h3>
                    <div class="col-auto ms-auto">
                        <input type="text" class="form-control" id="searchProduct" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="productsList">
                        <?php foreach ($produits as $produit): ?>
                        <div class="col-md-4 col-sm-6 product-item">
                            <div class="product-card card" onclick="addToCart(<?php echo $produit['id_produit']; ?>, '<?php echo addslashes(e($produit['nom_produit'])); ?>', <?php echo $produit['prix_vente']; ?>, <?php echo $produit['quantite_stock']; ?>)">
                                <div class="card-body">
                                    <h4 class="mb-1 fs-6"><?php echo e($produit['nom_produit']); ?></h4>
                                    <div class="text-muted small mb-2"><?php echo e($produit['nom_categorie'] ?? 'Sans catégorie'); ?></div>
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-primary"><?php echo format_montant($produit['prix_vente']); ?></strong>
                                        <span class="badge bg-success">Stock: <?php echo $produit['quantite_stock']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px; z-index: 99;">
                <div class="card-header">
                    <h3 class="card-title">Panier</h3>
                    <div class="col-auto ms-auto">
                        <button class="btn btn-sm btn-ghost-danger" onclick="clearCart()">Vider</button>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;" id="cartItems">
                    <div class="text-center p-4 text-muted">
                        <div class="mb-2">Panier vide</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="mb-3">
                        <label class="form-label">Client (optionnel)</label>
                        <select class="form-select" id="clientSelect">
                            <option value="">Client Comptoir</option>
                            <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id_client']; ?>"><?php echo e($client['nom_client']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cart-total mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total:</span>
                            <strong class="fs-3" id="cartTotal">0 <?php echo $devise; ?></strong>
                        </div>
                    </div>
                    <div class="btn-group w-100 mb-2">
                        <button class="btn btn-primary btn-lg" onclick="processSale()" id="btnProcessSale" disabled style="flex: 1;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6"/><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/></svg>
                            Valider la vente
                        </button>
                    </div>
                    <button class="btn btn-warning w-100" onclick="generateProforma()" id="btnProforma" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><line x1="9" y1="7" x2="10" y2="7"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="13" y1="17" x2="15" y2="17"/></svg>
                        Facture Proforma (Devis)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, nom, prix, stockMax) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        if (existing.quantite < stockMax) {
            existing.quantite++;
        } else {
            alert('Stock insuffisant');
            return;
        }
    } else {
        cart.push({ id, nom, prix, quantite: 1, stockMax });
    }
    updateCart();
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const btnProcessSale = document.getElementById('btnProcessSale');
    const btnProforma = document.getElementById('btnProforma');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="text-center p-4 text-muted"><div class="mb-2">Panier vide</div></div>';
        btnProcessSale.disabled = true;
        btnProforma.disabled = true;
        document.getElementById('cartTotal').textContent = '0 <?php echo $devise; ?>';
        return;
    }
    
    btnProcessSale.disabled = false;
    btnProforma.disabled = false;
    let html = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const subtotal = item.prix * item.quantite;
        total += subtotal;
        html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between mb-2">
                    <strong>${item.nom}</strong>
                    <button class="btn btn-sm btn-ghost-danger" onclick="removeFromCart(${index})">×</button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="input-group" style="width: 120px;">
                        <button class="btn btn-sm" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control form-control-sm text-center" value="${item.quantite}" readonly>
                        <button class="btn btn-sm" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <strong>${subtotal.toLocaleString()} <?php echo $devise; ?></strong>
                </div>
            </div>
        `;
    });
    
    cartItems.innerHTML = html;
    document.getElementById('cartTotal').textContent = total.toLocaleString() + ' <?php echo $devise; ?>';
}

function updateQuantity(index, delta) {
    const item = cart[index];
    const newQty = item.quantite + delta;
    if (newQty > 0 && newQty <= item.stockMax) {
        item.quantite = newQty;
        updateCart();
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function clearCart() {
    if (confirm('Vider le panier ?')) {
        cart = [];
        updateCart();
    }
}

function processSale() {
    if (cart.length === 0) return;
    
    alert('Fonctionnalité en cours de développement. Créez le fichier ajax/process_vente.php pour traiter les ventes.');
}

function generateProforma() {
    if (cart.length === 0) {
        alert('Le panier est vide !');
        return;
    }
    
    // Créer un formulaire et le soumettre vers proforma.php
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'proforma.php';
    form.target = '_blank';
    
    // Ajouter les items du panier
    cart.forEach((item, index) => {
        const inputNom = document.createElement('input');
        inputNom.type = 'hidden';
        inputNom.name = `cart_items[${index}][nom]`;
        inputNom.value = item.nom;
        form.appendChild(inputNom);
        
        const inputPrix = document.createElement('input');
        inputPrix.type = 'hidden';
        inputPrix.name = `cart_items[${index}][prix]`;
        inputPrix.value = item.prix;
        form.appendChild(inputPrix);
        
        const inputQte = document.createElement('input');
        inputQte.type = 'hidden';
        inputQte.name = `cart_items[${index}][quantite]`;
        inputQte.value = item.quantite;
        form.appendChild(inputQte);
    });
    
    // Ajouter le total
    const total = cart.reduce((sum, item) => sum + (item.prix * item.quantite), 0);
    const inputTotal = document.createElement('input');
    inputTotal.type = 'hidden';
    inputTotal.name = 'total';
    inputTotal.value = total;
    form.appendChild(inputTotal);
    
    // Ajouter le nom du client
    const clientSelect = document.getElementById('clientSelect');
    const clientName = clientSelect.options[clientSelect.selectedIndex].text;
    const inputClient = document.createElement('input');
    inputClient.type = 'hidden';
    inputClient.name = 'client_name';
    inputClient.value = clientName;
    form.appendChild(inputClient);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

document.getElementById('searchProduct').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
});
</script>

<?php include 'footer.php'; ?>
