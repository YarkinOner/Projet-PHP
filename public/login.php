<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login    = trim($_POST["login"] ?? "");
    $password = (string)($_POST["password"] ?? "");

    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user["password"])) {
        $error = "Identifiants incorrects.";
    } else {
        $_SESSION["user_id"] = (int)$user["id"];
        header("Location: /php_exam/public/index.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Connexion</title>
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

    <h1 class="h1">Connexion</h1>
    <p class="muted">Connectez-vous pour acheter des figurines.</p>

    <?php if (!empty($error)): ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card form-card">
      <div class="card-body">
        <form method="POST">
          <label>Nom d’utilisateur ou e-mail</label>
          <input class="input" name="login" required>

          <label>Mot de passe</label>
          <input class="input" name="password" type="password" required>

          <div class="row">
            <button class="btn primary" type="submit">Se connecter</button>
            <a class="btn" href="/php_exam/public/register.php">Créer un compte</a>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
