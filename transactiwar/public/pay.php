<?php
require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/TransactionController.php";
require_once "../src/utils/sanitizer.php";

/* Ensure user is logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

/* Generate CSRF token if not exists */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    TransactionController::pay($pdo);
}

// Fetch the GET parameter
$prefillReceiverUsername = $_GET['receiver_username'] ?? '';

// STRICT VALIDATION: If the username contains anything other than letters, numbers, 
// or underscores, or is longer than 30 characters (like a SQL query), wipe it out.
if ($prefillReceiverUsername !== '' && !preg_match('/^[a-zA-Z0-9_]{1,30}$/', $prefillReceiverUsername)) {
    $prefillReceiverUsername = ''; 
}

?>

<h2 class="page-title">Pay / Transfer Money</h2>

<?php
if (!empty($_SESSION['error'])) {
    echo "<p class='alert alert-error'>" . Sanitizer::escape($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
if (!empty($_SESSION['success'])) {
    echo "<p class='alert alert-success'>" . Sanitizer::escape($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
?>

<div class="form-card">
    <form method="POST" action="/pay" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-group">
            <label class="form-label">Receiver Username</label>
            <input type="text" name="receiver_username" class="form-control" required autocomplete="off" value="<?= Sanitizer::escape($prefillReceiverUsername) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Amount (Rs.)</label>
            <input type="number" step="0.01" min="0.01" max="1000000" name="amount" class="form-control" required autocomplete="off">
        </div>

        <div class="form-group">
            <label class="form-label">Comment (Optional)</label>
            <textarea name="comment" rows="3" class="form-control" maxlength="255" autocomplete="off"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Transfer</button>
    </form>
</div>