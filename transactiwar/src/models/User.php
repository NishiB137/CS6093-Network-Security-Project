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

    public static function incrementFailedAttempts($pdo, $userId) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET failed_attempts = failed_attempts + 1,
                last_failed_login = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("
            UPDATE users
            SET is_blocked = TRUE
            WHERE id = ? AND failed_attempts >= 5
        ");

        $stmt->execute([$userId]);
    }

    public static function resetFailedAttempts($pdo, $userId) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET failed_attempts = 0,
                last_failed_login = NULL,
                is_blocked = FALSE
            WHERE id = ?
        ");

        $stmt->execute([$userId]);
    }

    public static function findPublicById($pdo, $id) {
        $stmt = $pdo->prepare("
            SELECT id, username, email, balance, bio, profile_image_path, created_at
            FROM users WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}