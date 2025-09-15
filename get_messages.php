<?php
session_start();
include 'db.php';

$current_id = $_SESSION['adminId'] ?? $_SESSION['member_id'] ?? null;
$partner_id = intval($_GET['partner_id']); // ID of person you're chatting with

if (!$current_id) die("Unauthorized");

$stmt = $conn->prepare("
    SELECT * FROM messages 
    WHERE 
        (sender_id = ? AND receiver_id = ?) OR 
        (sender_id = ? AND receiver_id = ?)
    ORDER BY timestamp ASC
");
$stmt->bind_param("iiii", $current_id, $partner_id, $partner_id, $current_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $is_sender = $row['sender_id'] == $current_id;
    $label = $is_sender ? "You" : ucfirst($row['sender_role']);
    echo "<p><strong>{$label}:</strong> " . htmlspecialchars($row['message']) . " <small style='color:#aaa;'>(" . $row['timestamp'] . ")</small></p>";
}
