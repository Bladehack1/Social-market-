<?php
/**
 * PROJECT: SOCIALMARKET PRO 2026
 * DEVELOPER: CODE WITH BLADE
 * DESCRIPTION: Professional User Dashboard with Secure Transaction Logic
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php'; // Pour les fonctions de sécurité

// --- 1. SÉCURITÉ D'ACCÈS ---
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=session_expired");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// --- 2. TRAITEMENT DE L'ACHAT (LOGIQUE BACKEND) ---
$purchase_message = "";
if (isset($_POST['action']) && $_POST['action'] == 'purchase') {
    $product_id = intval($_POST['product_id']);

    // On récupère les infos du produit et le solde de l'user en une fois
    $stmt = $pdo->prepare("SELECT p.name, p.price, u.balance 
                           FROM products p, users u 
                           WHERE p.id = ? AND u.id = ?");
    $stmt->execute([$product_id, $user_id]);
    $data = $stmt->fetch();

    if ($data) {
        if ($data['balance'] >= $data['price']) {
            // DÉBUT DE LA TRANSACTION SQL (CRITIQUE POUR LA SÉCURITÉ)
            $pdo->beginTransaction();
            try {
                // Débiter le solde
                $update = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $update->execute([$data['price'], $user_id]);

                // Créer la commande
                $order = $pdo->prepare("INSERT INTO orders (user_id, product_id, price_at_purchase, status) VALUES (?, ?, ?, 'completed')");
                $order->execute([$user_id, $product_id, $data['price']]);

                $pdo->commit();
                $purchase_message = "<div class='alert success'>✅ Achat réussi ! " . htmlspecialchars($data['name']) . " a été ajouté à vos commandes.</div>";
            } catch (Exception $e) {
                $pdo->rollBack();
                $purchase_message = "<div class='alert error'>❌ Erreur lors de la transaction. Réessayez.</div>";
            }
        } else {
            $purchase_message = "<div class='alert warning'>⚠️ Solde insuffisant (RS " . number_format($data['balance'], 2) . "). Veuillez recharger votre compte.</div>";
        }
    }
}

// --- 3. RÉCUPÉRATION DES STATISTIQUES (POUR L'UI) ---
$user_stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$current_balance = $user_stmt->fetchColumn();

// Stats pour l'affichage humain
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$my_orders_count = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$my_orders_count->execute([$user_id]);
$my_orders = $my_orders_count->fetchColumn();

// Liste des produits actifs
$products = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SocialMarket Pro</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <style>
        /* CSS INTERNE POUR LES DÉTAILS SPÉCIFIQUES (UI 2026) */
        .dashboard-wrapper { display: flex; min-height: 100vh; background: #0a0e17; color: white; }
        
        .main-content { flex: 1; padding: 40px; margin-left: 280px; transition: 0.3s; }
        
        .welcome-section { margin-bottom: 40px; }
        .welcome-section h1 { font-size: 2.5rem; margin: 0; font-weight: 800; }
        .welcome-section p { color: #94a3b8; margin-top: 5px; }

        /* Stats Cards Layout */
        .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .stat-box { background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255,255,255,0.1); padding: 30px; border-radius: 24px; backdrop-filter: blur(10px); }
        .stat-box small { color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; font-weight: 700; }
        .stat-box h2 { font-size: 2.2rem; margin: 10px 0 0; color: #38bdf8; }

        /* Grid de produits longue et détaillée */
        .services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .service-card { 
            background: #151a25; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); 
            overflow: hidden; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        .service-card:hover { transform: translateY(-10px); border-color: #38bdf8; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .card-img { height: 160px; background: linear-gradient(45deg, #1e293b, #0f172a); display: flex; align-items: center; justify-content: center; font-size: 3rem; }
        .card-body { padding: 25px; }
        .card-body h3 { margin: 0 0 10px; font-size: 1.3rem; }
        .card-body p { color: #94a3b8; font-size: 0.9rem; line-height: 1.6; height: 60px; overflow: hidden; }
        
        .card-footer { padding: 20px 25px; background: rgba(255,255,255,0.02); display: flex; justify-content: space-between; align-items: center; }
        .price { font-size: 1.4rem; font-weight: 800; color: #00e676; }
        
        /* Alert Styles */
        .alert { padding: 20px; border-radius: 15px; margin-bottom: 30px; font-weight: 600; }
        .alert.success { background: rgba(0, 230, 118, 0.1); border: 1px solid #00e676; color: #00e676; }
        .alert.error { background: rgba(255, 51, 102, 0.1); border: 1px solid #ff3366; color: #ff3366; }
        .alert.warning { background: rgba(255, 171, 0, 0.1); border: 1px solid #ffab00; color: #ffab00; }

        .btn-buy { background: #38bdf8; color: #000; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; }
        .btn-buy:hover { background: #ffffff; transform: scale(1.05); }

        /* Sidebar Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <?php include 'includes/header.php'; ?>

    <main class="main-content">
        
        <div class="welcome-section">
            <h1>Espace Membre 🚀</h1>
            <p>Heureux de vous revoir, <strong><?php echo htmlspecialchars($username); ?></strong>. Gérez vos services en un clic.</p>
        </div>

        <?php echo $purchase_message; ?>

        <div class="stats-container">
            <div class="stat-box">
                <small>Votre Solde Actuel</small>
                <h2>RS <?php echo number_format($current_balance, 2); ?></h2>
            </div>
            <div class="stat-box">
                <small>Vos Commandes</small>
                <h2><?php echo $my_orders; ?></h2>
            </div>
            <div class="stat-box">
                <small>Communauté SocialMarket</small>
                <h2><?php echo $total_users; ?> <span style="font-size: 1rem; color: #94a3b8;">Membres</span></h2>
            </div>
        </div>

        <h2 style="margin-bottom: 30px; font-weight: 800;">Services Disponibles</h2>
        
        <div class="services-grid">
            <?php if(empty($products)): ?>
                <div class="stat-box" style="grid-column: 1/-1; text-align: center;">
                    <p>Aucun service n'est disponible pour le moment. Revenez bientôt !</p>
                </div>
            <?php else: ?>
                <?php foreach($products as $row): ?>
                    <div class="service-card">
                        <div class="card-img">📦</div>
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="price">RS <?php echo number_format($row['price'], 0); ?></div>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="purchase">
                                <button type="submit" class="btn-buy" onclick="return confirm('Confirmer l\'achat de ce service ?')">ACHETER</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php include 'includes/footer.php'; ?>
    </main>
</div>

<script src="assets/js/main.js"></script>
<script>
    // Petit effet de bienvenue dans la console
    console.log("%c SocialMarket Pro | Coded by Blade ", "color: #38bdf8; font-weight: bold; font-size: 18px;");
</script>

</body>
</html>
