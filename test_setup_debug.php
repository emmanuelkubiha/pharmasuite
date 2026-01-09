<?php
/**
 * Script de débogage pour la configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de configuration</h1>";

// Test 1: Inclusion des fichiers
echo "<h2>1. Test d'inclusion des fichiers</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "✅ Fichiers chargés correctement<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: Connexion à la base de données
echo "<h2>2. Test de connexion à la base de données</h2>";
try {
    global $pdo;
    if ($pdo) {
        echo "✅ Connexion PDO établie<br>";
    } else {
        echo "❌ Pas de connexion PDO<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// Test 3: Vérifier la table configuration
echo "<h2>3. Test de la table configuration</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM configuration");
    $configs = $stmt->fetchAll();
    echo "Nombre de lignes dans configuration: " . count($configs) . "<br>";
    
    if (count($configs) > 0) {
        echo "<pre>";
        foreach ($configs as $config) {
            echo "ID: " . $config['id_config'] . "<br>";
            echo "Nom boutique: " . ($config['nom_boutique'] ?? 'vide') . "<br>";
            echo "Est configuré: " . ($config['est_configure'] ?? '0') . "<br>";
            echo "---<br>";
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// Test 4: Fonction is_system_configured
echo "<h2>4. Test de is_system_configured()</h2>";
try {
    $isConfigured = is_system_configured();
    echo "Résultat: " . ($isConfigured ? "✅ Système configuré" : "❌ Système non configuré") . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// Test 5: Vérifier les utilisateurs
echo "<h2>5. Test de la table utilisateurs</h2>";
try {
    $stmt = $pdo->query("SELECT id_utilisateur, nom_complet, login, est_actif FROM utilisateurs");
    $users = $stmt->fetchAll();
    echo "Nombre d'utilisateurs: " . count($users) . "<br>";
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Login</th><th>Actif</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id_utilisateur'] . "</td>";
            echo "<td>" . $user['nom_complet'] . "</td>";
            echo "<td>" . $user['login'] . "</td>";
            echo "<td>" . ($user['est_actif'] ? 'Oui' : 'Non') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

echo "<br><br><a href='setup.php'>Retour à Setup</a> | <a href='index.php'>Aller à Index</a>";
