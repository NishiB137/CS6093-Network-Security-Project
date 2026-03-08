<?php

class Log {

    public static function create($pdo, $username, $eventType, $webpage, $ip) {

        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (username, event_type, webpage, ip_address)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$username, $eventType, $webpage, $ip]);
    }

}