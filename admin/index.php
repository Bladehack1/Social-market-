<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: ADMIN DASHBOARD (INDEX)
 * DEVELOPER: BLADE
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// 1. SÉCURITÉ : Vérification stricte du rôle Admin
confirm_admin();

// 2. RÉCUPÉRATION DES STATISTIQUES GLOBALES
// Total des utilisateurs
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// Revenus totaux (Argent dépensé par les clients)
$total_revenue = $pdo->query("SELECT SUM(price_at_purchase) FROM orders")->fetchColumn() ?? 0;

// Dépôts en attente (Argent à valider)
$pending_deposits_count = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();

// Dernières commandes
$recent_orders = $pdo->query("SELECT o.*, u.username, p.name as product_name 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             JOIN products p ON o.product_id = p.id 
                             ORDER BY o.order_date DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Blade Panel | Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card-admin { 
            background: #151a25; padding: 25px; border-radius: 20px; border-left: 4px solid #38bdf8;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .stat-card-admin h3 { color: #64748b; font-size: 0.8rem; text-transform: uppercase; margin: 0; }
        .stat-card-admin p { font-size: 1.8rem; font-weight: 900; margin: 10px 0 0; }

        .admin-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .admin-panel-box { background: rgba(21, 26, 37, 0.6); padding: 30px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); }
        
        .quick-actions a {
            display: flex; align-items: center; gap: 15px; padding: 15px;
            background: #0a0e17; margin-bottom: 10px; border-radius: 12px;
            text-decoration: none; color: white; transition: 0.3s;
        }
        .quick-actions a:hover { background: #38bdf8; color: #000; }
    </style>
</head>
<body style="background: #0a0e17; color: white; padding: 40px;">

    <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-weight: 900; font-size: 2.2rem; color: #38bdf8;">BLADE PANEL 🛡️</h1>
            <p style="color: #94a3b8;">Vue d'ensemble de votre business SocialMarket</p>
        </div>
        <a href="../dashboard.php" class="btn btn-primary" style="background: #fff; color: #000;">VOIR LE SITE</a>
    </header>

    <div class="admin-stats">
        <div class="stat-card-admin">
            <h3>Membres Totaux</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="stat-card-admin" style="border-left-color: #00e676;">
            <h3>Chiffre d'Affaire</h3>
            <p>RS <?php echo number_format($total_revenue, 0); ?></p>
        </div>
        <div class="stat-card-admin" style="border-left-color: #ffab00;">
            <h3>Dépôts à valider</h3>
            <p><?php echo $pending_deposits_count; ?></p>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-panel-box">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Dernières Commandes Clients</h2>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="text-align: left; color: #64748b;">
                        <th style="padding-bottom: 15px;">Client</th>
                        <th style="padding-bottom: 15px;">Service</th>
                        <th style="padding-bottom: 15px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px 0;"><strong><?php echo e($order['username']); ?></strong></td>
                        <td><?php echo e($order['product_name']); ?></td>
                        <td style="color: #64748b;"><?php echo time_ago($order['order_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-panel-box">
            <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Actions Rapides</h2>
            <div class="quick-actions">
                <a href="manage_deposits.php">
                    <i class="fas fa-money-check-alt"></i> Valider les fonds (<?php echo $pending_deposits_count; ?>)
                </a>
                <a href="add_product.php">
                    <i class="fas fa-plus-circle"></i> Ajouter un Service
                </a>
                <a href="manage_users.php">
                    <i class="fas fa-users"></i> Gérer les Utilisateurs
                </a>
            </div>
        </div>
    </div>

</body>
</html>
