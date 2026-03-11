<?php

class Transaction {

    public static function transfer($pdo, $senderId, $receiverId, $amount, $comment) {
        try {
            // Begin atomic transaction
            $pdo->beginTransaction();

            // 1. Lock the sender's row and check balance (Prevents Race Conditions)
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :sender_id FOR UPDATE");
            $stmt->execute(['sender_id' => $senderId]);
            $sender = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sender || $sender['balance'] < $amount) {
                throw new Exception("Insufficient balance to complete the transfer.");
            }

            // 2. Deduct from sender
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE id = :sender_id");
            $stmt->execute(['amount' => $amount, 'sender_id' => $senderId]);

            // 3. Add to receiver
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :receiver_id");
            $stmt->execute(['amount' => $amount, 'receiver_id' => $receiverId]);

            // 4. Record transaction log
            $stmt = $pdo->prepare("
                INSERT INTO transactions (sender_id, receiver_id, amount, comment) 
                VALUES (:sender_id, :receiver_id, :amount, :comment)
            ");
            $stmt->execute([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'amount' => $amount,
                'comment' => $comment
            ]);

            // Commit transaction
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e; 
        }
    }

    public static function getHistoryByUserId($pdo, $userId) {
        $stmt = $pdo->prepare("
            SELECT t.id, t.amount, t.comment, t.created_at, t.sender_id, t.receiver_id,
                   s.username as sender_name, 
                   r.username as receiver_name 
            FROM transactions t
            JOIN users s ON t.sender_id = s.id
            JOIN users r ON t.receiver_id = r.id
            WHERE t.sender_id = :user_id OR t.receiver_id = :user_id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}