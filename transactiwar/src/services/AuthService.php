<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/validator.php';

class AuthService {

    public static function register($pdo, $username, $email, $password){

        $username = trim($username);
        $email = strtolower(trim($email));

        Validator::require($username, "Username");
        Validator::require($email, "Email");
        Validator::require($password, "Password");

        Validator::username($username);
        Validator::email($email);
        Validator::password($password);

        if(User::findByEmail($pdo, $email)){
            throw new Exception("Email already registered");
        }

        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        User::create($pdo, $username, $email, $passwordHash);

        return User::findByEmail($pdo, $email);
    }


    public static function login($pdo, $email, $password) {

        $email = strtolower(trim($email));

        Validator::require($email, "Email");
        Validator::require($password, "Password");
        Validator::email($email);

        $user = User::findByEmail($pdo, $email);

        if (!$user) {
            throw new Exception("Invalid email or password");
        }
        
        if ($user['is_blocked']) {

            $lastFailed = strtotime($user['last_failed_login']);
            $now = time();

            // 5 minutes = 300 seconds
            if (($now - $lastFailed) > 300) {

                // unblock user
                User::resetFailedAttempts($pdo, $user['id']);

                // refresh user data
                $user = User::findByEmail($pdo, $email);

            } else {
                throw new Exception("Account temporarily blocked. Try again later.");
            }
        }

        if (strlen($password) < 8 || strlen($password) > 72 || !password_verify($password, $user['password_hash'])) {

            User::incrementFailedAttempts($pdo, $user['id']);
            throw new Exception("Invalid email or password");
        }

        User::resetFailedAttempts($pdo, $user['id']);

        return $user;
    }



}