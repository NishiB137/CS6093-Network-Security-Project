<?php

class Validator {

    public static function require($value, $fieldName) {
        if ($value === null || trim($value) === '') {
            throw new Exception("$fieldName is required");
        }
    }

    public static function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

    }

    public static function username($username) {

        $username = trim($username);

        if (strlen($username) < 3 || strlen($username) > 30) {
            throw new Exception("Username must be 3–30 characters");
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception("Username may contain letters, numbers and underscore only");
        }
    }

    public static function password($password) {

        if (strlen($password) < 8 || strlen($password) > 72) {
            throw new Exception("Password must be between 8 and 72 characters");
        }
        if (preg_match('/\s/', $password)) {
            throw new Exception("Password cannot contain spaces.");
        }
    }
}