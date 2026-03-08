<?php

require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/AuthController.php";

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    AuthController::register($pdo);
}

?>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
    <title>TransactiWar</title>
</head>

<main class="container auth-container">
    <h2 class="page-title text-center">Register</h2>

    <?php
    if (!empty($_SESSION['error'])) {
        // Replaced inline style with our clean alert classes
        echo "<p class='alert alert-error'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>

    <div class="form-card mx-auto">
        <form method="POST">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>

        </form>

        <div class="auth-footer">
            <p><a href="/login" class="auth-link">Go to Login</a></p>
        </div>
    </div>
</main>