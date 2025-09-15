<?php
session_start();
require_once "db.php";

// Check officer role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'relationship_officer') {
    die("Unauthorized access.");
}

$loan_id = $_GET['id'] ?? null;

if ($loan_id) {
    $stmt = $conn->prepare("UPDATE loans SET loan_status = 'forwarded_to_auditor', updated_at = NOW() WHERE loan_id = ?");
    $stmt->bind_param("i", $loan_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Loan #$loan_id has been forwarded to Auditor.";
    } else {
        $_SESSION['error'] = "Failed to forward loan.";
    }
    $stmt->close();
}

header("Location: relationship_officer.php");
exit();
