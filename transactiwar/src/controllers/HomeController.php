<?php

require_once __DIR__ . '/../models/User.php';

class HomeController {

    public static function getHomeData($pdo) {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        // Fetch the user's latest data from the database
        $user = User::findPublicById($pdo, $_SESSION['user_id']);

        if (!$user) {
            throw new Exception("User data could not be found.");
        }

        return [
            'username' => $user['username'],
            'balance'  => $user['balance']
        ];
    }
}