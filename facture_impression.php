<?php
/**
 * PAGE IMPRESSION FACTURE - STORE SUITE
 * Génération et impression de facture de vente
 */
require_once 'protection_pages.php';

// Sécurisation : s'assurer que $config est bien défini (protection_pages.php doit le définir)
if (!isset($config) || !is_array($config) || empty($config['nom_boutique'])) {
    // Récupération manuelle si besoin
    if (function_exists('get_system_config')) {
        $config = get_system_config();
    }
    if (!$config || empty($config['nom_boutique'])) {
        die('Configuration système manquante. Contactez l\'administrateur.');
    }
    // Couleurs par défaut si non définies
    $couleur_primaire = $config['couleur_primaire'] ?? '#206bc4';
    $couleur_secondaire = $config['couleur_secondaire'] ?? '#ffffff';
    $devise = $config['devise'] ?? 'CDF';
}
// Sinon, les variables globales sont déjà définies par protection_pages.php

$id_vente = $_GET['id'] ?? 0;
$format = $_GET['format'] ?? 'thermal';
if (!in_array($format, ['thermal', 'a4'], true)) {
    $format = 'thermal';
}

// Récupérer les informations de la vente
$vente = db_fetch_one("
    SELECT 
        v.*,
        c.nom_client,
        c.telephone as client_tel,
        c.adresse as client_adresse,
        u.nom_complet as nom_vendeur
    FROM ventes v
    LEFT JOIN clients c ON v.id_client = c.id_client
    INNER JOIN utilisateurs u ON v.id_vendeur = u.id_utilisateur
    WHERE v.id_vente = ?
", [$id_vente]);

if (!$vente) {
    die('Facture introuvable');
}

// Récupérer les détails de la vente
$details = db_fetch_all("
    SELECT 
        vd.*, 
        p.dosage, p.date_peremption, p.conditionnement
    FROM details_vente vd
    LEFT JOIN produits p ON vd.id_produit = p.id_produit
    WHERE vd.id_vente = ?
    ORDER BY vd.id_detail
", [$id_vente]);

$page_title = 'Facture ' . $vente['numero_facture'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture <?php echo $vente['numero_facture']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
        
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .invoice-container {
            max-width: <?php echo $format === 'thermal' ? '360px' : '800px'; ?>;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .invoice-header {
            background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
            color: white;
            padding: <?php echo $format === 'thermal' ? '10px 8px' : '30px'; ?>;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .invoice-body {
            padding: <?php echo $format === 'thermal' ? '8px' : '30px'; ?>;
        }

        .info-block {
            margin-bottom: <?php echo $format === 'thermal' ? '12px' : '20px'; ?>;
            font-size: <?php echo $format === 'thermal' ? '8px' : '13px'; ?>;
        }

        .invoice-table th {
            background: <?php echo $couleur_primaire; ?>20;
            color: <?php echo $couleur_primaire; ?>;
            font-weight: 600;
            padding: <?php echo $format === 'thermal' ? '6px' : '10px'; ?>;
            font-size: <?php echo $format === 'thermal' ? '12px' : '14px'; ?>;
        }

        .invoice-table td {
            padding: <?php echo $format === 'thermal' ? '6px' : '10px'; ?>;
            font-size: <?php echo $format === 'thermal' ? '12px' : '14px'; ?>;
        }

        .total-row {
            font-size: <?php echo $format === 'thermal' ? '14px' : '1.2em'; ?>;
            font-weight: bold;
            background: <?php echo $couleur_primaire; ?>10;
        }

        .footer-text {
            text-align: center;
            color: #666;
            font-size: <?php echo $format === 'thermal' ? '11px' : '0.9em'; ?>;
            padding: <?php echo $format === 'thermal' ? '12px' : '20px'; ?>;
            border-top: 2px solid #eee;
            margin-top: <?php echo $format === 'thermal' ? '16px' : '30px'; ?>;
        }

        .badge-paid {
            background: #28a745;
            color: white;
            padding: <?php echo $format === 'thermal' ? '4px 10px' : '5px 15px'; ?>;
            border-radius: 20px;
            font-size: <?php echo $format === 'thermal' ? '11px' : '0.9em'; ?>;
        }

        .qr-code {
            width: <?php echo $format === 'thermal' ? '70px' : '100px'; ?>;
            height: <?php echo $format === 'thermal' ? '70px' : '100px'; ?>;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ddd;
        }

        <?php if ($format === 'thermal'): ?>
                .invoice-table th, .invoice-table td {
                    font-size: 7px;
                    padding: 2px 1px;
                }
                .invoice-table th:nth-child(1), .invoice-table td:nth-child(1) { width: 10px; }
                .invoice-table th:nth-child(2), .invoice-table td:nth-child(2) { width: 70px; }
                .invoice-table th:nth-child(3), .invoice-table td:nth-child(3) { width: 18px; text-align:center; }
                .invoice-table th:nth-child(4), .invoice-table td:nth-child(4) { width: 32px; text-align:right; }
                .invoice-table th:nth-child(5), .invoice-table td:nth-child(5) { width: 32px; text-align:right; }
        body {
            background: #fff;
        }
        .invoice-container {
            box-shadow: none;
            border: 1px dashed #ddd;
            max-width: 210px;
            margin: 0 auto;
            padding: 0 2px;
        }
        .invoice-header {
            flex-direction: row;
            align-items: flex-start;
            justify-content: flex-start;
            text-align: left;
            gap: 6px;
            padding: 6px 2px 4px 2px;
        }
        .logo-pharma { width: 28px; height: 28px; border-radius: 6px; margin: 0 4px 0 0; background: #fff; display:block; }
        .invoice-header h1, .invoice-header h2, .invoice-header h4 {
            text-align: left;
            margin-bottom: 2px;
        }
        .invoice-body, .info-block, .invoice-header, .footer-text {
            text-align: left !important;
        }
        .info-block {
            margin-bottom: 6px;
            font-size: 8px;
        }
                <?php if ($format === 'a4'): ?>
                .logo-pharma img, .logo-pharma svg {
                    max-width: 24px;
                    max-height: 24px;
                    width: 24px;
                    height: 24px;
                    margin: 0 auto 8px auto;
                    display: block;
                }
                .logo-pharma {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    margin-bottom: 8px;
                }
                <?php endif; ?>
        .invoice-header h1 { font-size: 11px; margin-bottom:2px; }
        .invoice-header h2 { font-size: 12px; margin-bottom:2px; }
        .invoice-header h4 { font-size: 10px; margin-bottom:2px; }
        .invoice-body { padding-bottom: 4px; }
        .invoice-table th, .invoice-table td { padding: 1px 0.5px; }
        .invoice-table th { font-size: 8px; }
        .invoice-table td { font-size: 8px; }
        /* Suppression du tronquage pour lisibilité */
        .total-row { font-size: 9px; background: #e7f3e7; font-weight: bold; }
        .footer-text { font-size: 7px; padding: 3px; }
        .invoice-table { table-layout: fixed; width: 100%; }
        <?php endif; ?>
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div style="display:flex;flex-direction:row;align-items:flex-start;justify-content:flex-start;text-align:left;gap:12px;">
                    <span class="logo-pharma">
                        <?php
                        $logoPath = null;
                        $tryPaths = [];
                        if (!empty($config['logo_boutique'])) {
                            $tryPaths[] = $config['logo_boutique'];
                            $tryPaths[] = 'uploads/logos/' . $config['logo_boutique'];
                            $tryPaths[] = 'images/' . $config['logo_boutique'];
                        }
                        $tryPaths[] = 'uploads/logos/logo.png';
                        $tryPaths[] = 'images/logo-pharma.png';
                        foreach ($tryPaths as $p) {
                            if (!empty($p) && file_exists($p) && is_file($p)) {
                                $logoPath = $p;
                                break;
                            }
                        }
                        ?>
                        <?php if ($logoPath): ?>
                            <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo pharmacie"
                            style="object-fit:contain;<?php if ($format === 'a4') { echo 'width:48px;height:48px;'; } else { echo 'width:32px;height:32px;'; } ?>" />
                        <?php else: ?>
                            <!-- Fallback SVG medical_services sans background -->
                            <svg class="material-symbols-outlined" width="32" height="32" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 14v20M14 24h20" stroke="#11d411" stroke-width="4" stroke-linecap="round"/></svg>
                        <?php endif; ?>
                    </span>
                <div style="text-align:center;">
                    <h1 class="mb-0"><?php echo htmlspecialchars($config['nom_boutique']); ?></h1>
                    <p class="mb-0" style="font-size:11px;"><?php echo htmlspecialchars($config['adresse'] ?? ''); ?></p>
                    <p class="mb-0" style="font-size:10px;">
                        <?php if (!empty($config['telephone'])): ?>
                        Tél: <?php echo htmlspecialchars($config['telephone']); ?>
                        <?php endif; ?>
                        <?php if (!empty($config['email'])): ?>
                        | Email: <?php echo htmlspecialchars($config['email']); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div style="text-align:right;">
                <h2 class="mb-0">FACTURE</h2>
                <h4 class="mb-0"><?php echo $vente['numero_facture']; ?></h4>
                <span class="badge-paid"><?php echo strtoupper($vente['statut']); ?></span>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- Info blocks -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-block">
                        <h5 class="text-uppercase text-muted mb-2">Client</h5>
                        <strong><?php echo htmlspecialchars($vente['nom_client'] ?? 'Vente au comptoir'); ?></strong>
                        <?php if (!empty($vente['client_tel'])): ?>
                        <br>Tél: <?php echo htmlspecialchars($vente['client_tel']); ?>
                        <?php endif; ?>
                        <?php if (!empty($vente['client_adresse'])): ?>
                        <br><?php echo htmlspecialchars($vente['client_adresse']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="info-block">
                        <h5 class="text-uppercase text-muted mb-2">Détails</h5>
                        <strong>Date:</strong> <?php echo date('d/m/Y à H:i', strtotime($vente['date_vente'])); ?><br>
                        <strong>Vendeur:</strong> <?php echo htmlspecialchars($vente['nom_vendeur']); ?><br>
                        <strong>Mode:</strong> <?php echo ucfirst($vente['mode_paiement']); ?>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <table class="table table-bordered invoice-table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Médicament / Dosage / Cond.</th>
                        <th width="60" class="text-center">Qté</th>
                        <th width="80" class="text-end">PU</th>
                        <th width="80" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $index => $detail): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td style="word-break:break-word;white-space:normal;line-height:1.2;">
                            <strong><?php echo htmlspecialchars($detail['nom_produit']); ?></strong>
                            <?php if (!empty($detail['dosage']) || !empty($detail['conditionnement'])): ?>
                                <span style="font-size:<?php echo $format === 'thermal' ? '7px' : '13px'; ?>;color:#555;"> <?php if (!empty($detail['dosage'])): ?><?php echo htmlspecialchars($detail['dosage']); ?><?php endif; ?><?php if (!empty($detail['dosage']) && !empty($detail['conditionnement'])): ?> | <?php endif; ?><?php if (!empty($detail['conditionnement'])): ?><?php echo htmlspecialchars($detail['conditionnement']); ?><?php endif; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo $detail['quantite']; ?></td>
                        <td class="text-end"><?php echo number_format($detail['prix_unitaire'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                        <td class="text-end"><?php echo number_format($detail['prix_total'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>Total HT</strong></td>
                        <td class="text-end"><strong><?php echo number_format($vente['montant_ht'], 0, ',', ' '); ?> <?php echo $devise; ?></strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>TVA (16%)</strong></td>
                        <td class="text-end"><strong><?php echo number_format($vente['montant_tva'], 0, ',', ' '); ?> <?php echo $devise; ?></strong></td>
                    </tr>
                    <?php if (!empty($vente['montant_remise']) && $vente['montant_remise'] > 0): ?>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>Remise</strong></td>
                        <td class="text-end"><strong>-<?php echo number_format($vente['montant_remise'], 0, ',', ' '); ?> <?php echo $devise; ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="total-row" style="background:<?php echo $couleur_primaire; ?>22;">
                        <td colspan="4" class="text-end"><strong>TOTAL À PAYER</strong></td>
                        <td class="text-end"><strong><?php echo number_format($vente['montant_total'], 0, ',', ' '); ?> <?php echo $devise; ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Notes -->
            <?php if (!empty($vente['notes'])): ?>
            <div class="mt-4">
                <strong>Notes:</strong>
                <p><?php echo nl2br(htmlspecialchars($vente['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="footer-text">
                <p class="mb-1"><strong>Merci pour votre confiance !</strong></p>
                <p class="mb-0">Cette facture a été générée électroniquement par <?php echo htmlspecialchars($config['nom_boutique']); ?></p>
                <p class="mb-0 text-muted small">Imprimé le <?php echo date('d/m/Y à H:i:s'); ?></p>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="text-center my-4 no-print">
        <div class="mb-2">
            <div class="btn-group" role="group" aria-label="Choix format">
                <a href="facture_impression.php?id=<?php echo $id_vente; ?>&format=thermal" class="btn btn-sm <?php echo $format === 'thermal' ? 'btn-primary' : 'btn-outline-primary'; ?>">Thermique (80mm)</a>
                <a href="facture_impression.php?id=<?php echo $id_vente; ?>&format=a4" class="btn btn-sm <?php echo $format === 'a4' ? 'btn-primary' : 'btn-outline-primary'; ?>">A4</a>
            </div>
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-lg me-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/><rect x="7" y="13" width="10" height="8" rx="2"/></svg>
            Imprimer
        </button>
        <a href="listes.php?page=ventes" class="btn btn-outline-secondary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12"/><line x1="5" y1="12" x2="9" y2="16"/><line x1="5" y1="12" x2="9" y2="8"/></svg>
            Retour aux ventes
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
