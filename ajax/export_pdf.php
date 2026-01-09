<?php
/**
 * EXPORT PDF - STORE SUITE
 * Export des rapports au format PDF (version HTML)
 */

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../protection_pages.php';
} catch (Exception $e) {
    die('<html><body><h1>Erreur de chargement</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>');
}

// Type de rapport
$type = $_GET['type'] ?? '';
$date_debut = $_GET['date_debut'] ?? date('Y-m-d');
$date_fin = $_GET['date_fin'] ?? date('Y-m-d');

// Vérifier que le type est valide
$types_valides = ['produits', 'ventes', 'benefices', 'categories', 'stock'];
if (empty($type) || !in_array($type, $types_valides)) {
    die('<html><body><h1>Erreur</h1><p>Type de rapport invalide ou non spécifié.</p><p>Types valides : ' . implode(', ', $types_valides) . '</p></body></html>');
}

// Headers pour affichage HTML (pas de vraie génération PDF sans bibliothèque)
header('Content-Type: text/html; charset=UTF-8');

// Génération du contenu HTML
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #206bc4; margin: 0; }
        .header img.logo { max-width: 150px; max-height: 80px; margin-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #206bc4; color: white; font-weight: bold; }
        .total { background-color: #f0f0f0; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #666; }
        
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="header">
    <?php if (!empty($config['logo_boutique']) && file_exists(__DIR__ . '/../uploads/logos/' . $config['logo_boutique'])): ?>
        <img src="../uploads/logos/<?php echo htmlspecialchars($config['logo_boutique']); ?>" alt="Logo" class="logo">
    <?php endif; ?>
    <h1><?php echo htmlspecialchars($config['nom_boutique']); ?></h1>
    <p><?php echo htmlspecialchars($config['adresse_boutique'] ?? ''); ?></p>
    <p>Tél: <?php echo htmlspecialchars($config['telephone_boutique'] ?? ''); ?></p>
</div>

<div class="info">
    <strong>Date du rapport :</strong> <?php echo date('d/m/Y à H:i'); ?><br>
    <strong>Période :</strong> du <?php echo date('d/m/Y', strtotime($date_debut)); ?> au <?php echo date('d/m/Y', strtotime($date_fin)); ?>
</div>

<?php
switch ($type) {
    case 'produits':
        $produits = db_fetch_all("
            SELECT 
                p.nom_produit,
                c.nom_categorie,
                p.prix_achat,
                p.prix_vente,
                p.quantite_stock,
                p.seuil_alerte
            FROM produits p
            LEFT JOIN categories c ON p.id_categorie = c.id_categorie
            WHERE p.est_actif = 1
            ORDER BY p.nom_produit
        ");
        ?>
        <h2>Liste des Produits</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix Vente</th>
                    <th>Stock</th>
                    <th>Seuil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nom_produit']); ?></td>
                    <td><?php echo htmlspecialchars($p['nom_categorie'] ?? 'Sans catégorie'); ?></td>
                    <td><?php echo number_format($p['prix_vente'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                    <td><?php echo $p['quantite_stock']; ?></td>
                    <td><?php echo $p['seuil_alerte']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        break;

    case 'ventes':
        $ventes = db_fetch_all("
            SELECT 
                v.numero_facture,
                v.date_vente,
                c.nom_client,
                u.nom_utilisateur,
                v.montant_total
            FROM ventes v
            LEFT JOIN clients c ON v.id_client = c.id_client
            INNER JOIN utilisateurs u ON v.id_utilisateur = u.id_utilisateur
            WHERE DATE(v.date_vente) BETWEEN ? AND ?
            AND v.statut = 'validee'
            ORDER BY v.date_vente DESC
        ", [$date_debut, $date_fin]);
        
        $total = array_sum(array_column($ventes, 'montant_total'));
        ?>
        <h2>Rapport des Ventes</h2>
        <table>
            <thead>
                <tr>
                    <th>Facture</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventes as $v): ?>
                <tr>
                    <td><?php echo htmlspecialchars($v['numero_facture']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($v['date_vente'])); ?></td>
                    <td><?php echo htmlspecialchars($v['nom_client'] ?? 'Comptoir'); ?></td>
                    <td><?php echo htmlspecialchars($v['nom_utilisateur']); ?></td>
                    <td><?php echo number_format($v['montant_total'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total">
                    <td colspan="4">TOTAL</td>
                    <td><?php echo number_format($total, 0, ',', ' '); ?> <?php echo $devise; ?></td>
                </tr>
            </tbody>
        </table>
        <?php
        break;

    case 'benefices':
        $benefices = db_fetch_all("
            SELECT 
                p.nom_produit,
                p.prix_achat,
                SUM(vd.quantite) as quantite_vendue,
                SUM(vd.prix_unitaire * vd.quantite) as ca,
                SUM((vd.prix_unitaire - p.prix_achat) * vd.quantite) as benefice
            FROM ventes_details vd
            INNER JOIN produits p ON vd.id_produit = p.id_produit
            INNER JOIN ventes v ON vd.id_vente = v.id_vente
            WHERE DATE(v.date_vente) BETWEEN ? AND ?
            AND v.statut = 'validee'
            AND p.prix_achat > 0
            GROUP BY p.id_produit
            ORDER BY benefice DESC
        ", [$date_debut, $date_fin]);
        
        $total_ca = array_sum(array_column($benefices, 'ca'));
        $total_benefice = array_sum(array_column($benefices, 'benefice'));
        $marge = $total_ca > 0 ? ($total_benefice / $total_ca * 100) : 0;
        ?>
        <h2>Rapport des Bénéfices</h2>
        <div class="info">
            <strong>Chiffre d'affaires :</strong> <?php echo number_format($total_ca, 0, ',', ' '); ?> <?php echo $devise; ?><br>
            <strong>Bénéfice total :</strong> <?php echo number_format($total_benefice, 0, ',', ' '); ?> <?php echo $devise; ?><br>
            <strong>Marge globale :</strong> <?php echo number_format($marge, 2); ?> %
        </div>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Qté Vendue</th>
                    <th>CA</th>
                    <th>Bénéfice</th>
                    <th>Marge</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($benefices as $b): 
                    $marge_produit = $b['ca'] > 0 ? ($b['benefice'] / $b['ca'] * 100) : 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['nom_produit']); ?></td>
                    <td><?php echo $b['quantite_vendue']; ?></td>
                    <td><?php echo number_format($b['ca'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                    <td><?php echo number_format($b['benefice'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                    <td><?php echo number_format($marge_produit, 2); ?> %</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        break;

    default:
        echo '<p>Type de rapport non reconnu.</p>';
        break;
}
?>

<div class="footer">
    Généré par <?php echo htmlspecialchars($config['nom_boutique']); ?> - <?php echo date('d/m/Y à H:i:s'); ?>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// IMPORTANT : Cette version affiche en HTML uniquement
// Pour une vraie génération PDF, installez une bibliothèque comme mPDF, TCPDF ou Dompdf
// Exemple avec mPDF :
// composer require mpdf/mpdf
// $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
// $mpdf->WriteHTML($html);
// $mpdf->Output('rapport_' . $type . '_' . date('Y-m-d') . '.pdf', 'I');

echo '<div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; background: #ffc107; color: #000; padding: 10px; text-align: center; z-index: 9999; border-bottom: 2px solid #ff9800;">';
echo '<strong>⚠️ ATTENTION :</strong> Affichage HTML uniquement. Pour générer un vrai PDF, installez mPDF (composer require mpdf/mpdf). ';
echo '<button onclick="window.print()" style="margin-left: 10px; padding: 5px 15px; background: #fff; border: 1px solid #333; cursor: pointer;">Imprimer en PDF</button>';
echo '</div>';
echo '<div style="margin-top: 60px;">' . $html . '</div>';
?>
