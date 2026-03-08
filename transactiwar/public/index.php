<?php

header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'none'; style-src 'self' 'unsafe-inline';");

ob_start();

require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/utils/logger.php";

/* Security headers */
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains"); // Fix #11: enforce HTTPS
header("Referrer-Policy: strict-origin-when-cross-origin");               // Fix #15: stop path leaking in Referer
// X-XSS-Protection intentionally removed — deprecated and can introduce vulnerabilities in old IE

/* Prevent browser caching globally for sensitive data */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* CSRF token */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Idle session timeout: expire session after 30 minutes of inactivity */
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
    session_unset();
    session_destroy();
    header("Location: /login");
    exit();
}
$_SESSION['last_activity'] = time();

/* Get request path */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/favicon.ico') {
    http_response_code(204); // No Content
    exit();
}

/* Routes */
$routes = [
    '/' => 'home.php',
    '/home' => 'home.php',
    '/login' => 'login.php',
    '/register' => 'register.php',
    '/logout' => 'logout.php',
    '/profile' => 'profile.php',
    '/search' => 'search.php',
    '/pay' => 'pay.php',
    '/transactions' => 'transactions.php'
];

/* Public pages */
$publicRoutes = ['/login', '/register'];

/* If route does not exist */
if (!array_key_exists($uri, $routes)) {

    $username = $_SESSION['username'] ?? '-';

    ActivityLogger::server_log(
        level: "REDIRECT",
        eventType: "wrong_url",
        username: $username,
        webpage: $uri
    );

    if (isset($_SESSION['user_id'])) {
        header("Location: /home");
    } else {
        header("Location: /login");
    }

    exit();
}

/* Prevent accessing protected pages without login */
if (!isset($_SESSION['user_id']) && !in_array($uri, $publicRoutes)) {
    header("Location: /login");
    exit();
}

/* Prevent logged-in users from accessing login/register */
if (isset($_SESSION['user_id']) && in_array($uri, $publicRoutes)) {
    header("Location: /home");
    exit();
}

$username = $_SESSION['username'] ?? '-';

ActivityLogger::server_log(
    level: "INFO",
    eventType: "page_visit",
    username: $username,
    webpage: $uri
);

if (!in_array($uri, $publicRoutes) && $uri !== '/logout') {
    require_once 'header.php';
}

/* Load page */
require $routes[$uri];