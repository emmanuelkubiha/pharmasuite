<?php
/**
 * ============================================================================
 * INDEX - PAGE D'ACCUEIL / REDIRECTION PRINCIPALE
 * ============================================================================
 * 
 * Importance : Point d'entrée du système STORESUITE
 * 
 * Fonctionnement :
 * 1. Vérifie si le système est configuré
 *    - Si NON → Redirige vers setup.php
 * 2. Vérifie si l'utilisateur est connecté
 *    - Si OUI → Redirige vers accueil.php (tableau de bord)
 *    - Si NON → Redirige vers login.php
 * 
 * ============================================================================
 */

// Inclusion de la configuration et connexion base de données
require_once __DIR__ . '/config/database.php';

// ============================================================================
// VÉRIFICATION 1 : SYSTÈME CONFIGURÉ ?
// ============================================================================
if (!is_system_configured()) {
    // Système non configuré → Rediriger vers la page de configuration
    redirect(BASE_URL . 'setup.php');
    exit;
}

// ============================================================================
// VÉRIFICATION 2 : UTILISATEUR CONNECTÉ ?
// ============================================================================
if (is_logged_in()) {
    // Utilisateur connecté → Rediriger vers le tableau de bord
    redirect(BASE_URL . 'accueil.php');
} else {
    // Utilisateur non connecté → Rediriger vers la page de connexion
    redirect(BASE_URL . 'login.php');
}

exit;