<?php
/**
 * AJAX endpoint - Get report content for modal display
 */
require_once __DIR__ . '/../protection_pages.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'html' => '', 'title' => ''];

try {
    if (empty($_POST['type'])) throw new Exception('Type de rapport manquant');
    
    $type = $_POST['type'];
    $date_debut = $_POST['date_debut'] ?? date('Y-m-d');
    $date_fin = $_POST['date_fin'] ?? date('Y-m-d');
    
    switch ($type) {
        case 'ventes':
            $response['title'] = 'Rapport Ventes';
            
            // Get ventes data
            $ventes = db_fetch_all("
                SELECT 
                    v.id_vente,
                    v.date_vente,
                    c.nom_client,
                    COUNT(dv.id_detail) as nombre_articles,
                    v.montant_total
                FROM ventes v
                LEFT JOIN clients c ON v.id_client = c.id_client
                LEFT JOIN details_vente dv ON v.id_vente = dv.id_vente
                WHERE DATE(v.date_vente) BETWEEN ? AND ?
                AND v.statut = 'validee'
                GROUP BY v.id_vente
                ORDER BY v.date_vente DESC
                LIMIT 50
            ", [$date_debut, $date_fin]);
            
            $html = '<table class="table table-sm table-hover">';
            $html .= '<thead><tr><th>ID</th><th>Date</th><th>Client</th><th>Articles</th><th class="text-end">Total</th></tr></thead><tbody>';
            
            foreach ($ventes as $v) {
                $html .= '<tr>';
                $html .= '<td><strong>#' . $v['id_vente'] . '</strong></td>';
                $html .= '<td>' . date('d/m/Y', strtotime($v['date_vente'])) . '</td>';
                $html .= '<td>' . e($v['nom_client'] ?? 'Client anonyme') . '</td>';
                $html .= '<td><span class="badge bg-info">' . $v['nombre_articles'] . '</span></td>';
                $html .= '<td class="text-end"><strong>' . format_montant($v['montant_total']) . '</strong></td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            
            if (empty($ventes)) {
                $html = '<div class="alert alert-info">Aucune vente enregistrée pour cette période</div>';
            }
            
            $response['html'] = $html;
            $response['success'] = true;
            break;
            
        case 'produits':
            $response['title'] = 'Inventaire Produits';
            
            // Inventaire complet, même sans ventes dans la période
            $produits = db_fetch_all("
                SELECT 
                    p.id_produit,
                    p.nom_produit,
                    p.prix_vente,
                    p.quantite_stock,
                    p.seuil_alerte,
                    p.seuil_critique,
                    p.est_actif,
                    cat.nom_categorie,
                    COALESCE(SUM(CASE WHEN v.statut = 'validee' AND DATE(v.date_vente) BETWEEN ? AND ? THEN dv.quantite ELSE 0 END), 0) AS quantite_vendue,
                    COALESCE(SUM(CASE WHEN v.statut = 'validee' AND DATE(v.date_vente) BETWEEN ? AND ? THEN dv.prix_total ELSE 0 END), 0) AS montant_total
                FROM produits p
                LEFT JOIN categories cat ON p.id_categorie = cat.id_categorie
                LEFT JOIN details_vente dv ON p.id_produit = dv.id_produit
                LEFT JOIN ventes v ON dv.id_vente = v.id_vente
                GROUP BY p.id_produit
                ORDER BY p.nom_produit ASC
                LIMIT 200
            ", [$date_debut, $date_fin, $date_debut, $date_fin]);
            
            $html = '<table class="table table-sm table-hover">';
            $html .= '<thead><tr><th>Produit</th><th>Catégorie</th><th class="text-end">Prix vente</th><th class="text-center">Stock</th><th class="text-center">Vendu</th><th class="text-end">Montant ventes</th></tr></thead><tbody>';
            
            foreach ($produits as $p) {
                $statut_class = $p['est_actif'] ? 'bg-success-lt' : 'bg-secondary-lt';
                $html .= '<tr>';
                $html .= '<td><strong>' . e($p['nom_produit']) . '</strong><br><small class="text-muted">#' . $p['id_produit'] . '</small></td>';
                $html .= '<td><span class="badge bg-purple-lt text-dark">' . e($p['nom_categorie'] ?? 'Non classé') . '</span></td>';
                $html .= '<td class="text-end">' . format_montant($p['prix_vente']) . '</td>';
                $html .= '<td class="text-center"><span class="badge ' . ($p['quantite_stock'] <= $p['seuil_critique'] ? 'bg-danger' : ($p['quantite_stock'] <= $p['seuil_alerte'] ? 'bg-warning' : 'bg-success')) . '">' . ($p['quantite_stock'] ?? 0) . '</span></td>';
                $html .= '<td class="text-center"><span class="badge bg-primary">' . ($p['quantite_vendue'] ?? 0) . '</span></td>';
                $html .= '<td class="text-end"><strong>' . format_montant($p['montant_total'] ?? 0) . '</strong></td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            
            if (empty($produits)) {
                $html = '<div class="alert alert-info">Aucun produit trouvé</div>';
            }
            
            $response['html'] = $html;
            $response['success'] = true;
            break;
            
        case 'benefices':
            // Vérification admin
            if (!$is_admin) {
                throw new Exception('Accès refusé. Seuls les administrateurs peuvent consulter ce rapport.');
            }
            
            $response['title'] = 'Performance Financière';
            
            // Get benefices data
            $stats = db_fetch_one("
                SELECT 
                    COUNT(*) as total_ventes,
                    SUM(montant_total) as chiffre_affaires,
                    SUM(montant_ht) as montant_ht,
                    SUM(montant_tva) as montant_tva,
                    AVG(montant_total) as panier_moyen
                FROM ventes
                WHERE DATE(date_vente) BETWEEN ? AND ?
                AND statut = 'validee'
            ", [$date_debut, $date_fin]);
            
            $html = '<div class="row g-2">';
            $html .= '<div class="col-md-6"><div class="bg-light p-3 rounded"><small class="text-muted">Chiffre d\'affaires TTC</small><h4>' . format_montant($stats['chiffre_affaires'] ?? 0) . '</h4></div></div>';
            $html .= '<div class="col-md-6"><div class="bg-light p-3 rounded"><small class="text-muted">Montant HT</small><h4>' . format_montant($stats['montant_ht'] ?? 0) . '</h4></div></div>';
            $html .= '<div class="col-md-6"><div class="bg-light p-3 rounded"><small class="text-muted">TVA (16%)</small><h4>' . format_montant($stats['montant_tva'] ?? 0) . '</h4></div></div>';
            $html .= '<div class="col-md-6"><div class="bg-light p-3 rounded"><small class="text-muted">Panier moyen</small><h4>' . format_montant($stats['panier_moyen'] ?? 0) . '</h4></div></div>';
            $html .= '</div>';
            
            $response['html'] = $html;
            $response['success'] = true;
            break;
            
        case 'categories':
            $response['title'] = 'Performance Catégories';
            
            // Get categories data
            $categories = db_fetch_all("
                SELECT 
                    cat.id_categorie,
                    cat.nom_categorie,
                    COUNT(DISTINCT dv.id_vente) as nombre_ventes,
                    SUM(dv.quantite) as quantite_vendue,
                    SUM(dv.prix_total) as montant_total,
                    COUNT(DISTINCT p.id_produit) as nombre_produits
                FROM details_vente dv
                INNER JOIN produits p ON dv.id_produit = p.id_produit
                INNER JOIN categories cat ON p.id_categorie = cat.id_categorie
                INNER JOIN ventes v ON dv.id_vente = v.id_vente
                WHERE DATE(v.date_vente) BETWEEN ? AND ?
                AND v.statut = 'validee'
                GROUP BY cat.id_categorie
                ORDER BY montant_total DESC
            ", [$date_debut, $date_fin]);
            
            $html = '<table class="table table-sm table-hover">';
            $html .= '<thead><tr><th>Catégorie</th><th class="text-center">Produits</th><th class="text-center">Quantité</th><th class="text-end">Montant</th></tr></thead><tbody>';
            
            foreach ($categories as $c) {
                $html .= '<tr>';
                $html .= '<td><strong>' . e($c['nom_categorie']) . '</strong></td>';
                $html .= '<td class="text-center"><span class="badge bg-success">' . $c['nombre_produits'] . '</span></td>';
                $html .= '<td class="text-center"><span class="badge bg-primary">' . $c['quantite_vendue'] . '</span></td>';
                $html .= '<td class="text-end"><strong>' . format_montant($c['montant_total']) . '</strong></td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            
            if (empty($categories)) {
                $html = '<div class="alert alert-info">Aucune vente par catégorie pour cette période</div>';
            }
            
            $response['html'] = $html;
            $response['success'] = true;
            break;
            
        default:
            throw new Exception('Type de rapport non reconnu');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
