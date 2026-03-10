<?php
// Define a CLI environment check
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

// Include necessary configuration and models
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/User.php';

// A list of 50 creative usernames
$creativeNames = [
    'alice', 'bob', 'charlie', 'diana', 'eve', 'frank', 'grace', 'heidi', 'ivan', 'judy',
    'kyle', 'luna', 'mallory', 'nina', 'oscar', 'peggy', 'quinn', 'romeo', 'sybil', 'trent',
    'ursula', 'victor', 'walter', 'xander', 'yara', 'zane', 'arthur', 'bella', 'caleb', 'daphne',
    'edgar', 'fiona', 'george', 'hannah', 'ian', 'julia', 'kevin', 'lily', 'mason', 'nora',
    'oliver', 'piper', 'riley', 'sam', 'tara', 'vance', 'willow', 'aiden', 'chloe', 'ethan'
];

echo "Starting automatic account creation for " . count($creativeNames) . " users...\n";

foreach ($creativeNames as $username) {
    $email = $username . '@gmail.com';
    
    // Determine a random length between 8 and 32 characters
    $randomLength = rand(8, 32);
    
    // Generate a secure random password of that exact length
    // (bin2hex with 16 bytes creates a 32-character string, so we slice off the length we need)
    $password = substr(bin2hex(random_bytes(16)), 0, $randomLength);

    // Prevent duplicate entries
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    
    if ($stmt->fetch()) {
        echo "[-] User '{$username}' already exists. Skipping.\n";
        continue;
    }

    // Hash the password securely
    $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

    try {
        User::create($pdo, $username, $email, $passwordHash);
        echo "[+] Created: " . str_pad($username, 10) . " | Email: " . str_pad($email, 20) . " | Pass: {$password}\n";
    } catch (Exception $e) {
        echo "[!] Failed to create user {$username}: " . $e->getMessage() . "\n";
    }
}

echo "\nAccount creation complete.\n";