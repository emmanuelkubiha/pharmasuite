<?php
/**
 * GÉNÉRATION DE MODÈLE EXCEL - PHARMA SUITE
 * Génère un fichier Excel modèle pour l'import de données
 */

require_once __DIR__ . '/../protection_pages.php';

$type = $_GET['type'] ?? 'produits';

// Headers pour téléchargement CSV (compatible Excel)
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="modele_' . $type . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM UTF-8 pour Excel
echo "\xEF\xBB\xBF";

// Générer le modèle selon le type
switch ($type) {
    case 'produits':
        echo "nom_produit;code_barre;categorie;prix_achat;prix_vente;quantite_stock;seuil_alerte\n";
        echo "Paracétamol 500mg;3700123456789;Médicaments;100;150;50;10\n";
        echo "Amoxicilline 1g;3700987654321;Antibiotiques;300;450;30;5\n";
        echo "Vitamine C 1000mg;3700111222333;Vitamines;50;80;100;20\n";
        break;
        
    case 'clients':
        echo "nom_client;telephone;email;adresse\n";
        echo "Jean Dupont;+243999123456;jean@example.com;123 Av Lumumba, Kinshasa\n";
        echo "Marie Kabila;+243998765432;marie@example.com;456 Av Mobutu, Lubumbashi\n";
        break;
        
    case 'fournisseurs':
        echo "nom_fournisseur;telephone;email;adresse\n";
        echo "Pharma Distribution;+243997111222;contact@pharmadist.cd;Zone Industrielle, Kinshasa\n";
        echo "Medico Supply;+243996333444;info@medicosupply.cd;Av Commerce, Lubumbashi\n";
        break;
        
    default:
        echo "type_invalide\n";
}
