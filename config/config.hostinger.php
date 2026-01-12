<?php
/**
 * CONFIGURATION HOSTINGER - shop.fosip-drc.org
 * 
 * Instructions:
 * 1. Remplir les informations de base de données Hostinger
 * 2. Renommer ce fichier en config.php
 * 3. Le placer dans le dossier config/ sur le serveur
 * 4. NE JAMAIS committer sur Git avec les vrais credentials
 */

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// BASE DE DONNÉES HOSTINGER - RENSEIGNÉ
// ============================================================================
define('DB_HOST', 'localhost');                    // Généralement localhost sur Hostinger
define('DB_NAME', 'u783961849_storesuite');        // Nom de la base de données
define('DB_USER', 'u783961849_emmanuel');          // Nom d'utilisateur MySQL
define('DB_PASS', 'Hallelujah2018');               // Mot de passe MySQL
define('DB_CHARSET', 'utf8mb4');

// ============================================================================
// URLS ET CHEMINS
// ============================================================================
define('ROOT_PATH', dirname(__DIR__));

// URL de base - ADAPTER selon votre configuration Hostinger
// Si à la racine du domaine: https://storesuite.shop/
// Si dans un sous-dossier: https://storesuite.shop/storesuite/
define('BASE_URL', 'https://storesuite.shop/');

define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('LOGO_PATH', UPLOAD_PATH . 'logos/');
define('PRODUCT_IMG_PATH', UPLOAD_PATH . 'produits/');
define('USER_IMG_PATH', UPLOAD_PATH . 'utilisateurs/');

// ============================================================================
// SÉCURITÉ
// ============================================================================
// Clé secrète unique - NE JAMAIS PARTAGER
// Générer une nouvelle avec: bin2hex(random_bytes(32))
define('SECRET_KEY', 'F7k9mP2nX#wL4v@Q8rT$y5jB0hGc3fDe1AZ7bM4sJ6pY9w');

// Durée de validité de la session (2 heures = 7200 secondes)
define('SESSION_LIFETIME', 7200);

// Tentatives de connexion
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_BLOCK_DURATION', 15); // minutes

// ============================================================================
// APPLICATION
// ============================================================================
define('APP_NAME', 'STORESUITE');
define('APP_VERSION', '2.0.0');

// Fuseau horaire
date_default_timezone_set('Africa/Lubumbashi');

// ============================================================================
// MODE DEBUG - TOUJOURS OFF EN PRODUCTION!
// ============================================================================
// En production (Hostinger), toujours laisser à false
// ⚠️ ACTIVEZ TEMPORAIREMENT true pour diagnostiquer les erreurs de connexion BD
define('DEBUG_MODE', true);

if (DEBUG_MODE === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================================================
// NIVEAUX D'ACCÈS
// ============================================================================
define('NIVEAU_ADMIN', 1);
define('NIVEAU_VENDEUR', 2);

// ============================================================================
// STATUTS VENTES
// ============================================================================
define('VENTE_EN_COURS', 'en_cours');
define('VENTE_VALIDEE', 'validee');
define('VENTE_ANNULEE', 'annulee');

// ============================================================================
// TYPES MOUVEMENTS STOCK
// ============================================================================
define('MOUVEMENT_ENTREE', 'entree');
define('MOUVEMENT_SORTIE', 'sortie');
define('MOUVEMENT_AJUSTEMENT', 'ajustement');
define('MOUVEMENT_RETOUR', 'retour');

// ============================================================================
// CONFIGURATION UPLOADS
// ============================================================================
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ============================================================================
// CONFIGURATION SUPPLÉMENTAIRE
// ============================================================================
// Devise par défaut
define('DEVISE', 'USD');

// Configuration TVA
define('TVA_STANDARD', 0.16);                // 16% de TVA standard

// Configuration de la pagination
define('ITEMS_PER_PAGE', 20);                // Nombre d'éléments par page

// ============================================================================
// FONCTIONS UTILITAIRES GLOBALES
// ============================================================================

/**
 * Fonction pour échapper les données HTML
 * Prévient les attaques XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour rediriger vers une page
 */
function redirect($url, $code = 302) {
    header("Location: $url", true, $code);
    exit;
}

/**
 * Fonction pour afficher un message d'erreur et arrêter l'exécution
 */
function die_error($message) {
    die("<div style='background:#f8d7da;color:#721c24;padding:20px;border:1px solid #f5c6cb;border-radius:5px;margin:20px;'>
        <strong>Erreur :</strong> $message
    </div>");
}

/**
 * Fonction pour formater les montants avec la devise
 */
function format_montant($montant, $devise = '$') {
    return number_format($montant, 2, ',', ' ') . ' ' . $devise;
}

/**
 * Fonction pour formater les dates
 */
function format_date($date, $format = 'd/m/Y H:i') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Génère un token CSRF pour sécuriser les formulaires
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est administrateur
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['niveau_acces']) && $_SESSION['niveau_acces'] == NIVEAU_ADMIN;
}

/**
 * Obtient l'ID de l'utilisateur connecté
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtient le nom de l'utilisateur connecté
 */
function get_user_name() {
    return $_SESSION['user_name'] ?? 'Utilisateur';
}

/**
 * Enregistre une activité dans les logs
 */
function log_activity($type_action, $description, $donnees = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs_activites (id_utilisateur, type_action, description, ip_address, user_agent, donnees_json)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            get_user_id(),
            $type_action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $donnees ? json_encode($donnees) : null
        ]);
    } catch (Exception $e) {
        // En cas d'erreur, on ne bloque pas l'application
        error_log("Erreur log_activity: " . $e->getMessage());
    }
}

/**
 * Définit un message flash pour affichage après redirection
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type; // success, error, warning, info
}

/**
 * Récupère et supprime le message flash
 */
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Génère un slug à partir d'une chaîne
 */
function generate_slug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Calcule le montant TTC à partir du HT (avec TVA 16%)
 */
function calcul_montant_ttc($montant_ht) {
    $montant_tva = round($montant_ht * TVA_STANDARD, 2);
    return [
        'ht' => $montant_ht,
        'tva' => $montant_tva,
        'ttc' => $montant_ht + $montant_tva
    ];
}

/**
 * Nettoie une chaîne pour utilisation en nom de fichier
 */
function sanitize_filename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return substr($filename, 0, 200);
}

/**
 * Vérifie si une extension de fichier est autorisée
 */
function is_allowed_image($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ALLOWED_IMAGE_EXTENSIONS);
}

// ============================================================================
// INSTRUCTIONS DÉPLOIEMENT HOSTINGER - GUIDE COMPLET
// ============================================================================
/*
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║  GUIDE DE DÉPLOIEMENT STORESUITE SUR HOSTINGER                           ║
 * ╚══════════════════════════════════════════════════════════════════════════╝
 * 
 * 📋 ÉTAPE 1 : PRÉPARER LES FICHIERS
 * ────────────────────────────────────────────────────────────────────────────
 * 1. Renommer ce fichier en "config.php"
 * 2. Remplir les informations de base de données (lignes 19-23)
 * 3. Vérifier que BASE_URL correspond à votre domaine (ligne 34)
 * 4. Générer une nouvelle SECRET_KEY unique :
 *    Exécuter en PHP : echo bin2hex(random_bytes(32));
 * 
 * 🗄️  ÉTAPE 2 : CRÉER LA BASE DE DONNÉES
 * ────────────────────────────────────────────────────────────────────────────
 * 1. Connectez-vous au panneau Hostinger (hpanel.hostinger.com)
 * 2. Allez dans : Bases de données → Gestion MySQL
 * 3. Créer une nouvelle base de données :
 *    - Nom : storesuite (ou autre nom au choix)
 *    - Utilisateur : sera créé automatiquement
 *    - Mot de passe : sera généré (NOTEZ-LE!)
 * 4. Accédez à phpMyAdmin
 * 5. Sélectionnez votre base
 * 6. Onglet "Importer" → Choisir database/storesuite_online.sql
 * 7. Cliquez "Exécuter"
 * 
 * 📤 ÉTAPE 3 : UPLOADER LES FICHIERS
 * ────────────────────────────────────────────────────────────────────────────
 * Via FTP (FileZilla recommandé) :
 * 
 * Hôte FTP : ftp.storesuite.shop (ou IP fournie par Hostinger)
 * Utilisateur : votre nom d'utilisateur Hostinger
 * Mot de passe : votre mot de passe Hostinger
 * Port : 21
 * 
 * Structure à respecter sur le serveur :
 * 
 * public_html/
 * ├── config/
 * │   ├── config.php (ce fichier renommé)
 * │   └── database.php
 * ├── assets/
 * ├── ajax/
 * ├── database/
 * ├── uploads/
 * │   ├── logos/
 * │   ├── produits/
 * │   └── utilisateurs/
 * ├── .htaccess
 * ├── index.php
 * └── ... (tous les autres fichiers PHP)
 * 
 * ⚙️  ÉTAPE 4 : CONFIGURER LES PERMISSIONS
 * ────────────────────────────────────────────────────────────────────────────
 * Via le gestionnaire de fichiers Hostinger ou FTP :
 * 
 * Dossiers (755) :
 * - uploads/ et tous ses sous-dossiers
 * - config/
 * - assets/
 * - ajax/
 * 
 * Fichiers PHP (644) :
 * - Tous les fichiers .php
 * 
 * Fichier spécial (.htaccess) : 644
 * 
 * 🔒 ÉTAPE 5 : ACTIVER SSL (HTTPS)
 * ────────────────────────────────────────────────────────────────────────────
 * 1. Panneau Hostinger → Avancé → SSL
 * 2. Activer le certificat SSL gratuit
 * 3. Attendre 15-30 minutes pour l'activation
 * 4. Vérifier que BASE_URL utilise https:// (ligne 34)
 * 5. Ajouter redirection HTTP → HTTPS dans .htaccess (déjà configuré)
 * 
 * 🐘 ÉTAPE 6 : CONFIGURER PHP
 * ────────────────────────────────────────────────────────────────────────────
 * 1. Panneau Hostinger → Avancé → Configuration PHP
 * 2. Sélectionner PHP 8.0 ou supérieur (recommandé : PHP 8.2)
 * 3. Paramètres recommandés :
 *    - memory_limit : 256M minimum
 *    - upload_max_filesize : 10M
 *    - post_max_size : 10M
 *    - max_execution_time : 300
 * 
 * 👤 ÉTAPE 7 : PREMIER UTILISATEUR
 * ────────────────────────────────────────────────────────────────────────────
 * Deux options :
 * 
 * Option A - Via l'importation SQL :
 * Le fichier storesuite_online.sql contient déjà un compte admin :
 * - Email : admin@storesuite.com
 * - Mot de passe : Admin123!
 * ⚠️ CHANGEZ CE MOT DE PASSE immédiatement après connexion !
 * 
 * Option B - Créer manuellement via phpMyAdmin :
 * INSERT INTO utilisateurs (nom_complet, email, password_hash, niveau_acces) 
 * VALUES ('Admin', 'votre@email.com', '$2y$10$...', 1);
 * (Générer le hash avec : password_hash('VotreMotDePasse', PASSWORD_DEFAULT))
 * 
 * ✅ ÉTAPE 8 : VÉRIFICATION FINALE
 * ────────────────────────────────────────────────────────────────────────────
 * 1. Accéder à : https://storesuite.shop/
 * 2. Vérifier la page de connexion
 * 3. Se connecter avec le compte admin
 * 4. Aller dans Paramètres → Configurer :
 *    - Nom de la boutique
 *    - Logo
 *    - Informations de contact
 *    - Devise
 * 5. Tester une vente test
 * 6. Vérifier l'impression des factures
 * 
 * 🔧 ÉTAPE 9 : MAINTENANCE ET SÉCURITÉ
 * ────────────────────────────────────────────────────────────────────────────
 * ✓ Sauvegardes automatiques :
 *   - Hostinger fait des sauvegardes quotidiennes (vérifier dans le panneau)
 *   - Faire des exports manuels réguliers de la base de données
 * 
 * ✓ Sécurité :
 *   - NE JAMAIS partager les identifiants de la base de données
 *   - Changer les mots de passe admin régulièrement
 *   - Vérifier les logs d'activité (table logs_activites)
 *   - Garder DEBUG_MODE à false en production
 * 
 * ✓ Surveillance :
 *   - Vérifier l'espace disque disponible
 *   - Monitor les erreurs dans les logs
 *   - Tester régulièrement les fonctionnalités critiques
 * 
 * 📞 SUPPORT
 * ────────────────────────────────────────────────────────────────────────────
 * Support Hostinger : support@hostinger.com
 * Documentation : https://www.hostinger.com/tutorials
 * 
 * En cas de problème :
 * 1. Vérifier les logs d'erreur dans le panneau Hostinger
 * 2. Activer temporairement DEBUG_MODE (ligne 71) pour voir les erreurs
 * 3. Vérifier que tous les fichiers sont bien uploadés
 * 4. Confirmer que la base de données est accessible
 * 
 * ════════════════════════════════════════════════════════════════════════════
 * 🎉 Après ces étapes, votre système STORESuite sera opérationnel !
 * ════════════════════════════════════════════════════════════════════════════
 */
?>
