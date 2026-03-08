<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../utils/sanitizer.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/RateLimitter.php';


class AuthController {

    public static function register($pdo){

        verify_csrf();
        rotate_csrf();

        try {

            $webpage = $_SERVER['REQUEST_URI'];
            $ip      = $_SERVER['REMOTE_ADDR'];
            RateLimiter::checkAndRecord($pdo, '-', 'register_attempt', $webpage, $ip, 5, 60);

            $user = AuthService::register(
                $pdo,
                $_POST['username'],
                $_POST['email'],
                $_POST['password']
            );

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            ActivityLogger::db_log($pdo, "register_user");
            ActivityLogger::server_log(level: "INFO", eventType: "register_user");

            header("Location: /home");
            exit();

        } catch(Exception $e){
            $email = Sanitizer::escape($_POST["email"]) ?? "";
            $reason = Sanitizer::escape($e->getMessage());
            ActivityLogger::server_log(
                level: "WARN",
                eventType: "registration_failed",
                arguments: "email=$email, reason=$reason"
            );

            $_SESSION['error'] = $reason;
            header("Location: /register");
            exit();
        }
    }

}