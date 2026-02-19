<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

$uid = (int)$_SESSION["user_id"];
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = (float)($_POST["amount"] ?? 0);

    if ($amount <= 0) {
        $error = "Montant invalide.";
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $uid);
        $stmt->execute();
        $msg = "Solde rechargé ✅";
    }
}

$stmt = $mysqli->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$username = $user ? $user["username"] : "Utilisateur";
$balance  = $user ? (float)$user["balance"] : 0.0;

$invoices = [];
try {
    $stInv = $mysqli->prepare("SELECT id, total_amount, created_at FROM invoice WHERE user_id = ? ORDER BY id DESC LIMIT 20");
    $stInv->bind_param("i", $uid);
    $stInv->execute();
    $invoices = $stInv->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $invoices = [];
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon compte</title>
  <link rel="stylesheet" href="/php_exam/public/assets/style.css">
</head>
<body>

<div class="container">

  <div class="nav">
    <div class="brand">
      <a href="/php_exam/public/" class="brand">GameRelic</a>
      <span class="badge">E-commerce PHP</span>
    </div>

    <div class="navlinks">
      <a class="btn" href="/php_exam/public/">Accueil</a>
      <a class="btn" href="/php_exam/public/sell.php">Vendre</a>
      <a class="btn" href="/php_exam/public/cart.php">Panier</a>
      <a class="btn" href="/php_exam/public/account.php">Compte</a>
      <a class="btn danger" href="/php_exam/public/logout.php">Se déconnecter</a>
    </div>
  </div>

  <h1 class="h1">Mon compte</h1>
  <p class="muted">Bonjour <strong><?= htmlspecialchars($username) ?></strong></p>

  <?php if (!empty($msg)): ?>
    <div class="alert ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="grid">
    <div class="card">
      <div class="card-body">
        <h3>Solde</h3>
        <p class="muted">Votre argent disponible</p>
        <div style="font-size:28px; font-weight:900; margin:10px 0;">
          <?= number_format($balance, 2) ?> €
        </div>

        <form method="POST">
          <label>Recharger (démo)</label>
          <input class="input" type="number" step="0.01" min="0.01" name="amount" placeholder="ex: 50" required>
          <div style="margin-top:12px;">
            <button class="btn primary" type="submit">Ajouter</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h3>Factures</h3>
        <p class="muted">Historique des commandes</p>

        <?php if (empty($invoices)): ?>
          <div class="alert">Aucune facture pour le moment.</div>
        <?php else: ?>
          <?php foreach ($invoices as $inv): ?>
            <div class="alert" style="display:flex; justify-content:space-between; gap:10px;">
              <div>#<?= (int)$inv["id"] ?></div>
              <div><strong><?= number_format((float)$inv["total_amount"], 2) ?> €</strong></div>
              <div class="muted"><?= htmlspecialchars($inv["created_at"] ?? "") ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>
</body>
</html>
