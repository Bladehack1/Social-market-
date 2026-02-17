<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: MANAGE USERS (ADMIN)
 * DEVELOPER: BLADE
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// SÉCURITÉ : Blade uniquement
confirm_admin();

$message = "";

// LOGIQUE : MISE À JOUR DU SOLDE MANUELLE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_balance'])) {
    $target_user_id = intval($_POST['user_id']);
    $new_balance = floatval($_POST['new_balance']);

    $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
    if ($stmt->execute([$new_balance, $target_user_id])) {
        $message = "<div class='alert success'>✅ Solde mis à jour avec succès.</div>";
    }
}

// RÉCUPÉRATION DE TOUS LES UTILISATEURS
$users = $pdo->query("SELECT id, username, email, balance, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Gestion Utilisateurs</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .user-table { width: 100%; border-collapse: collapse; margin-top: 30px; background: #151a25; border-radius: 20px; overflow: hidden; }
        .user-table th, .user-table td { padding: 18px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .user-table th { background: rgba(56, 189, 248, 0.1); color: #38bdf8; font-size: 0.8rem; }
        
        .balance-input {
            width: 100px; padding: 8px; background: #0a0e17; border: 1px solid #1e293b;
            color: #00e676; border-radius: 8px; font-weight: bold;
        }
        .btn-update {
            background: #38bdf8; border: none; padding: 8px 12px; border-radius: 8px;
            cursor: pointer; font-size: 0.8rem; font-weight: bold;
        }
    </style>
</head>
<body style="background: #0a0e17; color: white; padding: 40px;">

    <div style="max-width: 1100px; margin: 0 auto;">
        <header style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Gestion des Membres 👥</h1>
            <a href="index.php" style="color: #38bdf8; text-decoration: none;">← Retour Panel</a>
        </header>

        <?php echo $message; ?>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>Solde (RS)</th>
                    <th>Date d'inscription</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['id']; ?></td>
                    <td style="font-weight: 800;"><?php echo e($u['username']); ?> 
                        <?php if($u['role'] == 'admin'): ?> <span style="color:var(--accent); font-size: 0.6rem;">[ADMIN]</span> <?php endif; ?>
                    </td>
                    <td style="color: #94a3b8;"><?php echo e($u['email']); ?></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 10px;">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <input type="number" name="new_balance" class="balance-input" value="<?php echo $u['balance']; ?>" step="0.01">
                            <button type="submit" name="update_balance" class="btn-update">OK</button>
                        </form>
                    </td>
                    <td style="font-size: 0.8rem; color: #64748b;"><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <button class="btn" style="background: #ff3366; padding: 5px 10px; font-size: 0.7rem;">Bannir</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
