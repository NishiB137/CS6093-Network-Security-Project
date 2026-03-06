<?php
// Retrieve database credentials from the Docker environment
$host = 'db'; // matches the service name in docker-compose.yml
$db   = getenv('POSTGRES_DB');
$user = getenv('POSTGRES_USER');
$pass = getenv('POSTGRES_PASSWORD');

try {
    // Attempt to connect using raw PDO (PHP Data Objects)
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Fetch the test message
    $stmt = $pdo->query("SELECT message FROM test_connection LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<h1>Environment Status: SUCCESS</h1>";
    echo "<p>Database says: <strong>" . htmlspecialchars($result['message']) . "</strong></p>";

} catch (PDOException $e) {
    echo "<h1>Environment Status: FAILED</h1>";
    echo "<p>Connection error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>