<?php

require_once __DIR__ . '/../services/SearchService.php';
require_once __DIR__ . '/../utils/sanitizer.php';

class SearchController {

    public static function handleSearch($pdo) {
        
        $rawInput = $_GET['q'] ?? '';
        if (!is_string($rawInput)) {
            $rawInput = '';
            $_SESSION["error"] = "Invalid search query";
            header("Location: /search");

            exit();
        }
        $searchQuery = trim($rawInput);
        // validate search query
       
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $result = [
            'query' => $searchQuery,
            'users' => [],
            'error' => ''
        ];

        if ($searchQuery !== '') {
            try {
                $result['users'] = SearchService::performSearch($pdo, $searchQuery, $ipAddress);
            } catch (Exception $e) {
                $_SESSION["error"] = Sanitizer::escape($e->getMessage() ??"");
                header("Location: /search");

                exit();
            }
        }

        return $result;
    }
}