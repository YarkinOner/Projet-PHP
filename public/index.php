<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$result = $mysqli->query("
  SELECT a.id, a.name, a.price, a.image_link, a.created_at, u.username
  FROM articles a
  JOIN users u ON u.id = a.author_id
  ORDER BY a.created_at DESC
");
$articles = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Accueil</title>
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
      <?php if (is_logged_in()): ?>
        <a class="btn" href="/php_exam/public/sell.php">Vendre</a>
        <a class="btn" href="/php_exam/public/cart.php">Panier</a>
        <a class="btn danger" href="/php_exam/public/logout.php">Se déconnecter</a>
      <?php else: ?>
        <a class="btn" href="/php_exam/public/login.php">Connexion</a>
        <a class="btn primary" href="/php_exam/public/register.php">Inscription</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- TITLE -->
  <h1 class="h1">Nos figurines</h1>
  <p class="muted">Découvrez les dernières figurines disponibles.</p>

  <br>

  <?php if (empty($articles)): ?>
    <div class="alert">
      Aucun article disponible pour le moment.
    </div>
  <?php else: ?>

    <div class="grid">
      <?php foreach ($articles as $a): ?>
        <div class="card">

          <?php if (!empty($a["image_link"])): ?>
            <img class="thumb"
                 src="<?= htmlspecialchars($a["image_link"]) ?>"
                 alt="<?= htmlspecialchars($a["name"]) ?>">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>

          <div class="card-body">
            <h3><?= htmlspecialchars($a["name"]) ?></h3>
            <p class="muted">
              Vendeur : <?= htmlspecialchars($a["username"]) ?>
            </p>

            <div class="row">
              <span class="price">
                <?= number_format((float)$a["price"], 2) ?> €
              </span>

              <a class="btn primary"
                 href="/php_exam/public/detail.php?id=<?= (int)$a["id"] ?>">
                Voir
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>

</body>
</html>
