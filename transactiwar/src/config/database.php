<?php

    $host = 'db'; 
    $dbname = getenv('POSTGRES_DB');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASS');
    // $user = getenv('POSTGRES_USER');
    // $password = getenv('POSTGRES_PASSWORD');

try {

    $pdo = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed");
}


