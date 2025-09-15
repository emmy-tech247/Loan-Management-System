<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$admin_id = $_SESSION['adminId'];
$member_id = intval($_GET['member_id']);

$stmt = $conn->prepare("
    SELECT * FROM messages 
    WHERE 
      (sender_id = ? AND receiver_id = ? AND sender_type = 'member') OR 
      (sender_id = ? AND receiver_id = ? AND sender_type = 'admin') 
    ORDER BY created_at ASC
");
$stmt->bind_param("iiii", $member_id, $admin_id, $admin_id, $member_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
    <div class="message <?= $row['sender_type'] === 'admin' ? 'admin-message' : 'member-message' ?>">
        <?= nl2br(htmlspecialchars($row['message'])) ?>
        <div class="timestamp"><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></div>
    </div>
<?php endwhile; ?>
