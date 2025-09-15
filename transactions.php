<?php
session_start();
require 'db.php';

// Use logged-in member ID securely
if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    die("Unauthorized access.");
}
$member_id = intval($_SESSION['member_id']);

// Get balance
$stmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN type != 'withdrawal' THEN amount_saved ELSE 0 END) - 
        SUM(CASE WHEN type = 'withdrawal' THEN amount_saved ELSE 0 END) AS balance 
    FROM savings_transactions 
    WHERE member_id = ?
");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$balance = $row['balance'] ?? 0;

echo "<h2>Total Balance: ₦" . number_format($balance, 2) . "</h2>";

// Get transaction history
echo "<h3>Transaction History:</h3><table border='1'>";
echo "<tr><th>Type</th><th>Amount</th><th>Date</th></tr>";

$stmt = $conn->prepare("SELECT type, amount_saved, created_at FROM savings_transactions WHERE member_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

while ($tx = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($tx['type']) . "</td>
        <td>₦" . number_format($tx['amount_saved'], 2) . "</td>
        <td>" . htmlspecialchars($tx['created_at']) . "</td>
    </tr>";
}
echo "</table>";
?>
