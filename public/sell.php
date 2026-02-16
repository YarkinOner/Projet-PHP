<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_login();

$error = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price       = (float)($_POST["price"] ?? 0);
    $image_link  = trim($_POST["image_link"] ?? "");
    $stock_qty   = (int)($_POST["stock_qty"] ?? 0);

    if ($name === "" || $description === "" || $price <= 0 || $stock_qty < 0) {
        $error = "Veuillez remplir correctement le formulaire (prix > 0, stock ≥ 0).";
    } else {
        $author_id = (int)$_SESSION["user_id"];

        $mysqli->begin_transaction();
        try {
            $stmt = $mysqli->prepare("INSERT INTO articles (name, description, price, image_link, author_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $name, $description, $price, $image_link, $author_id);
            $stmt->execute();
            $article_id = $stmt->insert_id;

            $stmt2 = $mysqli->prepare("INSERT INTO stock (article_id, quantity) VALUES (?, ?)");
            $stmt2->bind_param("ii", $article_id, $stock_qty);
            $stmt2->execute();

            $mysqli->commit();
            $msg = "Article publié avec succès ✅";
        } catch (Throwable $e) {
            $mysqli->rollback();
            $error = "Impossible de publier l’article : " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Vendre</title>
  <link rel="stylesheet" href="/php_exam/public/assets/style.css">
</head>
<body>

<div class="container">

  <!-- HEADER -->
  <div class="nav">
    <div class="brand">
      <a href="/php_exam/public/" class="brand">FigurineStore</a>
      <span class="badge">E-commerce PHP</span>
    </div>

    <div class="navlinks">
      <a class="btn" href="/php_exam/public/">Accueil</a>
      <a class="btn" href="/php_exam/public/sell.php">Vendre</a>
      <a class="btn" href="/php_exam/public/cart.php">Panier</a>
      <a class="btn danger" href="/php_exam/public/logout.php">Se déconnecter</a>
    </div>
  </div>

  <h1 class="h1">Vendre une figurine</h1>
  <p class="muted">Publiez une nouvelle annonce en quelques secondes.</p>

  <?php if (!empty($msg)): ?>
    <div class="alert ok"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card form-card">
    <div class="card-body">
      <form method="POST">
        <label>Nom de l’article</label>
        <input class="input" name="name" required>

        <label>Description</label>
        <textarea class="input" name="description" required></textarea>

        <label>Prix (€)</label>
        <input class="input" name="price" type="number" step="0.01" required>

        <label>Lien de l’image (optionnel)</label>
        <input class="input" name="image_link" placeholder="https://...">

        <label>Quantité en stock</label>
        <input class="input" name="stock_qty" type="number" min="0" required>

        <div class="row" style="margin-top:12px;">
          <button class="btn primary" type="submit">Publier</button>
          <a class="btn" href="/php_exam/public/">Retour</a>
        </div>
      </form>
    </div>
  </div>

</div>

</body>
</html>
