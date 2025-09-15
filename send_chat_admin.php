<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$admin_id = $_SESSION['adminId'];
$member_id = intval($_POST['member_id']);
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
    $stmt->bind_param("iis", $admin_id, $member_id, $message);
    $stmt->execute();
    echo "success";
    exit;
}

http_response_code(400);
echo "Invalid input";
