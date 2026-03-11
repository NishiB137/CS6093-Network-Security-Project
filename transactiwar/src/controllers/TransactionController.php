<?php

require_once __DIR__ . '/../services/TransactionService.php';
require_once __DIR__ . '/../utils/RateLimitter.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../utils/sanitizer.php';
require_once __DIR__ . '/../utils/logger.php';

class TransactionController {

    public static function pay($pdo) {
        verify_csrf();
        rotate_csrf();

        try {

            $webpage = $_SERVER['REQUEST_URI'];
            $ip      = $_SERVER['REMOTE_ADDR'];
            RateLimiter::checkAndRecord($pdo, $_SESSION['username'], 'transfer_attempt', $webpage, $ip, 5, 60);

    

            TransactionService::processTransfer(
                $pdo,
                $_SESSION['user_id'],
                $_POST['receiver_username'] ?? '',
                $_POST['amount'] ?? '',
                $_POST['comment'] ?? ''
            );

            $_SESSION['success'] = Sanitizer::escape("Transfer successful!");

            
            ActivityLogger::db_log($pdo, "transfer_success");
            ActivityLogger::server_log(
                level: "INFO", 
                eventType: "transfer_success",
                arguments: "to={$_POST['receiver_username']}, amount={$_POST['amount']}"
            );


            header("Location: /pay");
            exit();

        } catch (Exception $e) {
            $reason = Sanitizer::escape($e->getMessage());
            
            ActivityLogger::server_log(
                level: "ERROR", 
                eventType: "transfer_failed", 
                arguments: "reason=$reason, to={$_POST['receiver_username']}, amount={$_POST['amount']}"
            );

            $_SESSION['error'] = Sanitizer::escape($e->getMessage());
            header("Location: /pay");
            exit();
        }
    }

    public static function history($pdo) {
        try {
            $transactions = TransactionService::getUserTransactions($pdo, $_SESSION['user_id']);
            $user = User::findPublicById($pdo, $_SESSION['user_id']);
            
            return [
                'transactions' => $transactions,
                'balance' => $user['balance'] ?? 0.00
            ];
        } catch (Exception $e) {
            return [
                'transactions' => [],
                'balance' => 0.00
            ];
        }
    }
}