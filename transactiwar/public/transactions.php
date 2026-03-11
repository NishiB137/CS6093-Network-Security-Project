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

$historyData = TransactionController::history($pdo);
$transactions = $historyData['transactions'];
$currentBalance = $historyData['balance'];
$currentUserId = $_SESSION['user_id'];
?>

<main class="container">
    <h2 class="page-title">Transaction History</h2>

    <div class="balance-summary-card">
        <h3>Current Balance: Rs. <?= Sanitizer::escape(number_format($currentBalance, 2)) ?></h3>
    </div>

    <?php if (empty($transactions)): ?>
        <p class="empty-state">No transactions found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Amount (Rs.)</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                        <?php 
                            $isSender = ($t['sender_id'] == $currentUserId);
                            $type = $isSender ? 'Sent' : 'Received';
                            $otherUser = $isSender ? $t['receiver_name'] : $t['sender_name'];
                            
                            // Replaced raw colors with CSS class names to eliminate inline styles
                            $colorClass = $isSender ? 'text-danger' : 'text-success';
                            $sign = $isSender ? '-' : '+';
                        ?>
                        <tr>
                            <td><?= Sanitizer::escape($t['created_at']) ?></td>
                            <td><strong><?= $type ?></strong></td>
                            <td><?= Sanitizer::escape($otherUser) ?></td>
                            <td class="fw-bold <?= $colorClass ?>">
                                <?= $sign ?><?= Sanitizer::escape(number_format($t['amount'], 2)) ?>
                            </td>
                            <td><?= Sanitizer::escape($t['comment'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>