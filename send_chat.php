<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

$member_id = $_SESSION['member_id'];
$admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 1;
$message = trim($_POST['message']);

if ($message !== '') {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'member', ?, NOW())");
    $stmt->bind_param("iis", $member_id, $admin_id, $message);
    $stmt->execute();
}
?>
