<?php
session_start();
require 'db.php'; // Ensure this file contains a valid PDO or mysqli connection

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Validate input
$loan_id = isset($_POST['loan_id']) ? (int)$_POST['loan_id'] : 0;
$status = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : '';
$remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
$admin_id = $_SESSION['admin_id'] ?? 0;

$allowed_statuses = ['submitted', 'reviewed', 'checked', 'approved', 'rejected'];

if (!$loan_id || !in_array($status, $allowed_statuses)) {
    echo "Invalid loan ID or status.";
    exit;
}

// Optional: role-based control (e.g., only accountants can set 'checked')
$admin_role = $_SESSION['admin_role'] ?? 'admin'; // example roles: 'ro', 'accountant', 'manager'

// Role-based permission logic
$role_permissions = [
    'ro' => ['reviewed'],
    'accountant' => ['checked'],
    'manager' => ['approved', 'rejected'],
    'admin' => $allowed_statuses // super admin
];

if (!in_array($status, $role_permissions[$admin_role] ?? [])) {
    echo "You do not have permission to set status to '{$status}'.";
    exit;
}

// Update the loan status
$stmt = $conn->prepare("UPDATE loans SET status = ?, remarks = ?, updated_at = NOW() WHERE id = ?");
if ($stmt->execute([$status, $remarks, $loan_id])) {
    // Audit logging (optional)
    $log = $conn->prepare("INSERT INTO audit_trail (admin_id, action, timestamp) VALUES (?, ?, NOW())");
    $log->execute([$admin_id, "Updated loan #$loan_id status to '$status'"]);

    echo "Loan status updated successfully.";
    header("Location: ../admin_panel.php#loans");
    exit;
} else {
    echo "Failed to update loan status. Please try again.";
    exit;
}
