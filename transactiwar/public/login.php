<?php

require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/AuthController.php";

/* Generate CSRF token if not exists */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Handle POST login */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::login($pdo);
}

?>

<head>
    <link rel="stylesheet" href="assets/css/style.css">
    <title>TransactiWar</title>
</head>

<main class="container auth-container">
    <h2 class="page-title text-center">Login</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="alert alert-error"><?= Sanitizer::escape($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="form-card mx-auto">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="/register" class="auth-link">Sign Up</a></p>
        </div>
    </div>
</main>