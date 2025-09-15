<?php
require 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    die("You must be logged in to apply for a loan.");
}
$member_id = $_SESSION['member_id'];

// Validate and sanitize inputs
$loan_type = $_POST['loan_type'] ?? '';
$amount_collected = $_POST['balance'] ?? 0;
$duration = $_POST['duration'] ?? 0;
$purpose = $_POST['purpose'] ?? '';
$interest_rate = 5.0; // fixed

// Ensure uploads directory exists
$uploadDir = __DIR__ . "/../uploads";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Secure file upload function
function upload($name) {
    global $uploadDir;
    if (!isset($_FILES[$name]) || $_FILES[$name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $fileName = basename($_FILES[$name]['name']);
    $targetPath = $uploadDir . "/" . $fileName;
    if (move_uploaded_file($_FILES[$name]['tmp_name'], $targetPath)) {
        return "uploads/" . $fileName; // relative path for DB
    } else {
        return null;
    }
}

// Upload files
$id_doc = upload('id_doc');
$passport = upload('passport');
$payslip = upload('payslip');
$guarantor = upload('guarantor');

// Check if all files were uploaded successfully
if (!$id_doc || !$passport || !$payslip || !$guarantor) {
    die("One or more files failed to upload. Please try again.");
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO loans 
(member_id, loan_type, balance, duration, purpose, id_doc, passport, payslip, guarantor, interest_rate) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "isdiissssd", 
    $member_id, 
    $loan_type, 
    $amount_collected, 
    $duration, 
    $purpose, 
    $id_doc, 
    $passport, 
    $payslip, 
    $guarantor, 
    $interest_rate
);

if ($stmt->execute()) {
    echo "✅ Application submitted successfully!";
} else {
    echo "❌ Failed to submit application: " . $stmt->error;
}
?>
