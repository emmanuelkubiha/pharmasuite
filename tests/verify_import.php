<?php
require_once 'config/database.php';

header('Content-Type: text/plain');

echo "✅ VÉRIFICATION IMPORT LOBIKO V2\n";
echo str_repeat('=', 60) . "\n";

// Produits avec dosage rempli
$with_dosage = db_fetch_one('SELECT COUNT(*) as nb FROM produits WHERE dosage IS NOT NULL AND dosage != ""', []);
echo "✓ Produits avec DOSAGE rempli: " . $with_dosage['nb'] . "\n";

// Produits avec conditionnement rempli
$with_cond = db_fetch_one('SELECT COUNT(*) as nb FROM produits WHERE conditionnement IS NOT NULL AND conditionnement != ""', []);
echo "✓ Produits avec CONDITIONNEMENT rempli: " . $with_cond['nb'] . "\n";

// Produits avec description remplie
$with_desc = db_fetch_one('SELECT COUNT(*) as nb FROM produits WHERE description IS NOT NULL AND description != ""', []);
echo "✓ Produits avec DESCRIPTION remplie: " . $with_desc['nb'] . "\n";

// Stock par dépôt
$depot_stock = db_fetch_one('SELECT COUNT(*) as nb FROM stock_par_depot WHERE id_depot = 1', []);
echo "✓ Produits au dépôt principal (ID 1): " . $depot_stock['nb'] . "\n";

echo "\n📦 EXEMPLES PRODUITS IMPORTÉS:\n";
$samples = db_fetch_all('SELECT nom_produit, dosage, conditionnement, prix_vente, description FROM produits LIMIT 5', []);
foreach ($samples as $p) {
    echo "\n  - " . $p['nom_produit'] . "\n";
    echo "    Dosage: " . ($p['dosage'] ?: '(vide)') . "\n";
    echo "    Conditionnement: " . ($p['conditionnement'] ?: '(vide)') . "\n";
    echo "    Prix: " . $p['prix_vente'] . " USD\n";
    echo "    Description: " . substr($p['description'], 0, 100) . "\n";
}
