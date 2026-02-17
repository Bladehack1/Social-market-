<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: INDEX (ROOT) - Login & Welcome
 * DEVELOPER: BLADE
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Si déjà connecté, on saute direct au dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// LOGIQUE DE CONNEXION
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Pseudo ou mot de passe incorrect.";
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
    <title>SocialMarket | Bienvenue</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0; padding: 0;
            background: #0f172a;
            color: white;
            display: flex; align-items: center; justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .hero-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%; max-width: 1200px;
            gap: 50px; padding: 20px;
        }

        .hero-text { display: flex; flex-direction: column; justify-content: center; }
        .hero-text h1 { font-size: 4rem; line-height: 1.1; margin: 0; font-weight: 900; }
        .hero-text span { color: #38bdf8; }
        .hero-text p { color: #94a3b8; font-size: 1.2rem; margin-top: 20px; }

        .login-box {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        input {
            width: 100%; padding: 15px; margin-top: 10px; margin-bottom: 20px;
            background: #0a0e17; border: 1px solid #1e293b; border-radius: 12px;
            color: white; box-sizing: border-box;
        }

        .btn-submit {
            width: 100%; padding: 16px; background: #38bdf8; color: #000;
            border: none; border-radius: 12px; font-weight: 900; cursor: pointer;
        }

        .error-msg { background: rgba(255, 51, 102, 0.1); color: #ff3366; padding: 10px; border-radius: 8px; margin-bottom: 15px; }

        @media (max-width: 900px) {
            .hero-section { grid-template-columns: 1fr; text-align: center; }
            .hero-text h1 { font-size: 2.5rem; }
            .hero-text p { display: none; }
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="hero-text">
            <h1>Boostez votre <span>Présence Digitale</span></h1>
            <p>La plateforme n°1 pour les services premium. Fiable, rapide et sécurisée par Blade.</p>
        </div>

        <div class="login-box">
            <h2 style="margin-top:0">Connexion</h2>
            
            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <label>NOM D'UTILISATEUR</label>
                <input type="text" name="username" placeholder="Pseudo" required>

                <label>MOT DE PASSE</label>
                <input type="password" name="password" placeholder="••••••••" required>

                <button type="submit" name="login" class="btn-submit">SE CONNECTER</button>
            </form>
            
            <p style="text-align:center; font-size: 0.9rem; margin-top:20px;">
                Nouveau ici ? <a href="register.php" style="color:#38bdf8; text-decoration:none">Créer un compte</a>
            </p>
        </div>
    </div>

</body>
</html>
