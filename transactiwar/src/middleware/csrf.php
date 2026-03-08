<?php

require_once __DIR__ . '/../utils/logger.php';


/**
 * Verify the submitted CSRF token matches the session token.
 * Dies with 403 if the check fails.
 */
function verify_csrf(): void {
    if (!isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {

        ActivityLogger::server_log(
            level: "WARN",
            eventType: "csrf_failed",
        );

        http_response_code(403);
        die("CSRF validation failed");
    }
}

/**
 * Rotate the CSRF token after a successful state-changing POST.
 * Call this immediately after verify_csrf() in every controller action.
 * Ensures a leaked token cannot be replayed for the rest of the session.
 */
function rotate_csrf(): void {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}