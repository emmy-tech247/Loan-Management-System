<?php
require 'db.php';

$loan_id = $_POST['loan_id'];
$amount_paid = $_POST['amount_paid'];
$method = $_POST['method'];

$stmt = $conn->prepare("INSERT INTO repayments (loan_id, amount_paid, date_paid, method)
VALUES (?, ?, CURDATE(), ?)");
$stmt->bind_param("ids", $loan_id, $amount_paid, $method);
$stmt->execute();

echo "Repayment recorded.";
?>
