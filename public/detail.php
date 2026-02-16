<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Article invalide.");

$stmt = $mysqli->prepare("SELECT a.*, u.username FROM articles a JOIN users u ON u.id = a.author_id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
if (!$article) die("Article introuvable.");

$stmt2 = $mysqli->prepare("SELECT quantity FROM stock WHERE article_id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stock = $stmt2->get_result()->fetch_assoc();
$qty = $stock ? (int)$stock["quantity"] : 0;

// Ajouter au panier
$msg = "";
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

    if ($newQty > $qty) {
        $msg = "Stock insuffisant. Maximum : " . $qty;
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
</head>
<body>
  <p><a href="/php_exam/public/index.php">← Retour à l’accueil</a></p>

  <h1><?= htmlspecialchars($article["name"]) ?></h1>
  <p>Vendeur : <?= htmlspecialchars($article["username"]) ?></p>
  <p>Prix : <?= number_format((float)$article["price"], 2) ?> €</p>
  <p>Stock : <?= $qty ?></p>

  <p><?= nl2br(htmlspecialchars($article["description"])) ?></p>

  <?php if (!empty($article["image_link"])): ?>
    <img src="<?= htmlspecialchars($article["image_link"]) ?>" alt="" style="max-width:350px;">
  <?php endif; ?>

  <?php if (!empty($msg)): ?>
    <p style="color:green;"><?= htmlspecialchars($msg) ?></p>
  <?php endif; ?>

  <?php if (is_logged_in()): ?>
    <?php if ($qty > 0): ?>
      <form method="POST" style="margin:12px 0;">
        <label>Quantité :</label>
        <input type="number" name="quantity" min="1" max="<?= $qty ?>" value="1">
        <button type="submit">Ajouter au panier</button>
      </form>
      <p><a href="/php_exam/public/cart.php">Voir mon panier</a></p>
    <?php else: ?>
      <p style="color:red;">Rupture de stock ❌</p>
    <?php endif; ?>
  <?php else: ?>
    <p>Pour ajouter au panier, <a href="/php_exam/public/login.php">connectez-vous</a>.</p>
  <?php endif; ?>
</body>
</html>
