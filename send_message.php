<?php
session_start();
include 'db.php';

$sender_id = $_SESSION['adminId'] ?? $_SESSION['member_id'] ?? null;
$sender_role = isset($_SESSION['adminId']) ? 'admin' : 'member';

if (!$sender_id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Unauthorized or invalid request");
}

$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_role, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $sender_role, $message);
    $stmt->execute();
    echo "Message sent!";
} else {
    echo "Message cannot be empty.";
}
