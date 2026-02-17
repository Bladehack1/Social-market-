<?php
// includes/db.php
$host = 'localhost';
$dbname = 'socialmarket_db';
$user = 'root'; // Par défaut sur KSWEB
$pass = '';     // Par défaut vide sur KSWEB

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // On active les erreurs pour débugger comme un pro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion fatale : " . $e->getMessage());
}
?>
