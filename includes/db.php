<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * FILE: db.php (Base de données)
 * DEVELOPER: BLADE
 */

// Paramètres de connexion (À adapter selon ton serveur Android/KSWEB)
$host = 'localhost';
$dbname = 'socialmarket_db';
$user = 'root'; 
$pass = ''; // Sur KSWEB, le mot de passe est souvent vide par défaut

try {
    // 1. Création de la connexion avec le jeu de caractères UTF-8 pour éviter les bugs d'accents
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    // 2. Options de configuration pour un site pro
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ATTR_ERRMODE_EXCEPTION, // Lance une erreur si une requête échoue
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,           // Récupère les données sous forme de tableaux propres
        PDO::ATTR_EMULATE_PREPARES   => false,                      // Désactive l'émulation pour une sécurité SQL accrue
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    // Si on arrive ici, c'est que la connexion a réussi.
} catch (PDOException $e) {
    // 3. En cas d'erreur, on affiche un message propre (important pour le débuggage sur Android)
    die("ERREUR DE CONNEXION : " . $e->getMessage());
}
?>
