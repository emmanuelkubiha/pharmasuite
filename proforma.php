<?php
/**
 * FACTURE PROFORMA - STORE SUITE
 * Génération de facture proforma (devis) sans enregistrement de vente
 */
require_once 'protection_pages.php';

// Récupérer les données du panier depuis la session ou POST
$cart_items = $_POST['cart_items'] ?? [];
$client_name = $_POST['client_name'] ?? 'Client';
$total = $_POST['total'] ?? 0;

if (empty($cart_items)) {
    die('Aucun article dans le panier');
}

// Générer un numéro de proforma temporaire
$numero_proforma = 'PRO-' . date('Ymd') . '-' . substr(uniqid(), -6);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture Proforma <?php echo $numero_proforma; ?></title>
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
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            background: linear-gradient(135deg, <?php echo $couleur_primaire; ?>, <?php echo $couleur_secondaire; ?>);
            color: white;
            padding: 30px;
        }
        
        .invoice-body {
            padding: 30px;
        }
        
        .proforma-badge {
            background: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5em;
            color: rgba(255, 193, 7, 0.1);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }
        
        .invoice-body {
            position: relative;
            z-index: 1;
        }
        
        .invoice-table th {
            background: <?php echo $couleur_primaire; ?>20;
            color: <?php echo $couleur_primaire; ?>;
            font-weight: 600;
        }
        
        .total-row {
            font-size: 1.2em;
            font-weight: bold;
            background: #ffc10720;
        }
        
        .validity-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
        }
        
        .footer-text {
            text-align: center;
            color: #666;
            font-size: 0.9em;
            padding: 20px;
            border-top: 2px solid #eee;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="watermark">PROFORMA</div>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><?php echo htmlspecialchars($config['nom_boutique']); ?></h1>
                    <p class="mb-0"><?php echo htmlspecialchars($config['adresse_boutique'] ?? ''); ?></p>
                    <p class="mb-0">
                        <?php if (!empty($config['telephone_boutique'])): ?>
                        Tél: <?php echo htmlspecialchars($config['telephone_boutique']); ?>
                        <?php endif; ?>
                        <?php if (!empty($config['email_boutique'])): ?>
                        | Email: <?php echo htmlspecialchars($config['email_boutique']); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="proforma-badge">PROFORMA</div>
                    <h4 class="mb-0"><?php echo $numero_proforma; ?></h4>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="invoice-body">
            <!-- Info blocks -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-block">
                        <h5 class="text-uppercase text-muted mb-2">Client</h5>
                        <strong><?php echo htmlspecialchars($client_name); ?></strong>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="info-block">
                        <h5 class="text-uppercase text-muted mb-2">Détails</h5>
                        <strong>Date:</strong> <?php echo date('d/m/Y à H:i'); ?><br>
                        <strong>Vendeur:</strong> <?php echo htmlspecialchars($user['nom_utilisateur']); ?><br>
                        <strong>Type:</strong> Facture Proforma (Devis)
                    </div>
                </div>
            </div>

            <!-- Validity Notice -->
            <div class="validity-notice">
                <strong>⚠ FACTURE PROFORMA - NON DÉFINITIVE</strong>
                <p class="mb-0 mt-2">Ce document est une estimation des coûts et n'a pas de valeur comptable. Les prix et disponibilités sont susceptibles de modification. Validité: 30 jours.</p>
            </div>

            <!-- Table -->
            <table class="table table-bordered invoice-table mt-4">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Produit</th>
                        <th width="100" class="text-center">Quantité</th>
                        <th width="120" class="text-end">Prix unitaire</th>
                        <th width="120" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $index = 1;
                    foreach ($cart_items as $item): 
                    ?>
                    <tr>
                        <td><?php echo $index++; ?></td>
                        <td><strong><?php echo htmlspecialchars($item['nom']); ?></strong></td>
                        <td class="text-center"><?php echo $item['quantite']; ?></td>
                        <td class="text-end"><?php echo number_format($item['prix'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                        <td class="text-end"><?php echo number_format($item['prix'] * $item['quantite'], 0, ',', ' '); ?> <?php echo $devise; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>TOTAL ESTIMÉ</strong></td>
                        <td class="text-end"><strong><?php echo number_format($total, 0, ',', ' '); ?> <?php echo $devise; ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Conditions -->
            <div class="mt-4">
                <h6><strong>Conditions générales :</strong></h6>
                <ul class="small text-muted">
                    <li>Cette facture proforma est valable 30 jours à compter de la date d'émission</li>
                    <li>Les prix peuvent varier selon la disponibilité des produits</li>
                    <li>Ce document n'est pas une facture définitive et ne constitue pas un engagement de vente</li>
                    <li>Pour confirmer la commande, veuillez contacter notre service commercial</li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="footer-text">
                <p class="mb-1"><strong>Pour toute question, contactez-nous !</strong></p>
                <p class="mb-0">Facture proforma générée par <?php echo htmlspecialchars($config['nom_boutique']); ?></p>
                <p class="mb-0 text-muted small">Imprimé le <?php echo date('d/m/Y à H:i:s'); ?></p>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="text-center my-4 no-print">
        <button onclick="window.print()" class="btn btn-warning btn-lg me-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/><rect x="7" y="13" width="10" height="8" rx="2"/></svg>
            Imprimer Proforma
        </button>
        <a href="vente.php" class="btn btn-outline-secondary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="5" y1="12" x2="19" y2="12"/><line x1="5" y1="12" x2="9" y2="16"/><line x1="5" y1="12" x2="9" y2="8"/></svg>
            Retour aux ventes
        </a>
        <button onclick="convertToSale()" class="btn btn-success btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 11 12 14 20 6"/><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/></svg>
            Convertir en vente définitive
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function convertToSale() {
            if (confirm('Voulez-vous enregistrer cette proforma comme vente définitive ?')) {
                // Rediriger vers vente.php avec les données pré-remplies
                alert('Fonctionnalité à implémenter : redirection vers vente.php avec panier pré-rempli');
                window.location.href = 'vente.php';
            }
        }
    </script>
</body>
</html>
