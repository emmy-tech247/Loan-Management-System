<?php
require 'db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit;
}

$member_id = $_SESSION['member_id'];
$auto_amount = isset($_POST['auto_amount']) ? floatval($_POST['auto_amount']) : 0;

if ($auto_amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO monthly_savings (member_id, auto_amount)
VALUES (?, ?) ON DUPLICATE KEY UPDATE auto_amount = VALUES(auto_amount)");
$stmt->bind_param("id", $member_id, $auto_amount);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'amount' => number_format($auto_amount, 2)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save amount.']);
}
?>
