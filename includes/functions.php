<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * FILE: functions.php (Outils Sécurité & Formatage)
 * DEVELOPER: BLADE
 */

// 1. FORMATAGE DE L'ARGENT (Standard Pro)
function formatMoney($amount) {
    return "RS " . number_format($amount, 2, '.', ',');
}

// 2. SÉCURISATION DES ENTRÉES (Anti-XSS)
// Utilise cette fonction pour afficher n'importe quel texte venant d'un utilisateur
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// 3. VÉRIFICATION DE CONNEXION (Middleware)
function confirm_logged_in() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?error=auth_required");
        exit;
    }
}

// 4. VÉRIFICATION ADMIN (Protection Panel)
function confirm_admin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php?error=unauthorized");
        exit;
    }
}

// 5. CALCUL DU TEMPS ÉCOULÉ (Ex: "Il y a 2 minutes")
function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);           // value 60 is seconds
    $hours   = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400);        // value 86400 is 24 hours * 60 min * 60 sec
    
    if ($seconds <= 60) {
        return "À l'instant";
    } else if ($minutes <= 60) {
        return "Il y a $minutes min";
    } else if ($hours <= 24) {
        return "Il y a $hours h";
    } else {
        return "Il y a $days jours";
    }
}

// 6. GÉNÉRATEUR DE JETON CSRF (Sécurité contre les faux formulaires)
function get_token() {
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}
?>
