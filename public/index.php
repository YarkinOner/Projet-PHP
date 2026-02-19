<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$stmt = $mysqli->query("
    SELECT a.*, u.username, s.quantity
    FROM articles a
    JOIN users u ON u.id = a.author_id
    LEFT JOIN stock s ON s.article_id = a.id
    ORDER BY a.id DESC
    LIMIT 30
");

$articles = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>GameRelic</title>
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

      <?php if (is_logged_in()): ?>
        <a class="btn" href="/php_exam/public/sell.php">Vendre</a>
        <a class="btn" href="/php_exam/public/cart.php">Panier</a>
        <a class="btn" href="/php_exam/public/account.php">Compte</a>
        <a class="btn danger" href="/php_exam/public/logout.php">Se déconnecter</a>
      <?php else: ?>
        <a class="btn" href="/php_exam/public/login.php">Connexion</a>
        <a class="btn primary" href="/php_exam/public/register.php">Inscription</a>
      <?php endif; ?>
    </div>
  </div>

  <h1 class="h1">Nos figurines</h1>
  <p class="muted">Découvrez les dernières figurines disponibles.</p>

  <div class="grid" style="margin-top:18px;">
    <?php foreach ($articles as $article): ?>
      <div class="card">
        <?php if (!empty($article["image_link"])): ?>
          <img class="thumb"
               src="<?= htmlspecialchars($article["image_link"]) ?>"
               alt="<?= htmlspecialchars($article["name"]) ?>">
        <?php else: ?>
          <div class="thumb"></div>
        <?php endif; ?>

        <div class="card-body">
          <h3><?= htmlspecialchars($article["name"]) ?></h3>

          <p class="muted">
            Vendeur : <?= htmlspecialchars($article["username"]) ?>
          </p>

          <div class="row">
            <div class="price">
              <?= number_format((float)$article["price"], 2) ?> €
            </div>

            <a class="btn primary"
               href="/php_exam/public/detail.php?id=<?= (int)$article["id"] ?>">
               Voir
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

</body>
</html>
