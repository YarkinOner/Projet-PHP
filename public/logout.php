<?php
require_once __DIR__ . "/../config/auth.php";

session_destroy();
header("Location: /php_exam/public/login.php");
exit;
