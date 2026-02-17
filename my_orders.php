
<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: MY ORDERS (HISTORIQUE)
 * DEVELOPER: BLADE
 */

session_start();
require_once 'includes/db.php';

// Sécurité : On vérifie si l'user est là
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// RÉCUPÉRATION DES COMMANDES (Jointure SQL pour avoir les noms des produits)
$query = "SELECT o.*, p.name as product_name, p.category 
          FROM orders o 
          JOIN products p ON o.product_id = p.id 
          WHERE o.user_id = ? 
          ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes | SocialMarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .orders-table-container {
            background: rgba(21, 26, 37, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.05);
            padding: 30px;
            margin-top: 30px;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; color: white; }
        th { text-align: left; padding: 15px; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 20px 15px; border-bottom: 1px solid rgba(255,255,255,0.03); }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status-completed { background: rgba(0, 230, 118, 0.1); color: #00e676; }
        .status-pending { background: rgba(255, 171, 0, 0.1); color: #ffab00; }
        
        .product-info h4 { margin: 0; font-size: 1rem; }
        .product-info small { color: #64748b; }
        
        .empty-state { text-align: center; padding: 60px; color: #64748b; }
    </style>
</head>
<body style="background: #0a0e17; color: white;">

<div class="app-container" style="display:flex;">
    <?php include 'includes/header.php'; ?>

    <main class="main-content" style="flex:1; padding: 40px; margin-left: 280px;">
        <h1 style="font-weight: 900; font-size: 2.5rem;">Mes Commandes 📦</h1>
        <p style="color: #94a3b8;">Suivez l'état de livraison de vos services digitaux.</p>

        <div class="orders-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Prix Payé</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div style="font-size: 3rem; margin-bottom: 10px;">🛒</div>
                                <p>Vous n'avez pas encore passé de commande.</p>
                                <a href="dashboard.php" class="btn btn-primary" style="margin-top: 15px;">Voir les services</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td style="color: #38bdf8; font-weight: 600;">#<?php echo $order['id']; ?></td>
                            <td>
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                    <small><?php echo htmlspecialchars($order['category']); ?></small>
                                </div>
                            </td>
                            <td style="font-weight: 700;">RS <?php echo number_format($order['price_at_purchase'], 2); ?></td>
                            <td style="color: #94a3b8; font-size: 0.9rem;">
                                <?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?>
                            </td>
                            <td>
                                <?php 
                                    $status_class = ($order['status'] == 'completed') ? 'status-completed' : 'status-pending';
                                    $status_text = ($order['status'] == 'completed') ? 'Livré' : 'En cours';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
