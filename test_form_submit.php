<?php
/**
 * Test de soumission du formulaire setup
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Test de soumission du formulaire</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;background:#d4edda;padding:10px;margin:10px 0;border-radius:5px;} .error{color:red;background:#f8d7da;padding:10px;margin:10px 0;border-radius:5px;} .info{color:blue;background:#d1ecf1;padding:10px;margin:10px 0;border-radius:5px;} pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div class='info'><strong>✅ Formulaire soumis en POST</strong></div>";
    
    echo "<h2>Données POST reçues:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Fichiers uploadés:</h2>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    // Maintenant essayons d'exécuter process_setup.php
    echo "<div class='info'><strong>🔄 Tentative d'exécution de process_setup.php...</strong></div>";
    
    // Capturer la sortie
    ob_start();
    try {
        include __DIR__ . '/process_setup.php';
    } catch (Exception $e) {
        echo "<div class='error'><strong>❌ Erreur:</strong> " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "<h2>Sortie de process_setup.php:</h2>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    
} else {
    // Afficher un formulaire de test
    echo "<div class='info'>Remplissez ce formulaire de test pour voir ce qui se passe lors de la soumission.</div>";
    ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <h2>Formulaire de test</h2>
        
        <div style="margin:10px 0;">
            <label><strong>Nom de la boutique *</strong></label><br>
            <input type="text" name="nom_boutique" value="Ma Super Boutique Test" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Slogan</strong></label><br>
            <input type="text" name="slogan" value="Votre partenaire commercial" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Adresse *</strong></label><br>
            <textarea name="adresse" required style="width:100%;padding:8px;margin-top:5px;">123 Avenue Test, Kinshasa</textarea>
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Téléphone *</strong></label><br>
            <input type="text" name="telephone" value="+243 999 999 999" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Email</strong></label><br>
            <input type="email" name="email" value="test@boutique.com" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Devise *</strong></label><br>
            <select name="devise" required style="width:100%;padding:8px;margin-top:5px;">
                <option value="CDF">CDF - Franc congolais</option>
                <option value="$">$ - Dollar américain</option>
            </select>
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Site web</strong></label><br>
            <input type="url" name="site_web" value="https://www.test.com" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>N° Registre Commerce</strong></label><br>
            <input type="text" name="num_registre_commerce" value="RCCM/TEST/123" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>N° Fiscal</strong></label><br>
            <input type="text" name="num_impot" value="IMP-12345" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <input type="hidden" name="couleur_primaire" value="#206bc4">
        <input type="hidden" name="couleur_secondaire" value="#1a5aa8">
        
        <h3 style="margin-top:30px;">Compte Administrateur</h3>
        
        <div style="margin:10px 0;">
            <label><strong>Nom complet *</strong></label><br>
            <input type="text" name="admin_nom" value="Admin Test" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Login *</strong></label><br>
            <input type="text" name="admin_login" value="admin" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Mot de passe *</strong></label><br>
            <input type="password" name="admin_password" value="123456" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Confirmer mot de passe *</strong></label><br>
            <input type="password" name="admin_password_confirm" value="123456" required style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <div style="margin:10px 0;">
            <label><strong>Email Admin</strong></label><br>
            <input type="email" name="admin_email" value="admin@test.com" style="width:100%;padding:8px;margin-top:5px;">
        </div>
        
        <button type="submit" style="margin-top:20px;padding:15px 40px;background:#28a745;color:white;border:none;border-radius:5px;font-size:16px;font-weight:bold;cursor:pointer;">
            🚀 Tester la configuration
        </button>
    </form>
    
    <?php
}

echo "<hr><p><a href='diagnostic.php'>← Retour au diagnostic</a> | <a href='setup.php'>Aller à setup.php</a></p>";
?>
