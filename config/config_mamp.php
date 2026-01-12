<?php
// ============================================================================
// CONFIGURATION - STORESUITE (LOCAL MAMP - MAC)
// ============================================================================
// Fichier de configuration pour développement local sur MAMP (Mac)
// À renommer en config.php lors du déploiement sur le serveur

// Base de données
define('DB_HOST', 'localhost');      // Port 3306 par défaut sur MAMP
define('DB_NAME', 'storesuite');
define('DB_USER', 'root');
define('DB_PASS', 'root');           // MAMP utilise 'root' par défaut (sans mot de passe)

// Application
define('BASE_URL', 'http://localhost:8888/STORESuite/');
define('DEVISE', 'USD');
define('APP_NAME', 'STORESuite');
define('VERSION', '1.0.0');

// Sécurité
define('SECRET_KEY', 'F7k9mP2nX#wL4v@Q8rT$y5jB0hGc3fDe1AZ7bM4sJ6pY9w');
define('SESSION_LIFETIME', 7200); // 2 heures

// Mode debug (true pour développement local, false pour production)
define('DEBUG_MODE', true);

// Niveaux d'accès utilisateur
define('NIVEAU_USER', 1);
define('NIVEAU_MANAGER', 2);
define('NIVEAU_ADMIN', 3);

// Fuseau horaire
date_default_timezone_set('Africa/Lubumbashi');

// Configuration de session
session_start();
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// ============================================================================
// PARAMÈTRES DE CONFIGURATION
// ============================================================================

// Configuration du shop
define('NOM_BOUTIQUE', 'STORESuite');
define('ADRESSE_BOUTIQUE', '');
define('TELEPHONE_BOUTIQUE', '');
define('EMAIL_BOUTIQUE', '');
define('LOGO_BOUTIQUE', '');

// Configuration TVA
define('TVA_STANDARD', 0.16); // 16% de TVA

// Configuration des uploads
define('MAX_UPLOAD_SIZE', 5242880); // 5MB en bytes
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Configuration de la pagination
define('ITEMS_PER_PAGE', 20);

// ============================================================================
// CONFIGURATION PAR ENVIRONNEMENT
// ============================================================================

if (DEBUG_MODE) {
    // Développement
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Production
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// ============================================================================
// FIN DE LA CONFIGURATION
// ============================================================================
?>
