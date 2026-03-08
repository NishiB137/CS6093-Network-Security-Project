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


}