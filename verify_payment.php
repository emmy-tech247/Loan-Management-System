<?php
session_start();
require 'db.php';

// Ensure payment_id and action are set
if (!isset($_POST['payment_id'], $_POST['action'])) {
    die("Invalid request.");
}

$payment_id = (int)$_POST['payment_id'];
$action = $_POST['action'];

// Fetch current payment status
$stmt = $conn->prepare("SELECT acknowledged_by_admin1, confirmed_by_admin2 FROM payment_transactions WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Payment not found.");
}

$payment = $result->fetch_assoc();

// Handle Acknowledge
if ($action === 'acknowledge' && !$payment['acknowledged_by_admin1']) {
    $update = $conn->prepare("UPDATE payment_transactions SET acknowledged_by_admin1 = 1, acknowledged_at = NOW() WHERE id = ?");
    $update->bind_param("i", $payment_id);
    $update->execute();

    // Optional audit log (corrected variable name)
    $conn->query("INSERT INTO audit_trail (action, timestamp) VALUES ('Payment ID $payment_id acknowledged by Admin 1', NOW())");

    echo "Payment acknowledged successfully.";

// Handle Confirm
} elseif ($action === 'confirm') {
    if (!$payment['acknowledged_by_admin1']) {
        die("Cannot confirm before acknowledgment.");
    }
    if (!$payment['confirmed_by_admin2']) {
        $update = $conn->prepare("UPDATE payment_transactions SET confirmed_by_admin2 = 1, confirmed_at = NOW() WHERE id = ?");
        $update->bind_param("i", $payment_id);
        $update->execute();

        // Optional audit log
        $conn->query("INSERT INTO audit_trail (action, timestamp) VALUES ('Payment ID $payment_id confirmed by Admin 2', NOW())");

        echo "Payment confirmed successfully.";
    } else {
        echo "Payment already confirmed.";
    }
} else {
    echo "Invalid action or already processed.";
}

$conn->close();
?>
