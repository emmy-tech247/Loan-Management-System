<?php
require 'db.php';
$member_id = $_GET['member_id'] ?? 0;

$data = [
    'loan_balance' => 0,
    'savings_balance' => 0,
    'fixed_deposit' => 0,
    'transactions' => []
];

$q1 = $conn->query("SELECT balance FROM loan_mgt WHERE member_id = $member_id");
if ($r1 = $q1->fetch_assoc()) $data['loan_balance'] = $r1['balance'];

$q2 = $conn->query("SELECT SUM(auto_amount) as total FROM savings WHERE member_id = $member_id");
if ($r2 = $q2->fetch_assoc()) $data['savings_balance'] = $r2['total'];

$q3 = $conn->query("SELECT SUM(amount_deposited ) as total FROM fixed_deposits WHERE member_id = $member_id");
if ($r3 = $q3->fetch_assoc()) $data['fixed_deposit'] = $r3['total'];

$q4 = $conn->query("SELECT type, auto_amount, date FROM transactions WHERE member_id = $member_id ORDER BY date DESC");
while ($row = $q4->fetch_assoc()) $data['transactions'][] = $row;

echo json_encode($data);
?>
