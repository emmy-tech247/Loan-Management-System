<?php
session_start();
require_once "db.php";

$loan_id = (int)($_POST['loan_id'] ?? 0);
$role    = $_POST['role'] ?? '';

if (!$loan_id || !$role) die("Invalid request");

$statusMap = [
    'relationship_officer' => 'forwarded_to_auditor',
    'auditor'              => 'forwarded_to_manager',
    'manager'              => 'forwarded_to_accountant',
    'accountant'           => 'disbursed'
];

if (isset($_POST['forward']) && isset($statusMap[$role])) {
    $newStatus = $statusMap[$role];
    $stmt = $conn->prepare("UPDATE loans SET loan_status = ? WHERE loan_id = ?");
    $stmt->bind_param("si", $newStatus, $loan_id);
    $stmt->execute();
    echo "Loan forwarded successfully!";
} elseif (isset($_POST['reject'])) {
    $stmt = $conn->prepare("UPDATE loans SET loan_status = 'rejected' WHERE loan_id = ?");
    $stmt->bind_param("i", $loan_id);
    $stmt->execute();
    echo "Loan rejected.";
} else {
    echo "Invalid action.";
}
?>
