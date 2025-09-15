<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = 1; // Replace with $_SESSION['member_id'] in production
    $amount = $_POST['amount_saved'] ?? null;
    $reference = 'TOPUP-' . uniqid(); // Generate unique reference

    if ($amount !== null && is_numeric($amount)) {
        // Insert with unique reference
        $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, amount_saved, reference) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $member_id, $amount, $reference);

        if ($stmt->execute()) {
            echo "✅ ₦" . number_format($amount, 2) . " added to your savings. Reference: $reference";
        } else {
            echo "❌ Failed to record top-up. Try again.";
        }
    } else {
        echo "❌ Invalid amount.";
    }
} else {
    echo "❌ Invalid request method.";
}
?>
