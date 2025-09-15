<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = intval($_POST['admin_id']);
    $member_id = intval($_POST['member_id']);
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
        $stmt->bind_param("iis", $admin_id, $member_id, $message);
        $stmt->execute();
    }
}
