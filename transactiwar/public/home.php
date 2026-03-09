<?php
require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/HomeController.php";
require_once "../src/utils/sanitizer.php";

/* Ensure user is logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

try {
    $homeData = HomeController::getHomeData($pdo);
} catch (Exception $e) {
    die(Sanitizer::escape($e->getMessage()));
}

?>

<main class="container">
    
    <h2 class="welcome-text">
        Welcome to TransactiWar, <span><?= Sanitizer::escape($homeData['username']) ?>!</span>
    </h2>

    <div class="balance-card">
        <h3>Current Balance</h3>
        <p class="balance-amount">
            Rs. <?= Sanitizer::escape(number_format($homeData['balance'], 2)) ?>
        </p>
    </div>

</main>