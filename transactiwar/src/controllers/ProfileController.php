<?php

require_once __DIR__ . '/../services/ProfileService.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../utils/sanitizer.php';
require_once __DIR__ . '/../utils/logger.php';

class ProfileController {

    public static function update($pdo){

        verify_csrf();
        rotate_csrf();

        try {

            ProfileService::updateProfile(
                $pdo,
                $_SESSION['user_id'],
                $_POST['bio'] ?? '',
                $_FILES['photo'] ?? null
            );

            $_SESSION['success'] = Sanitizer::escape("Profile updated successfully!");
            $username=$_SESSION["username"];

            // Log the profile update
            ActivityLogger::db_log($pdo, "profile_update_success");
            ActivityLogger::server_log(
                level: "INFO",
                eventType: "profile_update_success",
                username: $username,
                webpage: "/profile"
            );


            header("Location: /profile");
            exit();

        } catch(Exception $e){

            $reason = Sanitizer::escape($e->getMessage());
            $username=$_SESSION["username"];

            // Log failure
            ActivityLogger::server_log(
                level: "ERROR",
                eventType: "profile_update_failed",
                username: $username,
                webpage: "/profile",
                arguments: "reason=$reason"
            );

            $_SESSION['error'] = Sanitizer::escape($e->getMessage());
            header("Location: /profile");
            exit();
        }
    }

    public static function view($pdo) {
        
        $rawUsername = $_GET['username'] ?? $_SESSION['username'];
        
        if(!is_string($rawUsername)){
            $rawUsername = '';
            header("Location: /profile");
            exit();
        }

        $viewUsername = trim($rawUsername);
        $isOwnProfile = ($viewUsername === $_SESSION['username']);
        
        try {
            $user = ProfileService::getProfile($pdo, $viewUsername);
            return [
                'user' => $user,
                'isOwnProfile' => $isOwnProfile,
                'error' => null
            ];
        } catch (Exception $e) {
            return [
                'user' => null,
                'isOwnProfile' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}