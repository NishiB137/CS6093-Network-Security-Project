<?php

require_once __DIR__ . '/../models/RateLimit.php';

class RateLimiter {
    /**
     * Atomically records this action AND checks the count in one DB round-trip.
     * Throws if the limit is exceeded AFTER inserting, so the attempt is always
     * logged (useful for auditing brute-force attempts).
     */
    public static function checkAndRecord($pdo, $username, $action, $webpage, $ip, $limit, $seconds) {
        $count = RateLimit::insertAndCount($pdo, $username, $action, $webpage, $ip, $seconds);
        if ($count >= $limit) {
            throw new Exception("Too many requests. Please try again in $seconds seconds.");
        }
    }
}