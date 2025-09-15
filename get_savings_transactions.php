<?php
session_start();
header('Content-Type: application/json');
include('db.php');

// Check if the member is logged in
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$member_id = $_SESSION['member_id'];

// Get current savings total
$amountQuery = $conn->prepare("SELECT SUM(amount_saved) AS total_saved FROM savings_transactions WHERE member_id = ?");
$amountQuery->bind_param("i", $member_id);
$amountQuery->execute();
$amountResult = $amountQuery->get_result();
$amountRow = $amountResult->fetch_assoc();
$total_saved = $amountRow['total_saved'] ?? 0;

// Get all savings transactions
$transactionsQuery = $conn->prepare("SELECT transaction_type, amount_saved, created_at FROM savings_transactions WHERE member_id = ? ORDER BY created_at DESC");
$transactionsQuery->bind_param("i", $member_id);
$transactionsQuery->execute();
$transactionsResult = $transactionsQuery->get_result();

$transactions = [];
while ($row = $transactionsResult->fetch_assoc()) {
    $transactions[] = [
        'transaction_type' => $row['transaction_type'],
        'amount' => $row['amount_saved'],
        'created_at' => $row['created_at']
    ];
}

// Return response
echo json_encode([
    'balance' => $total_saved,
    'transactions' => $transactions
]);

?>

