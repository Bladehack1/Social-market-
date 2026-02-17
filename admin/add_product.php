<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: ADD PRODUCT (ADMIN ONLY)
 * DEVELOPER: BLADE
 */

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// 1. SÉCURITÉ : Vérification du rang Admin
confirm_admin(); // Utilise la fonction de sécurité qu'on a créée dans functions.php

$message = "";

// 2. LOGIQUE D'AJOUT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_product'])) {
    $name = e($_POST['name']);
    $price = floatval($_POST['price']);
    $category = e($_POST['category']);
    $description = e($_POST['description']);
    
    // Optionnel : Gestion d'image (par défaut ici)
    $image_url = "default_service.png"; 

    if (!empty($name) && $price > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category, description, image_url, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$name, $price, $category, $description, $image_url]);
            
            $message = "<div class='alert success'>🚀 Service '$name' publié avec succès sur SocialMarket !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert error'>❌ Erreur SQL : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert warning'>⚠️ Veuillez remplir tous les champs obligatoires.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Ajouter un Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-form-container {
            max-width: 700px;
            margin: 40px auto;
            background: #151a25;
            padding: 40px;
            border-radius: 28px;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #94a3b8; font-weight: 600; font-size: 0.85rem; }
        input, select, textarea {
            width: 100%; padding: 15px; background: #0a0e17; border: 1px solid #1e293b;
            border-radius: 12px; color: white; font-family: 'Outfit'; box-sizing: border-box;
        }
        input:focus, textarea:focus { border-color: #38bdf8; outline: none; }
        .btn-publish {
            width: 100%; padding: 16px; background: #38bdf8; color: #000; border: none;
            border-radius: 12px; font-weight: 900; cursor: pointer; transition: 0.3s;
        }
        .btn-publish:hover { background: #fff; transform: translateY(-3px); }
    </style>
</head>
<body style="background: #0a0e17; color: white; padding: 20px;">

    <div class="admin-form-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-weight: 900; margin: 0;">NOUVEAU SERVICE</h1>
            <p style="color: #64748b;">Remplissez les détails pour vos clients</p>
        </div>

        <?php echo $message; ?>

        <form method="POST">
            <div class="form-group">
                <label>NOM DU SERVICE</label>
                <input type="text" name="name" placeholder="ex: Netflix Premium 4K" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>PRIX (RS)</label>
                    <input type="number" name="price" placeholder="ex: 500" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>CATÉGORIE</label>
                    <select name="category">
                        <option value="Streaming">Streaming</option>
                        <option value="Social Media">Social Media</option>
                        <option value="Gaming">Gaming</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>DESCRIPTION DÉTAILLÉE</label>
                <textarea name="description" rows="4" placeholder="Décrivez ce que le client reçoit exactement..."></textarea>
            </div>

            <button type="submit" name="submit_product" class="btn-publish">
                PUBLIER LE SERVICE MAINTENANT
            </button>
            
            <a href="../dashboard.php" style="display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 0.9rem;">
                ← Retour au Dashboard
            </a>
        </form>
    </div>

</body>
</html>
