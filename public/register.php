<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = (string)($_POST["password"] ?? "");

    if ($username === "" || $email === "" || $password === "") {
        $error = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = "Ce nom d’utilisateur ou cette adresse e-mail est déjà utilisé(e).";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt2 = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt2->bind_param("sss", $username, $email, $hash);
            $stmt2->execute();

            $_SESSION["user_id"] = (int)$stmt2->insert_id;
            header("Location: /php_exam/public/index.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="/php_exam/public/assets/style.css">
</head>
<body>

  <div class="container">
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

    <h1 class="h1">Inscription</h1>
    <p class="muted">Créez un compte pour vendre et acheter des figurines.</p>

    <?php if (!empty($error)): ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card form-card">
      <div class="card-body">
        <form method="POST">
          <label>Nom d’utilisateur</label>
          <input class="input" name="username" required>

          <label>Adresse e-mail</label>
          <input class="input" name="email" type="email" required>

          <label>Mot de passe</label>
          <input class="input" name="password" type="password" required>

          <div class="row">
            <button class="btn primary" type="submit">Créer un compte</button>
            <a class="btn" href="/php_exam/public/login.php">J’ai déjà un compte</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
