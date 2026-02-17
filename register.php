<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: REGISTER (INSCRIPTION)
 * DEVELOPER: BLADE
 */

session_start();
require_once 'includes/db.php';

// Si l'utilisateur est déjà connecté, on l'envoie au dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $email    = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- 1. VALIDATIONS DE SÉCURITÉ HUMAINE ---
    if (strlen($username) < 4) {
        $error = "Le pseudo est trop court (min 4 caractères).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit faire au moins 6 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // --- 2. VÉRIFICATION DES DOUBLONS (SQL) ---
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if ($check->rowCount() > 0) {
            $error = "Ce pseudo ou cet email est déjà utilisé.";
        } else {
            // --- 3. CRÉATION DU COMPTE ---
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            try {
                $insert = $pdo->prepare("INSERT INTO users (username, email, password, balance, role) VALUES (?, ?, ?, 0.00, 'user')");
                $insert->execute([$username, $email, $hashed_password]);
                
                $success = "Compte créé avec succès, Blade ! <a href='index.php' style='color:#38bdf8'>Connectez-vous ici</a>";
            } catch (PDOException $e) {
                $error = "Erreur système lors de la création.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte | SocialMarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #0a0e17; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        
        .auth-card {
            background: rgba(21, 26, 37, 0.8);
            backdrop-filter: blur(15px);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.05);
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }

        .auth-header { text-align: center; margin-bottom: 35px; }
        .auth-header h1 { font-weight: 900; font-size: 2rem; margin: 0; }
        .auth-header p { color: #94a3b8; font-size: 0.9rem; margin-top: 10px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 0.85rem; font-weight: 600; }
        input {
            width: 100%; padding: 14px; background: #0f172a; border: 1px solid #1e293b;
            border-radius: 12px; color: white; font-family: 'Outfit'; transition: 0.3s;
            box-sizing: border-box;
        }
        input:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1); outline: none; }

        .btn-reg {
            width: 100%; padding: 16px; background: #38bdf8; color: #000; border: none;
            border-radius: 12px; font-weight: 900; font-size: 1rem; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
        }
        .btn-reg:hover { background: #fff; transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; text-align: center; }
        .alert-error { background: rgba(255, 51, 102, 0.1); color: #ff3366; border: 1px solid #ff3366; }
        .alert-success { background: rgba(0, 230, 118, 0.1); color: #00e676; border: 1px solid #00e676; }

        .footer-link { text-align: center; margin-top: 25px; color: #94a3b8; font-size: 0.9rem; }
        .footer-link a { color: #38bdf8; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <h1>REJOINDRE 👋</h1>
        <p>Commencez à booster vos réseaux avec SocialMarket</p>
    </div>

    <?php if($error): ?> <div class="alert alert-error"><?php echo $error; ?></div> <?php endif; ?>
    <?php if($success): ?> <div class="alert alert-success"><?php echo $success; ?></div> <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>NOM D'UTILISATEUR</label>
            <input type="text" name="username" placeholder="ex: blade_dev" required value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>">
        </div>

        <div class="form-group">
            <label>ADRESSE EMAIL</label>
            <input type="email" name="email" placeholder="votre@email.com" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
        </div>

        <div class="form-group">
            <label>MOT DE PASSE</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>CONFIRMER MOT DE PASSE</label>
            <input type="password" name="confirm_password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-reg">CRÉER MON COMPTE</button>
    </form>

    <div class="footer-link">
        Déjà un compte ? <a href="index.php">Se connecter</a>
    </div>
</div>

</body>
</html>
