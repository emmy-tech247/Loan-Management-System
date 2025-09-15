<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$member_id = $_SESSION['member_id'];
$admin_id = 1;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'member', ?, NOW())");
        $stmt->bind_param("iis", $member_id, $admin_id, $msg);
        $stmt->execute();
        echo "success";
        exit;
    }
}
http_response_code(400);
echo "Invalid request";
