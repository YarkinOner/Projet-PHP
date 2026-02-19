<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

$uid = (int)$_SESSION["user_id"];
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cart_id = (int)($_POST["cart_id"] ?? 0);
    $action  = $_POST["action"] ?? "";
    $newQty  = (int)($_POST["quantity"] ?? 1);

    $stmt = $mysqli->prepare("
        SELECT c.id, c.quantity, c.article_id, s.quantity AS stock_qty
        FROM cart c
        JOIN stock s ON s.article_id = c.article_id
        WHERE c.id = ? AND c.user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $cart_id, $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $stockQty = (int)$row["stock_qty"];

        if ($action === "update") {
            if ($newQty < 1) $newQty = 1;
            if ($newQty > $stockQty) {
                $error = "Stock insuffisant. Maximum : " . $stockQty;
            } else {
                $stU = $mysqli->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stU->bind_param("iii", $newQty, $cart_id, $uid);
                $stU->execute();
                $msg = "Quantité mise à jour ✅";
            }
        }

        if ($action === "delete") {
            $stD = $mysqli->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stD->bind_param("ii", $cart_id, $uid);
            $stD->execute();
            $msg = "Article supprimé du panier ✅";
        }
    }
}

$stmt = $mysqli->prepare("
    SELECT c.id AS cart_id, c.quantity,
           a.id AS article_id, a.name, a.price, a.image_link,
           s.quantity AS stock_qty
    FROM cart c
    JOIN articles a ON a.id = c.article_id
    JOIN stock s ON s.article_id = a.id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = 0.0;
foreach ($items as $it) {
    $total += ((float)$it["price"]) * ((int)$it["quantity"]);
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Panier</title>
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
      <a class="btn danger" href="/php_exam/public/logout.php">Se déconnecter</a>
    </div>
  </div>

  <h1 class="h1">Panier</h1>
  <p class="muted">Gérez vos articles avant de valider la commande.</p>

  <?php if (!empty($msg)): ?>
    <div class="alert ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <div class="alert">Votre panier est vide.</div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($items as $it): ?>
        <div class="card">

          <?php if (!empty($it["image_link"])): ?>
            <img class="thumb"
                 src="<?= htmlspecialchars($it["image_link"]) ?>"
                 alt="<?= htmlspecialchars($it["name"]) ?>">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>

          <div class="card-body">
            <h3>
              <a href="/php_exam/public/detail.php?id=<?= (int)$it["article_id"] ?>">
                <?= htmlspecialchars($it["name"]) ?>
              </a>
            </h3>

            <p class="muted">Stock : <?= (int)$it["stock_qty"] ?></p>

            <div class="row">
              <span class="price"><?= number_format((float)$it["price"], 2) ?> €</span>
              <span class="badge">x<?= (int)$it["quantity"] ?></span>
            </div>

            <div style="margin-top:10px;">
              <form method="POST" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="cart_id" value="<?= (int)$it["cart_id"] ?>">
                <input type="hidden" name="action" value="update">

                <input class="input"
                       style="max-width:140px;"
                       type="number"
                       name="quantity"
                       min="1"
                       max="<?= (int)$it["stock_qty"] ?>"
                       value="<?= (int)$it["quantity"] ?>">

                <button class="btn primary" type="submit">Mettre à jour</button>
              </form>

              <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="cart_id" value="<?= (int)$it["cart_id"] ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn danger" type="submit">Supprimer</button>
              </form>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

    <div class="card" style="margin-top:16px;">
      <div class="card-body">
        <div class="row">
          <div>
            <div class="muted">Total</div>
            <div style="font-size:22px; font-weight:900;"><?= number_format($total, 2) ?> €</div>
          </div>
          <a class="btn primary" href="/php_exam/public/validate.php">Valider la commande</a>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

</body>
</html>
