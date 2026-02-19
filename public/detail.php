<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    die("Article invalide.");
}

$stmt = $mysqli->prepare("SELECT a.*, u.username FROM articles a JOIN users u ON u.id = a.author_id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    die("Article introuvable.");
}

$stmt2 = $mysqli->prepare("SELECT quantity FROM stock WHERE article_id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stock = $stmt2->get_result()->fetch_assoc();
$qty = $stock ? (int)$stock["quantity"] : 0;

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_login();

    $addQty = (int)($_POST["quantity"] ?? 1);
    if ($addQty < 1) $addQty = 1;

    $uid = (int)$_SESSION["user_id"];

    $stmtC = $mysqli->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND article_id = ? LIMIT 1");
    $stmtC->bind_param("ii", $uid, $id);
    $stmtC->execute();
    $existing = $stmtC->get_result()->fetch_assoc();

    $currentQty = $existing ? (int)$existing["quantity"] : 0;
    $newQty = $currentQty + $addQty;

    if ($qty <= 0) {
        $err = "Rupture de stock ❌";
    } elseif ($newQty > $qty) {
        $err = "Stock insuffisant. Maximum : " . $qty;
    } else {
        if ($existing) {
            $stmtU = $mysqli->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmtU->bind_param("ii", $newQty, $existing["id"]);
            $stmtU->execute();
        } else {
            $stmtI = $mysqli->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (?, ?, ?)");
            $stmtI->bind_param("iii", $uid, $id, $addQty);
            $stmtI->execute();
        }
        $msg = "Ajouté au panier ✅";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Détail</title>
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

  <h1 class="h1">Détail de l’article</h1>

  <?php if ($msg): ?>
    <div class="alert ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if ($err): ?>
    <div class="alert error"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <div class="card" style="margin-top:14px;">
    <div style="display:grid; grid-template-columns: 1.15fr 1fr; gap:14px; padding:14px;">

      <div>
        <?php if (!empty($article["image_link"])): ?>
          <img class="thumb-lg"
               src="<?= htmlspecialchars($article["image_link"]) ?>"
               alt="<?= htmlspecialchars($article["name"]) ?>">
        <?php else: ?>
          <div class="thumb-lg"></div>
        <?php endif; ?>
      </div>

      <div>
        <h2><?= htmlspecialchars($article["name"]) ?></h2>

        <p class="muted">
          Vendeur : <strong><?= htmlspecialchars($article["username"]) ?></strong>
        </p>

        <div class="row">
          <div>
            <div class="muted">Prix</div>
            <div style="font-size:22px; font-weight:900;">
              <?= number_format((float)$article["price"], 2) ?> €
            </div>
          </div>
          <span class="badge">Stock : <?= $qty ?></span>
        </div>

        <div class="alert">
          <?= nl2br(htmlspecialchars($article["description"])) ?>
        </div>

        <form method="POST">
          <label>Quantité</label>
          <div class="row">
            <input class="input" style="max-width:160px;" type="number"
                   name="quantity" min="1" max="<?= $qty ?>" value="1">
            <button class="btn primary">Ajouter au panier</button>
          </div>
        </form>

        <div style="margin-top:14px;">
          <a class="btn" href="/php_exam/public/">← Retour</a>
        </div>
      </div>

    </div>
  </div>

</div>

</body>
</html>
