<?php
session_start();
include 'db.php';

$partner_id = intval($_GET['partner_id']);
$partner_type = $_GET['partner_type'];

if (isset($_SESSION['member_id'])) {
    $user_id = $_SESSION['member_id'];
    $user_type = 'member';
} elseif (isset($_SESSION['admin1_id'])) {
    $user_id = $_SESSION['admin1_id'];
    $user_type = 'admin1';
} elseif (isset($_SESSION['admin2_id'])) {
    $user_id = $_SESSION['admin2_id'];
    $user_type = 'admin2';
} else {
    die("Unauthorized.");
}

// Mark as read
$update = $conn->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND receiver_type = ? AND sender_id = ? AND sender_type = ?");
$update->bind_param("isis", $user_id, $user_type, $partner_id, $partner_type);
$update->execute();

// Fetch messages
$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE 
    (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?) 
    OR 
    (sender_id = ? AND sender_type = ? AND receiver_id = ? AND receiver_type = ?)
    ORDER BY created_at ASC");

$stmt->bind_param("isisisis", $user_id, $user_type, $partner_id, $partner_type, $partner_id, $partner_type, $user_id, $user_type);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>
