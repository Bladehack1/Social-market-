<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: INDEX / LOGIN (CONNEXION)
 * DEVELOPER: BLADE
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Si l'utilisateur est déjà connecté, on le propulse au dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// LOGIQUE DE CONNEXION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(e($_POST['username']));
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Recherche de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Vérification du mot de passe haché (Sécurité Blade)
        if ($user && password_verify($password, $user['password'])) {
            // Création de la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Identifiants invalides ou compte inexistant.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | SocialMarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #0a0e17;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        /* Effet de fond lumineux */
        .bg-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.2;
            z-index: -1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .login-card {
            background: rgba(21, 26, 37, 0.7);
            backdrop-filter: blur(20px);
            padding: 50px;
            border-radius: 32px;
            border: 1px solid rgba(255,255,255,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h1 { font-weight: 900; font-size: 2.2rem; margin-bottom: 10px; }
        .login-card p { color: #94a3b8; margin-bottom: 30px; font-size: 0.9rem; }

        .input-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        
        input {
            width: 100%; padding: 16px; background: #0f172a; border: 1px solid #1e293b;
            border-radius: 14px; color: white; font-family: 'Outfit'; box-sizing: border-box;
            transition: 0.3s;
        }
        input:focus { border-color: #38bdf8; outline: none; box-shadow: 0 0 15px rgba(56, 189, 248, 0.1); }

        .btn-login {
            width: 100%; padding: 18px; background: #38bdf8; color: #000; border: none;
            border-radius: 14px; font-weight: 900; font-size: 1rem; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover { background: #fff; transform: scale(1.02); }

        .alert { background: rgba(255, 51, 102, 0.1); color: #ff3366; padding: 12px; border-radius: 10px; border: 1px solid #ff3366; margin-bottom: 20px; font-size: 0.85rem; }
        
        .footer-links { margin-top: 25px; font-size: 0.85rem; color: #64748b; }
        .footer-links a { color: #38bdf8; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<div class="login-card">
    <h1>BIENVENUE</h1>
    <p>Connectez-vous pour accéder à vos services</p>

    <?php if($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>NOM D'UTILISATEUR</label>
            <input type="text" name="username" placeholder="Entrez votre pseudo" required>
        </div>

        <div class="input-group">
            <label>MOT DE PASSE</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">ACCÉDER AU DASHBOARD</button>
    </form>

    <div class="footer-links">
        Pas encore membre ? <a href="register.php">Créer un compte Blade</a>
    </div>
</div>

</body>
</html>
