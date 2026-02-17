<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: LOGOUT (DÉCONNEXION)
 * DEVELOPER: BLADE
 */

// 1. On initialise la session pour pouvoir y accéder
session_start();

// 2. On vide toutes les variables de session
$_SESSION = array();

// 3. Si on veut détruire complètement la session, on efface aussi le cookie de session.
// Cela garantit qu'aucune trace ne reste sur le navigateur de l'utilisateur.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. On détruit enfin la session côté serveur
session_destroy();

// 5. Redirection immédiate vers la page de connexion avec un petit message
header("Location: index.php?msg=logged_out");
exit();
?>
