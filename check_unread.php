<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

$member_id = $_SESSION['member_id'];
$unread = ['admin1' => 0, 'admin2' => 0];

for ($i = 1; $i <= 2; $i++) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as cnt FROM messages 
        WHERE sender_id = ? AND receiver_id = ? AND sender_type = 'admin' AND is_read = 0
    ");
    $stmt->bind_param("ii", $i, $member_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unread["admin$i"] = $result['cnt'];
}

header('Content-Type: application/json');
echo json_encode($unread);
?>
