<?php
session_start();
require 'db.php';

// ====== Notification Functions ======
function sendEmailNotification($toEmail, $subject, $message) {
    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($toEmail, $subject, $message, $headers);
}

function sendSMSNotification($phoneNumber, $message) {
    $apiKey = "YOUR_TERMI_KEY"; // Replace with your actual Termii API key
    $senderID = "LoanSys";      // Must be your approved Termii sender ID

    $payload = [
        "to" => $phoneNumber,
        "from" => $senderID,
        "sms" => $message,
        "type" => "plain",
        "channel" => "generic",
        "api_key" => $apiKey
    ];

    $ch = curl_init("https://api.ng.termii.com/api/sms/send");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
}

// ====== Workflow Logic ======
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "❌ Invalid request.";
    header("Location: loan_application.php");
    exit();
}

$loan_id = $_POST['loan_id'] ?? null;
$role = $_POST['role'] ?? '';
$action = $_POST['action'] ?? '';

if (!$loan_id || !$role || !$action) {
    $_SESSION['error'] = "❌ Missing required parameters.";
    header("Location: loan_application.php");
    exit();
}

// ====== Role-Based Status Mapping ======
$status_map = [
    'relationship_officer' => [
        'review' => 'reviewed'
    ],
    'auditor' => [
        'forward' => 'forwarded_to_manager'
    ],
    'manager' => [
        'approve' => 'approved_by_manager',
        'reject' => 'rejected_by_manager'
    ],
    'accountant' => [
        'check' => 'checked'  // You can later change to 'disbursed'
    ]
];

// ====== Validate Action ======
if (!isset($status_map[$role][$action])) {
    $_SESSION['error'] = "❌ Invalid action for this role.";
    header("Location: admin_panel.php");
    exit();
}

$new_status = $status_map[$role][$action];

// ====== Update Loan Status in Database ======
$stmt = $conn->prepare("UPDATE loans SET loan_status = ?, updated_at = NOW() WHERE loan_id = ?");
$stmt->bind_param("si", $new_status, $loan_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "✅ Loan #$loan_id status updated to '$new_status'.";

    // --- Insert into approvals if loan is approved ---
    if ($new_status === 'approved_by_manager') {
        $approvalStmt = $conn->prepare("INSERT INTO approvals (item_id, item_type, status) VALUES (?, 'loan', 'approved')");
        $approvalStmt->bind_param("i", $loan_id);
        $approvalStmt->execute();
        $approvalStmt->close();
    }

    // Fetch member contact for notification
    $memberQuery = $conn->prepare("
        SELECT m.email, m.phone_number 
        FROM loans l 
        JOIN members m ON l.member_id = m.member_id 
        WHERE l.loan_id = ?
    ");
    $memberQuery->bind_param("i", $loan_id);
    $memberQuery->execute();
    $result = $memberQuery->get_result();

    if ($row = $result->fetch_assoc()) {
        $toEmail = $row['email'];
        $phoneNumber = $row['phone_number'];
        $subject = "Loan Update: Loan #$loan_id";
        $message = "Dear Member,\n\nYour loan application status has been updated to '$new_status'.\n\nThank you.";

        sendEmailNotification($toEmail, $subject, $message);
        sendSMSNotification($phoneNumber, $message);
    }

    $memberQuery->close();
} else {
    $_SESSION['error'] = "❌ Failed to update loan status.";
}

$stmt->close();

// ====== Role-Based Redirection ======
switch ($role) {
    case 'relationship_officer':
        header("Location: relationship_officer.php");
        break;
    case 'auditor':
        header("Location: auditor.php");
        break;
    case 'manager':
        header("Location: manager.php");
        break;
    case 'accountant':
        header("Location: accountant.php");
        break;
    default:
        header("Location: admin_panel.php");
        break;
}
exit();
?>
