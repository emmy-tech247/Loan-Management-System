<?php
// Ensure no output before headers or JSON
ob_start();
header('Content-Type: application/json');
session_start();

include 'db.php';

// Get logged-in member ID
$member_id = $_SESSION['member_id'] ?? 1;

// Sanitize inputs
$amount = floatval($_POST['amount_paid'] ?? 0);
$type = trim($_POST['type'] ?? '');
$reference = uniqid('pay_');

// Validate inputs
if ($amount <= 0 || empty($type)) {
    echo json_encode(['error' => 'Invalid amount/type']);
    exit;
}

// Get member email
$email = '';
$stmt = $conn->prepare("SELECT email_address FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

if (empty($email)) {
    echo json_encode(['error' => 'Member email not found']);
    exit;
}

// Insert pending transaction
$stmt = $conn->prepare("
    INSERT INTO payment_transactions (member_id, amount_paid, type, status, reference)
    VALUES (?, ?, ?, 'pending', ?)
");
$stmt->bind_param("idss", $member_id, $amount, $type, $reference);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Failed to log transaction']);
    exit;
}
$stmt->close();

// Return data as valid JSON
echo json_encode([
    'key' => 'pk_test_7886df5e8290f9f50b3fa179756bd7a98c85664e', // ✅ Replace with actual public key
    'email' => $email,
    'amount' => intval($amount * 100), // Paystack expects amount in kobo
    'reference' => $reference
]);
exit;
?>
