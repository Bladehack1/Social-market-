<?php 
session_start();
require_once 'includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Identifiants incorrects, Blade.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialMarket | Connexion</title>
    <style>
        :root {
            --primary: #0061FF;
            --glass: rgba(255, 255, 255, 0.8);
        }
        body {
            margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', system-ui; background: #f0f2f5;
        }
        .login-card {
            background: var(--glass); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 28px; width: 100%; max-width: 360px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); border: 1px solid white;
        }
        h2 { text-align: center; color: #1a1a1a; margin-bottom: 30px; }
        input {
            width: 100%; padding: 14px; margin-bottom: 15px; border-radius: 12px;
            border: 1px solid #ddd; box-sizing: border-box; font-size: 16px;
        }
        button {
            width: 100%; padding: 14px; border: none; border-radius: 12px;
            background: var(--primary); color: white; font-weight: bold; cursor: pointer;
            transition: 0.3s; font-size: 16px;
        }
        button:hover { opacity: 0.9; transform: translateY(-2px); }
        .error { color: #ff385c; text-align: center; margin-bottom: 15px; font-size: 14px; }
        .link { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>USER LOGIN</h2>
        <?php if($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN NOW</button>
        </form>
        <div class="link">Pas encore de compte ? <a href="register.php" style="color:var(--primary)">S'inscrire</a></div>
    </div>
</body>
</html>
