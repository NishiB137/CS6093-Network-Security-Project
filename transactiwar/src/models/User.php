<?php

class User {

    public static function findByEmail($pdo, $email){

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = :email"
        );

        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $username, $email, $passwordHash){

        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password_hash)
             VALUES (:username, :email, :password_hash)"
        );

        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash
        ]);
    }

}