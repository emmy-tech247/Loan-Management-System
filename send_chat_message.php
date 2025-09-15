<?php
session_start();
include 'db.php';

$admin_id = 1;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['member_id'], $_POST['message'])) {
    $member_id = intval($_POST['member_id']);
    $msg = trim($_POST['message']);

    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
        $stmt->bind_param("iis", $admin_id, $member_id, $msg);
        $stmt->execute();
        echo 'success';
    } else {
        echo 'empty';
    }
} else {
    echo 'invalid';
}
