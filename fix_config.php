<?php
/**
 * Script pour corriger automatiquement la configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

echo "<h1>Correction de la configuration</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;padding:15px;background:#d4edda;border-radius:5px;margin:10px 0;} .error{color:red;padding:15px;background:#f8d7da;border-radius:5px;margin:10px 0;}</style>";

try {
    global $pdo;
    
    // Vérifier les données actuelles
    $stmt = $pdo->query("SELECT * FROM configuration WHERE id_config = 1");
    $config = $stmt->fetch();
    
    if (!$config) {
        echo "<div class='error'>❌ Aucune configuration trouvée avec id_config = 1</div>";
        die();
    }
    
    // Vérifier si les champs requis sont remplis
    $nom_ok = !empty($config['nom_boutique']);
    $adresse_ok = !empty($config['adresse']);
    $tel_ok = !empty($config['telephone']);
    
    if ($nom_ok && $adresse_ok && $tel_ok) {
        // Mettre à jour est_configure à 1
        $pdo->exec("UPDATE configuration SET est_configure = 1 WHERE id_config = 1");
        
        echo "<div class='success'>";
        echo "<h2>✅ Configuration corrigée avec succès !</h2>";
        echo "<p>Le flag <strong>est_configure</strong> a été mis à 1.</p>";
        echo "<p>Nom boutique: <strong>" . htmlspecialchars($config['nom_boutique']) . "</strong></p>";
        echo "<p>Adresse: <strong>" . htmlspecialchars($config['adresse']) . "</strong></p>";
        echo "<p>Téléphone: <strong>" . htmlspecialchars($config['telephone']) . "</strong></p>";
        echo "</div>";
        
        echo "<p style='margin-top:30px;'><a href='index.php' style='display:inline-block;padding:15px 30px;background:#28a745;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>🚀 Lancer le système</a></p>";
        
    } else {
        echo "<div class='error'>";
        echo "<h2>❌ Impossible de corriger</h2>";
        echo "<p>Les champs obligatoires ne sont pas tous remplis :</p>";
        echo "<ul>";
        echo "<li>Nom boutique: " . ($nom_ok ? "✅" : "❌") . "</li>";
        echo "<li>Adresse: " . ($adresse_ok ? "✅" : "❌") . "</li>";
        echo "<li>Téléphone: " . ($tel_ok ? "✅" : "❌") . "</li>";
        echo "</ul>";
        echo "<p>Veuillez compléter la configuration via setup.php</p>";
        echo "</div>";
        
        echo "<p style='margin-top:30px;'><a href='setup.php' style='display:inline-block;padding:15px 30px;background:#007bff;color:white;text-decoration:none;border-radius:5px;font-weight:bold;'>📝 Aller à la configuration</a></p>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erreur: " . $e->getMessage() . "</div>";
}

echo "<hr><p><a href='diagnostic.php'>← Retour au diagnostic</a></p>";
?>
