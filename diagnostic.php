<?php
/**
 * Script de diagnostic - À supprimer après utilisation
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostic de Configuration</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} table{border-collapse:collapse;width:100%;} td,th{border:1px solid #ddd;padding:8px;text-align:left;}</style>";

// Charger les fichiers
require_once __DIR__ . '/config/database.php';

echo "<h2>1️⃣ Connexion à la base de données</h2>";
try {
    global $pdo;
    if ($pdo) {
        echo "<p class='success'>✅ Connexion PDO active</p>";
    } else {
        echo "<p class='error'>❌ Pas de connexion PDO</p>";
        die();
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
    die();
}

echo "<h2>2️⃣ Contenu de la table configuration</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM configuration");
    $configs = $stmt->fetchAll();
    
    echo "<p>Nombre de lignes: <strong>" . count($configs) . "</strong></p>";
    
    if (count($configs) > 0) {
        echo "<table>";
        echo "<tr><th>Champ</th><th>Valeur</th><th>État</th></tr>";
        
        foreach ($configs as $config) {
            echo "<tr><td colspan='3'><strong>ID: " . $config['id_config'] . "</strong></td></tr>";
            
            $important_fields = ['nom_boutique', 'adresse', 'telephone', 'est_configure'];
            
            foreach ($important_fields as $field) {
                $value = $config[$field] ?? '';
                $isEmpty = empty($value);
                $status = $isEmpty ? "❌ Vide" : "✅ Rempli";
                $class = $isEmpty ? "error" : "success";
                
                echo "<tr>";
                echo "<td><strong>$field</strong></td>";
                echo "<td>" . htmlspecialchars($value) . "</td>";
                echo "<td class='$class'>$status</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Aucune ligne dans la table configuration</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>3️⃣ Test de la fonction is_system_configured()</h2>";
try {
    $isConfigured = is_system_configured();
    
    if ($isConfigured) {
        echo "<p class='success'>✅ Système détecté comme CONFIGURÉ</p>";
        echo "<p class='info'>→ Le système devrait rediriger vers login.php</p>";
    } else {
        echo "<p class='error'>❌ Système détecté comme NON CONFIGURÉ</p>";
        echo "<p class='info'>→ C'est pourquoi il redirige vers setup.php</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>4️⃣ Vérification détaillée</h2>";
try {
    $stmt = $pdo->query("SELECT est_configure, nom_boutique, adresse, telephone FROM configuration WHERE id_config = 1");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<table>";
        echo "<tr><th>Condition</th><th>Valeur</th><th>Résultat</th></tr>";
        
        $nom_ok = !empty($result['nom_boutique']);
        $adresse_ok = !empty($result['adresse']);
        $tel_ok = !empty($result['telephone']);
        $flag_ok = isset($result['est_configure']) && $result['est_configure'] == 1;
        
        echo "<tr><td>nom_boutique rempli</td><td>" . htmlspecialchars($result['nom_boutique']) . "</td><td class='" . ($nom_ok ? "success" : "error") . "'>" . ($nom_ok ? "✅" : "❌") . "</td></tr>";
        echo "<tr><td>adresse rempli</td><td>" . htmlspecialchars($result['adresse']) . "</td><td class='" . ($adresse_ok ? "success" : "error") . "'>" . ($adresse_ok ? "✅" : "❌") . "</td></tr>";
        echo "<tr><td>telephone rempli</td><td>" . htmlspecialchars($result['telephone']) . "</td><td class='" . ($tel_ok ? "success" : "error") . "'>" . ($tel_ok ? "✅" : "❌") . "</td></tr>";
        echo "<tr><td>est_configure = 1</td><td>" . ($result['est_configure'] ?? 'NULL') . "</td><td class='" . ($flag_ok ? "success" : "error") . "'>" . ($flag_ok ? "✅" : "❌") . "</td></tr>";
        
        echo "<tr style='background:#f0f0f0;'><td colspan='2'><strong>Résultat final</strong></td><td class='" . (($flag_ok || ($nom_ok && $adresse_ok && $tel_ok)) ? "success" : "error") . "'>";
        
        if ($flag_ok) {
            echo "✅ Flag est_configure = 1";
        } elseif ($nom_ok && $adresse_ok && $tel_ok) {
            echo "✅ Tous les champs requis remplis";
        } else {
            echo "❌ Conditions non remplies";
        }
        
        echo "</td></tr>";
        echo "</table>";
        
        if (!$flag_ok && $nom_ok && $adresse_ok && $tel_ok) {
            echo "<div style='background:#fff3cd;padding:15px;margin:20px 0;border-left:4px solid #ffc107;'>";
            echo "<strong>⚠️ Solution:</strong> Les données sont présentes mais est_configure n'est pas à 1.<br>";
            echo "La fonction is_system_configured() devrait automatiquement mettre à jour ce flag.<br>";
            echo "<a href='fix_config.php' style='display:inline-block;margin-top:10px;padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;'>🔧 Corriger automatiquement</a>";
            echo "</div>";
        }
        
    } else {
        echo "<p class='error'>❌ Aucune ligne avec id_config = 1</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<hr><p><a href='index.php'>→ Aller à index.php</a> | <a href='setup.php'>→ Aller à setup.php</a></p>";
?>
