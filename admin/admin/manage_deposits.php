
<?php
/**
 * PROJECT: SOCIALMARKET ADMIN
 * PAGE: VALIDATION DES FONDS
 * DEVELOPER: BLADE
 */

session_start();
require_once '../includes/db.php';

// 1. SÉCURITÉ : Seul l'admin entre ici
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Accès non autorisé ! Seul Blade peut valider les fonds.");
}

$message = "";

// 2. LOGIQUE DE VALIDATION (L'ACTION HUMAINE)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $deposit_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Récupérer les infos du dépôt
    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE id = ? AND status = 'pending'");
    $stmt->execute([$deposit_id]);
    $deposit = $stmt->fetch();

    if ($deposit) {
        if ($action === 'approve') {
            try {
                $pdo->beginTransaction();
                
                // A. Mettre à jour le solde de l'utilisateur
                $updateUser = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $updateUser->execute([$deposit['amount'], $deposit['user_id']]);
                
                // B. Marquer le dépôt comme approuvé
                $updateDep = $pdo->prepare("UPDATE deposits SET status = 'approved' WHERE id = ?");
                $updateDep->execute([$deposit_id]);
                
                $pdo->commit();
                $message = "<div class='alert success'>✅ Dépôt de RS " . $deposit['amount'] . " approuvé !</div>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "<div class='alert error'>❌ Erreur critique lors de la validation.</div>";
            }
        } elseif ($action === 'reject') {
            $updateDep = $pdo->prepare("UPDATE deposits SET status = 'rejected' WHERE id = ?");
            $updateDep->execute([$deposit_id]);
            $message = "<div class='alert warning'>🚫 Dépôt rejeté.</div>";
        }
    }
}

// 3. RÉCUPÉRER LES DÉPÔTS EN ATTENTE
$pending_deposits = $pdo->query("SELECT d.*, u.username FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status = 'pending' ORDER BY d.created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Validation des Fonds</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #151a25; border-radius: 15px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .admin-table th { background: rgba(56, 189, 248, 0.1); color: #38bdf8; font-size: 0.8rem; text-transform: uppercase; }
        .btn-action { padding: 8px 15px; border-radius: 8px; font-size: 0.8rem; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-approve { background: #00e676; color: #000; }
        .btn-reject { background: #ff3366; color: #fff; margin-left: 10px; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(0, 230, 118, 0.1); color: #00e676; border: 1px solid #00e676; }
    </style>
</head>
<body style="background: #0a0e17; color: white; padding: 40px;">

    <div style="max-width: 1000px; margin: 0 auto;">
        <h1>Gestion des Dépôts 💰</h1>
        <p style="color: #94a3b8;">Vérifiez vos SMS JazzCash/EasyPaisa avant d'approuver.</p>
        <br>

        <?php echo $message; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Montant</th>
                    <th>Méthode</th>
                    <th>Transaction ID (TID)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($pending_deposits)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:30px; color:#64748b;">Aucun dépôt en attente.</td></tr>
                <?php else: ?>
                    <?php foreach($pending_deposits as $dep): ?>
                    <tr>
                        <td style="font-weight:bold;"><?php echo htmlspecialchars($dep['username']); ?></td>
                        <td style="color:#00e676;">RS <?php echo number_format($dep['amount'], 2); ?></td>
                        <td><?php echo $dep['method']; ?></td>
                        <td style="font-family: monospace; background:rgba(255,255,255,0.05); padding:5px;"><?php echo $dep['transaction_id']; ?></td>
                        <td style="font-size:0.8rem; color:#94a3b8;"><?php echo $dep['created_at']; ?></td>
                        <td>
                            <a href="?action=approve&id=<?php echo $dep['id']; ?>" class="btn-action btn-approve" onclick="return confirm('Créditer le compte ?')">APPROUVER</a>
                            <a href="?action=reject&id=<?php echo $dep['id']; ?>" class="btn-action btn-reject">REJETER</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <br>
        <a href="../dashboard.php" style="color:#38bdf8;">← Retour au Dashboard</a>
    </div>

</body>
</html>
