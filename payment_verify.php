<?php
session_start();
include 'db.php';

if (!isset($_GET['payment_reference'])) {
  die("❌ No reference supplied");
}

$reference = $_GET['payment_reference'];
$secret_key = "sk_test_22525ea6d05f20aa98a38ae1a4e2f7376f08bd6d"; // Replace with your actual secret key

// Verify payment via Paystack
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . rawurlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $secret_key",
  "Cache-Control: no-cache"
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
  die("cURL Error: $err");
}

$result = json_decode($response, true);

if ($result['status'] && $result['data']['status'] === 'success') {
  $amount = $result['data']['amount'] / 100;
  $email = $result['data']['customer']['email'];
  $reference = $result['data']['reference'];
  $type = $result['data']['metadata']['custom_fields'][0]['value'];

  $member_id = $_SESSION['member_id'] ?? 0;

  // Mark the payment as verified
  $stmt = $conn->prepare("UPDATE payment_transactions SET status = 'successful', verified = 1 WHERE reference = ?");
  $stmt->bind_param("s", $reference);
  $stmt->execute();
  $stmt->close();

  // Insert into appropriate table
  if ($type === 'repayment') {
    // Find latest loan for this member
    $loan = $conn->query("SELECT id FROM loans WHERE member_id = $member_id ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
    $loan_id = $loan['id'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO repayments (loan_id, amount_paid, reference, payment_method, paid_at) VALUES (?, ?, ?, 'Paystack', NOW())");
    $stmt->bind_param("ids", $loan_id, $amount, $reference);
    $stmt->execute();
    $stmt->close();
  } elseif ($type === 'savings') {
    $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, amount_saved, reference, payment_method, saved_at) VALUES (?, ?, ?, 'Paystack', NOW())");
    $stmt->bind_param("ids", $member_id, $amount, $reference);
    $stmt->execute();
    $stmt->close();
  }

  // Set session for dashboard feedback
  $_SESSION['payment_status'] = 'success';
  $_SESSION['payment_type'] = $type;
  $_SESSION['payment_amount'] = $amount;

  header("Location: member.php");
  exit;
} else {
  echo "<h2 style='color:red;'>❌ Payment failed or not verified!</h2>";
  echo "<pre>" . print_r($result, true) . "</pre>";
}
?>
