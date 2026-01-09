<?php
/**
 * PAGE DE VENTE PROFESSIONNELLE - STORE SUITE
 * Interface complète avec modification de prix, quantité et TVA 16%
 */
require_once 'protection_pages.php';
$page_title = 'Point de Vente';

// Récupération des produits actifs avec stock
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
    transition: all 0.2s ease;
    border: 2px solid #e9ecef;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    border-color: <?php echo $couleur_primaire; ?>;
}

.cart-container {
    position: sticky;
    top: 80px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
}

.cart-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s;
}

.cart-item:hover {
    background: #f8f9fa;
}

.cart-totals {
    background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.qty-input {
    width: 70px;
    text-align: center;
}

.price-input {
    width: 120px;
}

.badge-stock {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>

<div class="container-fluid">
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                    Point de Vente
                </h2>
            </div>
            <div class="col-auto">
                <button class="btn btn-danger" onclick="clearCart()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                    Vider
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Liste des produits -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Produits disponibles</h3>
                    <div class="col-auto ms-auto">
                        <input type="text" class="form-control" id="searchProduct" placeholder="🔍 Rechercher un produit...">
                    </div>
                </div>
                <div class="card-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div class="row g-3" id="productsList">
                        <?php foreach ($produits as $produit): ?>
                        <div class="col-md-6 col-xl-4 product-item" data-name="<?php echo strtolower(e($produit['nom_produit'])); ?>">
                            <div class="product-card card" onclick='selectProduct(<?php echo json_encode($produit); ?>)'>
                                <div class="card-body position-relative">
                                    <span class="badge-stock badge <?php echo $produit['quantite_stock'] <= $produit['seuil_alerte'] ? 'bg-warning' : 'bg-success'; ?>">
                                        Stock: <?php echo $produit['quantite_stock']; ?>
                                    </span>
                                    <h4 class="mb-2 fs-5"><?php echo e($produit['nom_produit']); ?></h4>
                                    <div class="text-muted small mb-2"><?php echo e($produit['nom_categorie'] ?? 'Sans catégorie'); ?></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-4 fw-bold text-primary"><?php echo number_format($produit['prix_vente'], 2, ',', ' '); ?> <?php echo $devise; ?></span>
                                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); selectProduct(<?php echo htmlspecialchars(json_encode($produit), ENT_QUOTES); ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panier -->
        <div class="col-lg-5">
            <div class="cart-container">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                            Panier (<span id="cartCount">0</span>)
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div id="cartItems" class="mb-3" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                            <div class="text-center text-muted py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                                <div>Panier vide</div>
                                <small>Sélectionnez des produits pour commencer</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Sélection client -->
                        <div class="mb-3">
                            <label class="form-label">Client</label>
                            <select class="form-select" id="clientSelect">
                                <option value="">Vente au comptoir</option>
                                <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['id_client']; ?>">
                                    <?php echo e($client['nom_client']); ?> 
                                    <?php if (!empty($client['telephone'])): ?>
                                    - <?php echo e($client['telephone']); ?>
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Mode de paiement -->
                        <div class="mb-3">
                            <label class="form-label">Mode de paiement</label>
                            <select class="form-select" id="paymentMethod" required>
                                <option value="especes">Espèces</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>

                        <!-- Totaux avec TVA -->
                        <div class="cart-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total HT:</span>
                                <strong id="cartSubtotal">0.00 <?php echo $devise; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-white border-opacity-25">
                                <span>TVA (16%):</span>
                                <strong id="cartTVA">0.00 <?php echo $devise; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-4">Total TTC:</span>
                                <strong class="fs-2" id="cartTotal">0.00 <?php echo $devise; ?></strong>
                            </div>
                        </div>

                        <!-- Bouton valider -->
                        <button class="btn btn-success w-100 mt-3 btn-lg" id="btnValidate" onclick="validateSale()" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                            Valider la vente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout au panier -->
<div class="modal fade" id="modalAddToCart" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter au panier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Produit</label>
                    <div id="modalProductName" class="fs-5"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><span class="text-danger">*</span> Quantité</label>
                        <input type="number" class="form-control form-control-lg" id="modalQuantity" value="1" min="1" required>
                        <small class="text-muted">Stock disponible: <span id="modalStock"></span></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><span class="text-danger">*</span> Prix unitaire (<?php echo $devise; ?>)</label>
                        <input type="number" class="form-control form-control-lg" id="modalPrice" step="0.01" min="0" required>
                        <small class="text-muted">Prix catalogue: <span id="modalOriginalPrice"></span> <?php echo $devise; ?></small>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <strong>Sous-total:</strong> <span id="modalSubtotal">0.00</span> <?php echo $devise; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary btn-lg" onclick="confirmAddToCart()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                    Ajouter au panier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let cart = [];
let selectedProduct = null;
const TVA_RATE = 0.16; // 16%

// Recherche produit
document.getElementById('searchProduct').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(search) ? '' : 'none';
    });
});

// Sélectionner un produit
function selectProduct(product) {
    selectedProduct = product;
    document.getElementById('modalProductName').textContent = product.nom_produit;
    document.getElementById('modalStock').textContent = product.quantite_stock;
    document.getElementById('modalQuantity').value = 1;
    document.getElementById('modalQuantity').max = product.quantite_stock;
    document.getElementById('modalPrice').value = parseFloat(product.prix_vente);
    document.getElementById('modalOriginalPrice').textContent = parseFloat(product.prix_vente).toFixed(2);
    updateModalSubtotal();
    new bootstrap.Modal(document.getElementById('modalAddToCart')).show();
}

// Mettre à jour le sous-total du modal
function updateModalSubtotal() {
    const qty = parseFloat(document.getElementById('modalQuantity').value) || 0;
    const price = parseFloat(document.getElementById('modalPrice').value) || 0;
    const subtotal = qty * price;
    document.getElementById('modalSubtotal').textContent = subtotal.toFixed(2);
}

document.getElementById('modalQuantity').addEventListener('input', updateModalSubtotal);
document.getElementById('modalPrice').addEventListener('input', updateModalSubtotal);

// Confirmer l'ajout au panier
function confirmAddToCart() {
    const qty = parseInt(document.getElementById('modalQuantity').value);
    const price = parseFloat(document.getElementById('modalPrice').value);
    
    if (!qty || qty < 1 || qty > selectedProduct.quantite_stock) {
        showAlertModal({
            title: 'Quantité invalide',
            message: `La quantité doit être entre 1 et ${selectedProduct.quantite_stock}`,
            type: 'warning',
            icon: 'warning'
        });
        return;
    }
    
    if (!price || price < 0) {
        showAlertModal({
            title: 'Prix invalide',
            message: 'Le prix doit être supérieur à 0',
            type: 'warning',
            icon: 'warning'
        });
        return;
    }
    
    // Vérifier si le produit est déjà dans le panier
    const existingIndex = cart.findIndex(item => item.id === selectedProduct.id_produit);
    
    if (existingIndex >= 0) {
        // Mettre à jour la quantité et le prix
        cart[existingIndex].quantity = qty;
        cart[existingIndex].price = price;
        cart[existingIndex].subtotal = qty * price;
    } else {
        // Ajouter nouveau produit
        cart.push({
            id: selectedProduct.id_produit,
            name: selectedProduct.nom_produit,
            price: price,
            originalPrice: parseFloat(selectedProduct.prix_vente),
            quantity: qty,
            maxStock: selectedProduct.quantite_stock,
            subtotal: qty * price
        });
    }
    
    bootstrap.Modal.getInstance(document.getElementById('modalAddToCart')).hide();
    updateCart();
    
    showAlertModal({
        title: 'Produit ajouté',
        message: `${selectedProduct.nom_produit} a été ajouté au panier`,
        type: 'success',
        icon: 'success'
    });
}

// Mettre à jour l'affichage du panier
function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const btnValidate = document.getElementById('btnValidate');
    
    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center text-muted py-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="6" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                <div>Panier vide</div>
                <small>Sélectionnez des produits pour commencer</small>
            </div>
        `;
        cartCount.textContent = '0';
        btnValidate.disabled = true;
    } else {
        let html = '';
        cart.forEach((item, index) => {
            html += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <strong>${item.name}</strong>
                            ${item.price !== item.originalPrice ? '<span class="badge bg-warning ms-2">Prix modifié</span>' : ''}
                        </div>
                        <button class="btn btn-sm btn-icon btn-ghost-danger" onclick="removeFromCart(${index})" title="Supprimer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-4">
                            <label class="form-label form-label-sm mb-1">Qté</label>
                            <input type="number" class="form-control form-control-sm qty-input" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="updateItemQuantity(${index}, this.value)">
                        </div>
                        <div class="col-4">
                            <label class="form-label form-label-sm mb-1">Prix</label>
                            <input type="number" class="form-control form-control-sm price-input" value="${item.price.toFixed(2)}" min="0" step="0.01" onchange="updateItemPrice(${index}, this.value)">
                        </div>
                        <div class="col-4 text-end">
                            <label class="form-label form-label-sm mb-1">Total</label>
                            <div class="fw-bold text-primary">${item.subtotal.toFixed(2)} <?php echo $devise; ?></div>
                        </div>
                    </div>
                </div>
            `;
        });
        cartItems.innerHTML = html;
        cartCount.textContent = cart.length;
        btnValidate.disabled = false;
    }
    
    updateTotals();
}

// Mettre à jour les totaux avec TVA
function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const tva = subtotal * TVA_RATE;
    const total = subtotal + tva;
    
    document.getElementById('cartSubtotal').textContent = subtotal.toFixed(2) + ' <?php echo $devise; ?>';
    document.getElementById('cartTVA').textContent = tva.toFixed(2) + ' <?php echo $devise; ?>';
    document.getElementById('cartTotal').textContent = total.toFixed(2) + ' <?php echo $devise; ?>';
}

// Mettre à jour la quantité d'un article
function updateItemQuantity(index, newQty) {
    newQty = parseInt(newQty);
    if (newQty < 1 || newQty > cart[index].maxStock) {
        showAlertModal({
            title: 'Quantité invalide',
            message: `La quantité doit être entre 1 et ${cart[index].maxStock}`,
            type: 'warning',
            icon: 'warning'
        });
        updateCart();
        return;
    }
    cart[index].quantity = newQty;
    cart[index].subtotal = newQty * cart[index].price;
    updateCart();
}

// Mettre à jour le prix d'un article
function updateItemPrice(index, newPrice) {
    newPrice = parseFloat(newPrice);
    if (newPrice < 0) {
        showAlertModal({
            title: 'Prix invalide',
            message: 'Le prix doit être supérieur à 0',
            type: 'warning',
            icon: 'warning'
        });
        updateCart();
        return;
    }
    cart[index].price = newPrice;
    cart[index].subtotal = cart[index].quantity * newPrice;
    updateCart();
}

// Supprimer un article
function removeFromCart(index) {
    showConfirmModal({
        title: 'Supprimer l\'article',
        message: `Voulez-vous retirer "${cart[index].name}" du panier ?`,
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, supprimer',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            cart.splice(index, 1);
            updateCart();
        }
    });
}

// Vider le panier
function clearCart() {
    if (cart.length === 0) return;
    
    showConfirmModal({
        title: 'Vider le panier',
        message: 'Voulez-vous vraiment vider le panier ?',
        icon: 'warning',
        type: 'danger',
        confirmText: 'Oui, vider',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            cart = [];
            updateCart();
        }
    });
}

// Valider la vente
function validateSale() {
    if (cart.length === 0) {
        showAlertModal({
            title: 'Panier vide',
            message: 'Ajoutez des produits avant de valider',
            type: 'warning',
            icon: 'warning'
        });
        return;
    }
    
    const idClient = document.getElementById('clientSelect').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const tva = subtotal * TVA_RATE;
    const total = subtotal + tva;
    
    showConfirmModal({
        title: 'Confirmer la vente',
        message: `Total TTC: ${total.toFixed(2)} <?php echo $devise; ?>\n\nValider cette vente ?`,
        icon: 'info',
        type: 'success',
        confirmText: 'Oui, valider',
        cancelText: 'Annuler'
    }).then(confirmed => {
        if (confirmed) {
            // Envoyer au serveur
            const formData = new FormData();
            formData.append('id_client', idClient);
            formData.append('mode_paiement', paymentMethod);
            formData.append('montant_ht', subtotal.toFixed(2));
            formData.append('montant_tva', tva.toFixed(2));
            formData.append('montant_total', total.toFixed(2));
            formData.append('cart', JSON.stringify(cart));
            
            fetch('ajax/valider_vente.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlertModal({
                        title: 'Vente validée !',
                        message: `Facture N° ${data.numero_facture} enregistrée avec succès`,
                        type: 'success',
                        icon: 'success'
                    }).then(() => {
                        // Ouvrir la facture dans un nouvel onglet
                        window.open('facture_impression.php?id=' + data.id_vente, '_blank');
                        // Réinitialiser le panier
                        cart = [];
                        updateCart();
                        document.getElementById('clientSelect').value = '';
                    });
                } else {
                    showAlertModal({
                        title: 'Erreur',
                        message: data.message,
                        type: 'danger',
                        icon: 'danger'
                    });
                }
            })
            .catch(e => {
                showAlertModal({
                    title: 'Erreur',
                    message: 'Erreur lors de la validation: ' + e,
                    type: 'danger',
                    icon: 'danger'
                });
            });
        }
    });
}
</script>

<?php include 'footer.php'; ?>
