<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

require_login();

$uid = (int)$_SESSION["user_id"];
$id  = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Article invalide.");
}

$stmt = $mysqli->prepare("SELECT id, author_id FROM articles WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if (!$article) {
    die("Article introuvable.");
}

if ((int)$article["author_id"] !== $uid) {
    http_response_code(403);
    die("Accès refusé.");
}

$mysqli->begin_transaction();
try {
    $s1 = $mysqli->prepare("DELETE FROM cart WHERE article_id = ?");
    $s1->bind_param("i", $id);
    $s1->execute();

    $s2 = $mysqli->prepare("DELETE FROM stock WHERE article_id = ?");
    $s2->bind_param("i", $id);
    $s2->execute();

    $s3 = $mysqli->prepare("DELETE FROM articles WHERE id = ?");
    $s3->bind_param("i", $id);
    $s3->execute();

    $mysqli->commit();

    header("Location: /php_exam/public/sell.php?deleted=1");
    exit;
} catch (Throwable $e) {
    $mysqli->rollback();
    die("Erreur: " . $e->getMessage());
}
