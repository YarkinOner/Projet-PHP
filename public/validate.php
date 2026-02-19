<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

$uid = (int)$_SESSION["user_id"];
$error = "";
$msg = "";

$stmt = $mysqli->prepare("
  SELECT c.article_id, c.quantity, a.price, s.quantity AS stock_qty
  FROM cart c
  JOIN articles a ON a.id = c.article_id
  JOIN stock s ON s.article_id = a.id
  WHERE c.user_id = ?
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($items)) {
  $error = "Votre panier est vide.";
} else {
  $total = 0.0;
  foreach ($items as $it) $total += ((float)$it["price"]) * ((int)$it["quantity"]);

  $stU = $mysqli->prepare("SELECT balance FROM users WHERE id = ?");
  $stU->bind_param("i", $uid);
  $stU->execute();
  $u = $stU->get_result()->fetch_assoc();
  $balance = $u ? (float)$u["balance"] : 0.0;

  if ($balance < $total) {
    $error = "Solde insuffisant. Total: " . number_format($total,2) . " €";
  } else {
    $mysqli->begin_transaction();
    try {
      foreach ($items as $it) {
        $need = (int)$it["quantity"];
        $have = (int)$it["stock_qty"];
        if ($have < $need) {
          throw new Exception("Stock insuffisant pour un article.");
        }
        $newStock = $have - $need;
        $stS = $mysqli->prepare("UPDATE stock SET quantity = ? WHERE article_id = ?");
        $stS->bind_param("ii", $newStock, $it["article_id"]);
        $stS->execute();
      }

      $newBalance = $balance - $total;
      $stB = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
      $stB->bind_param("di", $newBalance, $uid);
      $stB->execute();

      $stI = $mysqli->prepare("
        INSERT INTO invoice (user_id, total_amount, billing_address, billing_city, billing_postal_code)
        VALUES (?, ?, 'Adresse démo', 'Paris', '75000')
      ");
      $stI->bind_param("id", $uid, $total);
      $stI->execute();

      $stC = $mysqli->prepare("DELETE FROM cart WHERE user_id = ?");
      $stC->bind_param("i", $uid);
      $stC->execute();

      $mysqli->commit();
      $msg = "Commande validée ✅ Nouveau solde : " . number_format($newBalance,2) . " €";
    } catch (Throwable $e) {
      $mysqli->rollback();
      $error = "Erreur: " . $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Validation</title>
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
      <a class="btn" href="/php_exam/public/cart.php">Panier</a>
      <a class="btn" href="/php_exam/public/account.php">Compte</a>
    </div>
  </div>

  <h1 class="h1">Validation de commande</h1>

  <?php if ($error): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($msg): ?>
    <div class="alert ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <a class="btn primary" href="/php_exam/public/account.php">Voir mon solde</a>
</div>
</body>
</html>
