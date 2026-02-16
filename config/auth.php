<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void {
    if (empty($_SESSION["user_id"])) {
        header("Location: /php_exam/public/login.php");
        exit;
    }
}

function is_logged_in(): bool {
    return !empty($_SESSION["user_id"]);
}
