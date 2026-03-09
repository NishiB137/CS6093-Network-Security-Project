<?php
require_once "../src/config/config.php";
require_once "../src/config/database.php"; 
require_once "../src/middleware/csrf.php";
require_once "../src/utils/logger.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Invalid request method");
}

verify_csrf();
rotate_csrf();

ActivityLogger::db_log($pdo, "logout");
ActivityLogger::server_log(level: "INFO", eventType: "logout");

// Destroy session
session_unset();
session_destroy();

// Delete cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect
header("Location: /login");
exit();