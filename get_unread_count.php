<?php
session_start();
include 'db.php';

$current_id = $_SESSION['adminId'] ?? $_SESSION['member_id'] ?? null;
$current_role = isset($_SESSION['adminId']) ? 'admin' : 'member';

$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = ? AND sender_role != ? AND is_read = 0");
$stmt->bind_param("is", $current_id, $current_role);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo $result['unread_count'];
