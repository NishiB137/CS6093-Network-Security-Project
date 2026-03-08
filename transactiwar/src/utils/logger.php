<?php

require_once __DIR__ . '/../models/Log.php';

class ActivityLogger {

    public static function db_log($pdo, $eventType = "page_visit") {

        if (!isset($_SESSION['username'])) {
            return; // don't log guest activity
        }

        $username = $_SESSION['username'];
        $webpage  = $_SERVER['REQUEST_URI'];
        $ip       = $_SERVER['REMOTE_ADDR'];

        // call the Log model to save the log entry
        Log::create($pdo, $username, $eventType, $webpage, $ip);
    }

    public static function server_log(
        $level = "INFO",
        $eventType = "page_visit",
        $username = null,
        $webpage = null,
        $ip = null,
        $arguments = null
    ) {

        $username = $username ?? ($_SESSION['username'] ?? "guest");
        $webpage  = $webpage  ?? ($_SERVER['REQUEST_URI'] ?? "");
        $ip       = $ip       ?? ($_SERVER['REMOTE_ADDR'] ?? "unknown");
        $method = ($_SERVER['REQUEST_METHOD'] ?? "unknown");
        
        $userAgentRaw = $_SERVER['HTTP_USER_AGENT'] ?? "unknown";
        $userAgent = strlen($userAgentRaw) > 50 ? substr($userAgentRaw, 0, 47) . '...' : $userAgentRaw;

        $timestamp = date('Y-m-d H:i:s');

        // Base log line
        $logEntry = "[$level] [$timestamp] $eventType | method=$method | page=$webpage | user=$username | ip=$ip| ua=\"$userAgent\"";

        // Append custom arguments
        if ($arguments !== null && $arguments !== "") {
            $logEntry .= " | $arguments";
        }

        $logEntry .= PHP_EOL;

        $logFile = __DIR__ . '/../../logs/server.log';

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
