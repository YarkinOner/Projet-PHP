<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_login();

$error = "";
$msg = "";

if (isset($_GET["deleted"])) {
    $msg = "Article supprimé ✅";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name        = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price       = (float)($_POST["price"] ?? 0);
    $stock_qty   = (int)($_POST["stock_qty"] ?? 0);

    $image_link = "";

    if ($name === "" || $description === "" || $price <= 0 || $stock_qty < 0) {
        $error = "Veuillez remplir correctement le formulaire (prix > 0, stock ≥ 0).";
    } else {

        // Image upload (optionnel)
        if (!empty($_FILES["image"]["name"])) {
            $allowedExt = ["jpg", "jpeg", "png", "webp"];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                $error = "Erreur lors de l’upload de l’image.";
            } elseif ($_FILES["image"]["size"] > $maxSize) {
                $error = "Image trop grande (max 5MB).";
            } else {
                $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt, true)) {
                    $error = "Format d’image non accepté (jpg, jpeg, png, webp).";
                } else {
                    $uploadDir = __DIR__ . "/uploads/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($_FILES["image"]["name"]));
                    $fileName = time() . "_" . $safeName;
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
                        $image_link = "/php_exam/public/uploads/" . $fileName;
                    } else {
                        $error = "Impossible d’enregistrer l’image.";
                    }
                }
            }
        }

        if ($error === "") {
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
}

// Mes annonces
$uid = (int)$_SESSION["user_id"];
$stMine = $mysqli->prepare("
  SELECT a.id, a.name, a.price, a.image_link, s.quantity
  FROM articles a
  LEFT JOIN stock s ON s.article_id = a.id
  WHERE a.author_id = ?
  ORDER BY a.id DESC
  LIMIT 30
");
$stMine->bind_param("i", $uid);
$stMine->execute();
$myArticles = $stMine->get_result()->fetch_all(MYSQLI_ASSOC);
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
      <form method="POST" enctype="multipart/form-data">
        <label>Nom de l’article</label>
        <input class="input" name="name" required>

        <label>Description</label>
        <textarea class="input" name="description" required></textarea>

        <label>Prix (€)</label>
        <input class="input" name="price" type="number" step="0.01" required>

        <label>Image de la figurine (optionnel)</label>
        <input class="input" type="file" name="image" accept="image/*">

        <label>Quantité en stock</label>
        <input class="input" name="stock_qty" type="number" min="0" required>

        <div class="row" style="margin-top:12px;">
          <button class="btn primary" type="submit">Publier</button>
          <a class="btn" href="/php_exam/public/">Retour</a>
        </div>
      </form>

      <p class="muted" style="margin-top:10px; font-size:12px;">
        Formats acceptés : jpg, jpeg, png, webp — Taille max : 5MB.
      </p>
    </div>
  </div>

  <h2 style="margin-top:18px;">Mes annonces</h2>
  <p class="muted">Vous pouvez supprimer vos articles si nécessaire.</p>

  <?php if (empty($myArticles)): ?>
    <div class="alert">Aucune annonce pour le moment.</div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($myArticles as $it): ?>
        <div class="card">
          <?php if (!empty($it["image_link"])): ?>
            <img class="thumb" src="<?= htmlspecialchars($it["image_link"]) ?>" alt="">
          <?php else: ?>
            <div class="thumb"></div>
          <?php endif; ?>

          <div class="card-body">
            <h3><?= htmlspecialchars($it["name"]) ?></h3>

            <div class="row">
              <div class="price"><?= number_format((float)$it["price"], 2) ?> €</div>
              <span class="badge">Stock : <?= (int)($it["quantity"] ?? 0) ?></span>
            </div>

            <div class="row" style="margin-top:12px;">
              <a class="btn" href="/php_exam/public/detail.php?id=<?= (int)$it["id"] ?>">Voir</a>

              <a class="btn danger"
                 href="/php_exam/public/delete_article.php?id=<?= (int)$it["id"] ?>"
                 onclick="return confirm('Supprimer cet article ?');">
                 Supprimer
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
