<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/RateLimitter.php';


class SearchService {

    public static function performSearch($pdo, $searchQuery, $ipAddress) {
        $searchQuery = trim($searchQuery);
        
        if ($searchQuery === '') {
            return [];
        }

        if (!preg_match('/^[a-zA-Z0-9_]{1,32}$/', $searchQuery)) {
            throw new Exception("Invalid search query");
            return [];
        }

        // Atomically record and check: 15 searches per 10 seconds
        $webpage = $_SERVER['REQUEST_URI'] ?? '/search';
        RateLimiter::checkAndRecord($pdo, $_SESSION['username'] ?? '-', 'search', $webpage, $ipAddress, 15, 60);


        // Log this search attempt
        ActivityLogger::server_log(
            level: "INFO",
            eventType: "search",
            arguments: "person=$searchQuery"
        );
        
        // Call the User model for exact match search
        $results = User::searchExactUsername($pdo, $searchQuery);


        return $results;
    }

    public static function getAllUsers($pdo) {
        return User::getAllUsers($pdo);
    }
}