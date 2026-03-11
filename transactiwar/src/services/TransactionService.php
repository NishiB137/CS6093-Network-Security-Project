<?php

require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/validator.php';

class TransactionService {

    public static function processTransfer($pdo, $senderId, $receiverUsername, $amount, $comment) {
        
        Validator::require($receiverUsername, "Receiver Username");
        Validator::require($amount, "Amount");

        // 1. STRICT USERNAME VALIDATION (Prevents SQLi/Malicious strings bypassing the UI)
        if (!preg_match('/^[a-zA-Z0-9_]{1,30}$/', $receiverUsername)) {
            throw new Exception("Invalid receiver username format.");
        }

        // 2. STRICT AMOUNT VALIDATION — regex enforces plain decimal format only.
        // is_numeric() also accepts scientific notation (e.g. 1e2 = 100) and hex (0x1A),
        // which could confuse downstream formatting or logging. Only allow digits with
        // an optional dot and up to 2 decimal places.
        $amount = round((float) $amount, 2);
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount) || (float)$amount <= 0 || (float)$amount > 10000000) {
            throw new Exception("Invalid amount. Must be a positive number with up to 2 decimal places.");
        }
        $amount = (float)$amount;

        // 3. COMMENT SANITIZATION & LENGTH CHECK
        $comment = trim($comment);
        if (strlen($comment) > 255) {
            throw new Exception("Comment exceeds the maximum allowed length of 255 characters.");
        }
        // Strip out HTML/PHP tags completely from the backend as an extra layer of defense
        $comment = strip_tags($comment);

        // Look up the receiver by their exact username (public fields only — no password hash needed)
        $receiver = User::findPublicByUsername($pdo, $receiverUsername);
        if (!$receiver) {
            throw new Exception("Receiver not found with that username.");
        }

        $receiverId = $receiver['id'];

        // Prevent self-transfer
        if ($senderId == $receiverId) {
            throw new Exception("You cannot transfer money to yourself.");
        }

        // Check if sender has enougamounth money (Model handles the FOR UPDATE lock)
        Transaction::transfer($pdo, $senderId, $receiverId, $amount, $comment);
    }

    public static function getUserTransactions($pdo, $userId) {
        return Transaction::getHistoryByUserId($pdo, $userId);
    }
}