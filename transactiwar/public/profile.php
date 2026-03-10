<?php
require_once "../src/config/config.php";
require_once "../src/config/database.php";
require_once "../src/controllers/ProfileController.php";
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

$profileData = ProfileController::view($pdo);

if ($profileData['error']) {
    die(Sanitizer::escape($profileData['error']));
}

$user = $profileData['user'];
$isOwnProfile = $profileData['isOwnProfile'];

/* Handle POST request for Updates */
if ($isOwnProfile && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ProfileController::update($pdo);
}
?>

<main class="container profile-container mx-auto">
    <h2 class="page-title text-center">Profile: <?= Sanitizer::escape($user['username']) ?></h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="alert alert-error"><?= Sanitizer::escape($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['success'])): ?>
        <p class="alert alert-success"><?= Sanitizer::escape($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="profile-header">
        <?php if (!empty($user['profile_image_path'])): ?>
            <img src="<?= Sanitizer::escape($user['profile_image_path']) ?>" alt="Profile Photo" class="profile-photo">
        <?php else: ?>
            <div class="profile-photo-placeholder">
                <span>No Image</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isOwnProfile): ?>
        <div class="user-details-card">
            <p><strong>Username:</strong> <?= Sanitizer::escape($user['username']) ?></p>
            <p><strong>Email:</strong> <?= Sanitizer::escape($user['email']) ?></p>
        </div>

        <div class="form-card mx-auto">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label class="form-label">Update Profile Picture (Max 2MB, 2000x2000px)</label>
                    <input type="file" name="photo" accept="image/png, image/jpeg, image/gif" class="form-control file-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" rows="4" class="form-control" maxlength="1000"><?= Sanitizer::escape($user['bio'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </form>
        </div>
    <?php else: ?>
        <div class="bio-section">
            <?php 
                // Explicitly check if the bio is an empty string or just spaces
                $displayBio = (!empty($user['bio']) && trim($user['bio']) !== '') ? $user['bio'] : 'No bio provided.';
            ?>
            <p class="bio-label"><strong>Bio:</strong></p>
            <p class="bio-text"><?= nl2br(Sanitizer::escape($displayBio)) ?></p>
        </div>
    <?php endif; ?>
</main>