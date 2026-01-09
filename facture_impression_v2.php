<?php
/**
 * PAGE D'IMPRESSION FACTURE - STORE SUITE
 * Affichage et impression des factures avec TVA 16%
 */
require_once 'protection_pages.php';

try {
    if (empty($_GET['id'])) {
        throw new Exception('Facture non spécifiée');
    }
    
    $id_vente = intval($_GET['id']);
    
    // Récupérer les infos vente
    $vente = db_fetch_one("
        SELECT v.*,
               c.nom_client, c.telephone, c.email, c.adresse,
               u.nom_complet as vendeur
        FROM ventes v
        LEFT JOIN clients c ON v.id_client = c.id_client
        LEFT JOIN utilisateurs u ON v.id_vendeur = u.id_utilisateur
        WHERE v.id_vente = ?
    ", [$id_vente]);
    
    if (!$vente) {
        throw new Exception('Facture non trouvée');
    }
    
    // Récupérer les détails
    $details = db_fetch_all("
        SELECT d.*, p.nom_produit, p.code_barre, p.unite_mesure
        FROM details_vente d
        JOIN produits p ON d.id_produit = p.id_produit
        WHERE d.id_vente = ?
        ORDER BY d.id_detail
    ", [$id_vente]);
    
} catch (Exception $e) {
    die('<div class="alert alert-danger p-5"><h3>Erreur</h3>' . e($e->getMessage()) . '</div>');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture <?php echo e($vente['numero_facture']); ?> - <?php echo e($nom_boutique); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 3px solid <?php echo $couleur_primaire; ?>;
            padding-bottom: 30px;
        }
        
        .boutique-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: <?php echo $couleur_primaire; ?>;
        }
        
        .boutique-info img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        
        .facture-info {
            text-align: right;
        }
        
        .facture-info .numero {
            font-size: 24px;
            font-weight: bold;
            color: <?php echo $couleur_primaire; ?>;
            margin-bottom: 10px;
        }
        
        .facture-info .date {
            font-size: 14px;
            color: #666;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 8px;
            margin-top: 25px;
        }
        
        .client-info, .boutique-details {
            display: flex;
            gap: 60px;
            margin-bottom: 40px;
        }
        
        .info-block {
            flex: 1;
        }
        
        .info-block h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .info-block p {
            font-size: 14px;
            line-height: 1.6;
            color: #666;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table thead {
            background: <?php echo $couleur_primaire; ?>;
            color: white;
        }
        
        .items-table th {
            padding: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            border: none;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .items-table tbody tr:hover {
            background: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid <?php echo $couleur_primaire; ?>;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .totals-row.total-ttc {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            padding: 15px 0;
            margin: 15px 0;
            color: <?php echo $couleur_primaire; ?>;
        }
        
        .totals-label {
            color: #666;
        }
        
        .totals-value {
            font-weight: 600;
            color: #333;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: <?php echo $couleur_primaire; ?>;
            color: white;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            opacity: 0.9;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            
            .btn-group {
                display: none;
            }
            
            .footer {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }
            
            .facture-info {
                text-align: left;
            }
            
            .client-info, .boutique-details {
                flex-direction: column;
                gap: 30px;
            }
            
            .items-table {
                font-size: 12px;
            }
            
            .items-table th, .items-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Boutons action -->
        <div class="btn-group">
            <button class="btn btn-primary" onclick="window.print()">🖨️ Imprimer</button>
            <button class="btn btn-secondary" onclick="window.close()">Fermer</button>
        </div>
        
        <!-- En-tête -->
        <div class="invoice-header">
            <div class="boutique-info">
                <?php if (!empty($logo_boutique)): ?>
                <img src="uploads/logos/<?php echo e($logo_boutique); ?>" alt="<?php echo e($nom_boutique); ?>">
                <?php endif; ?>
                <h1><?php echo e($nom_boutique); ?></h1>
            </div>
            <div class="facture-info">
                <div class="numero">Facture #<?php echo e($vente['numero_facture']); ?></div>
                <div class="date">
                    <strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?>
                </div>
            </div>
        </div>
        
        <!-- Infos client et vendeur -->
        <div class="client-info">
            <div class="info-block">
                <h3>Facturé à</h3>
                <p>
                    <?php if ($vente['nom_client']): ?>
                        <strong><?php echo e($vente['nom_client']); ?></strong><br>
                        <?php if ($vente['adresse']): ?>
                            <?php echo e($vente['adresse']); ?><br>
                        <?php endif; ?>
                        <?php if ($vente['telephone']): ?>
                            Tél: <?php echo e($vente['telephone']); ?><br>
                        <?php endif; ?>
                        <?php if ($vente['email']): ?>
                            <?php echo e($vente['email']); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        Vente comptoir
                    <?php endif; ?>
                </p>
            </div>
            <div class="info-block">
                <h3>Vendeur</h3>
                <p><?php echo e($vente['vendeur']); ?></p>
                <h3 style="margin-top: 15px;">Mode de paiement</h3>
                <p>
                    <?php
                    $modes = ['especes' => 'Espèces', 'carte' => 'Carte', 'mobile_money' => 'Mobile Money', 'cheque' => 'Chèque', 'credit' => 'Crédit'];
                    echo $modes[$vente['mode_paiement']] ?? ucfirst($vente['mode_paiement']);
                    ?>
                </p>
            </div>
        </div>
        
        <!-- Articles -->
        <div class="section-title">Détail des articles</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Produit</th>
                    <th class="text-center" style="width: 10%;">Quantité</th>
                    <th class="text-right" style="width: 15%;">P.U.</th>
                    <th class="text-right" style="width: 15%;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $detail): ?>
                <tr>
                    <td>
                        <strong><?php echo e($detail['nom_produit']); ?></strong>
                        <?php if (!empty($detail['code_barre'])): ?>
                        <br><small style="color: #999;">Code: <?php echo e($detail['code_barre']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $detail['quantite']; ?> <?php echo e($detail['unite_mesure'] ?? 'pièce'); ?>
                    </td>
                    <td class="text-right">
                        <?php echo format_montant($detail['prix_unitaire'], $devise); ?>
                    </td>
                    <td class="text-right">
                        <strong><?php echo format_montant($detail['quantite'] * $detail['prix_unitaire'], $devise); ?></strong>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Totaux -->
        <div class="totals">
            <div class="totals-row">
                <span class="totals-label">Sous-total HT:</span>
                <span class="totals-value"><?php echo format_montant($vente['montant_ht'], $devise); ?></span>
            </div>
            <div class="totals-row">
                <span class="totals-label">TVA (16%):</span>
                <span class="totals-value"><?php echo format_montant($vente['montant_tva'], $devise); ?></span>
            </div>
            <?php if ($vente['montant_remise'] > 0): ?>
            <div class="totals-row">
                <span class="totals-label">Remise:</span>
                <span class="totals-value">-<?php echo format_montant($vente['montant_remise'], $devise); ?></span>
            </div>
            <?php endif; ?>
            <div class="totals-row total-ttc">
                <span>MONTANT TOTAL TTC:</span>
                <span><?php echo format_montant($vente['montant_total'], $devise); ?></span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Merci d'avoir fait confiance à <?php echo e($nom_boutique); ?> | Facture générée le <?php echo date('d/m/Y à H:i'); ?></p>
        </div>
    </div>
</body>
</html>
