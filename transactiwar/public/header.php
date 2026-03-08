<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>TransactiWar</title>
</head>
<body>

    <header>
        <nav>
            <a href="/home">Home</a>
            <a href="/search">Search</a>
            <a href="/pay">Pay</a>
            <a href="/transactions">Transaction History</a>
            <a href="/profile">Profile</a> 
            
            <form method="POST" action="/logout" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>
</body>
</html>