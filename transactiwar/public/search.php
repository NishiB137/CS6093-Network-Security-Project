<?php
require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/SearchController.php";
require_once "../src/utils/sanitizer.php";

/* Ensure user is logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$searchData = SearchController::handleSearch($pdo);
$searchQuery = $searchData['query'];
$users = $searchData['users'];
$rateLimitError = $searchData['error'];
?>

<h2 class="page-title">Search Users</h2>

<?php if (!empty($rateLimitError)): ?>
    <p class="alert alert-error"><?= Sanitizer::escape($rateLimitError) ?></p>
<?php endif; ?>

<?php
if (!empty($_SESSION['error'])) {
    echo "<p class='alert alert-error'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>

<form method="GET" action="/search" class="search-form">
    <input type="text" name="q" class="search-input" placeholder="Search exact username..." required value="<?= Sanitizer::escape($searchQuery) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="/search"><button type="button" class="btn btn-secondary">Clear</button></a>
</form>

<hr class="divider">

<?php if ($searchQuery === ''): ?>
    <p class="empty-state">Enter an exact username to search for a user.</p>
<?php elseif (empty($users) && empty($rateLimitError)): ?>
    <p class="empty-state">No user found with that exact username.</p>
<?php elseif (!empty($users)): ?>
    <ul class="user-list">
        <?php foreach ($users as $u): ?>
            <li class="user-card">
                <div class="user-card-content">
                    <?php if (!empty($u['profile_image_path'])): ?>
                        <img src="<?= Sanitizer::escape($u['profile_image_path']) ?>" alt="Photo" class="user-avatar">
                    <?php else: ?>
                        <div class="user-avatar-placeholder">No Pic</div>
                    <?php endif; ?>
                    
                    <div class="user-info">
                        <strong><?= Sanitizer::escape($u['username']) ?></strong><br>
                        <a href="/profile?username=<?= urlencode($u['username']) ?>" class="view-profile-link">View Profile</a>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
