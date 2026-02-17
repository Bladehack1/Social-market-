<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * PAGE: ADD FUNDS (RECHARGE DE COMPTE)
 * DEVELOPER: BLADE FRONTEND & BACKEND
 */

session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['user_id'];
$status_message = "";

// LOGIQUE DE RÉCEPTION DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_deposit'])) {
    $amount = floatval($_POST['amount']);
    $method = htmlspecialchars($_POST['payment_method']);
    $transaction_id = htmlspecialchars($_POST['tid']);

    if ($amount >= 100) { // Montant minimum
        // On enregistre la demande de dépôt en "Attente"
        $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, method, transaction_id, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $amount, $method, $transaction_id]);
        
        $status_message = "<div class='alert success'>✅ Demande envoyée ! Votre solde sera mis à jour dès que Blade aura validé le TID : <strong>$transaction_id</strong>.</div>";
    } else {
        $status_message = "<div class='alert error'>❌ Le montant minimum est de 100 RS.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des Fonds | SocialMarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .deposit-container { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; }
        
        /* Liste des méthodes de paiement */
        .payment-methods { display: flex; flex-direction: column; gap: 15px; }
        .method-card { 
            background: #151a25; border: 1px solid rgba(255,255,255,0.05); padding: 20px; 
            border-radius: 18px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 15px;
        }
        .method-card:hover, .method-card.active { border-color: #38bdf8; background: rgba(56, 189, 248, 0.05); }
        .method-logo { width: 50px; height: 50px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #000; font-size: 10px; overflow: hidden; }
        
        /* Formulaire */
        .deposit-form { background: #151a25; padding: 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); }
        .input-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; color: #94a3b8; font-weight: 500; }
        input, select { width: 100%; padding: 15px; background: #0a0e17; border: 1px solid #1e293b; border-radius: 12px; color: white; font-size: 1rem; }
        input:focus { border-color: #38bdf8; outline: none; }

        .instructions { background: rgba(56, 189, 248, 0.1); border-left: 4px solid #38bdf8; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .instructions p { margin: 5px 0; font-size: 0.9rem; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert.success { background: rgba(0, 230, 118, 0.1); color: #00e676; border: 1px solid #00e676; }
        .alert.error { background: rgba(255, 51, 102, 0.1); color: #ff3366; border: 1px solid #ff3366; }
    </style>
</head>
<body style="background: #0a0e17; color: white;">

<div class="app-container" style="display:flex;">
    <?php include 'includes/header.php'; ?>

    <main class="main-content" style="flex:1; padding: 40px; margin-left: 280px;">
        <h1 style="font-weight: 900; font-size: 2.5rem; margin-bottom: 10px;">Recharger Solde 💳</h1>
        <p style="color: #94a3b8; margin-bottom: 40px;">Ajoutez des fonds pour commander vos services instantanément.</p>

        <?php echo $status_message; ?>

        <div class="deposit-container">
            
            <div class="payment-methods">
                <h3>1. Choisir la méthode</h3>
                <div class="method-card active" onclick="selectMethod('JazzCash', '0300XXXXXXX', 'M. BLADE')">
                    <div class="method-logo">JAZZ</div>
                    <div>
                        <div style="font-weight: 700;">JazzCash</div>
                        <small style="color: #94a3b8;">Instant Transfer</small>
                    </div>
                </div>
                <div class="method-card" onclick="selectMethod('EasyPaisa', '0345XXXXXXX', 'M. BLADE')">
                    <div class="method-logo">EASY</div>
                    <div>
                        <div style="font-weight: 700;">EasyPaisa</div>
                        <small style="color: #94a3b8;">Mobile Wallet</small>
                    </div>
                </div>
                <div class="method-card" onclick="selectMethod('SadaPay', '0312XXXXXXX', 'M. BLADE')">
                    <div class="method-logo">SADA</div>
                    <div>
                        <div style="font-weight: 700;">SadaPay</div>
                        <small style="color: #94a3b8;">Bank Transfer</small>
                    </div>
                </div>
            </div>

            <div class="deposit-form">
                <div class="instructions" id="payment-info">
                    <strong>Instructions de paiement :</strong>
                    <p>Envoyez le montant sur le compte JazzCash suivant :</p>
                    <p style="font-size: 1.2rem; color: #38bdf8; font-weight: 800;">Numéro : 0300XXXXXXX</p>
                    <p>Nom : <strong>M. BLADE</strong></p>
                </div>

                <form method="POST">
                    <input type="hidden" name="payment_method" id="selected_method" value="JazzCash">
                    
                    <div class="input-group">
                        <label>Montant à envoyer (RS)</label>
                        <input type="number" name="amount" placeholder="Ex: 1000" required min="100">
                    </div>

                    <div class="input-group">
                        <label>Transaction ID (TID)</label>
                        <input type="text" name="tid" placeholder="Entrez l'ID reçu par SMS" required>
                        <small style="color: #64748b; font-size: 0.8rem;">C'est l'identifiant unique de votre transfert.</small>
                    </div>

                    <button type="submit" name="submit_deposit" class="btn btn-primary" style="width: 100%; padding: 18px; font-size: 1.1rem;">
                        CONFIRMER MON DÉPÔT
                    </button>
                </form>
            </div>

        </div>
    </main>
</div>

<script>
function selectMethod(name, num, owner) {
    document.getElementById('selected_method').value = name;
    document.getElementById('payment-info').innerHTML = `
        <strong>Instructions de paiement ${name} :</strong>
        <p>Envoyez le montant sur le compte suivant :</p>
        <p style="font-size: 1.2rem; color: #38bdf8; font-weight: 800;">Numéro : ${num}</p>
        <p>Nom : <strong>${owner}</strong></p>
    `;
    
    // Switch active class
    const cards = document.querySelectorAll('.method-card');
    cards.forEach(c => c.classList.remove('active'));
    event.currentTarget.classList.add('active');
}
</script>

</body>
</html>
