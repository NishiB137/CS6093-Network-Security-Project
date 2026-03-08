<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

ini_set('expose_php', 0);

date_default_timezone_set('Asia/Kolkata');

ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600*12);

session_set_cookie_params([
    'lifetime' => 3600*12,
    'path' => '/',
    'secure' => true, 
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}