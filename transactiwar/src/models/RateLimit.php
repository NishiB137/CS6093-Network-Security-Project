<?php

class RateLimit {
    /**
     * Atomically inserts the action log and returns the count of recent
     * actions for this IP in one query — eliminates the check-then-record
     * race condition where two concurrent requests could both pass a
     * separate COUNT check before either INSERT runs.
     *
     * Returns the total number of actions (including this new one) within
     * the time window. The caller compares against the limit.
     */
    public static function insertAndCount($pdo, $username, $eventType, $webpage, $ip, $seconds) {
        $stmt = $pdo->prepare("
            WITH inserted AS (
                INSERT INTO activity_logs (username, event_type, webpage, ip_address)
                VALUES (:username, :event_type, :webpage, :ip)
            )
            SELECT COUNT(*) 
            FROM activity_logs
            WHERE ip_address  = :ip
              AND event_type  = :event_type
              AND timestamp  >= NOW() - (:seconds * INTERVAL '1 second')
        ");
        $stmt->execute([
            'username'   => $username,
            'event_type' => $eventType,
            'webpage'    => $webpage,
            'ip'         => $ip,
            'seconds'    => $seconds,
        ]);
        return (int) $stmt->fetchColumn();
    }
}