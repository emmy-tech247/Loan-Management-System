<?php
session_start();
include 'db.php';

$admin_id = 1;

if (isset($_GET['member_id']) && is_numeric($_GET['member_id'])) {
    $member_id = intval($_GET['member_id']);

    $stmt = $conn->prepare("
        SELECT * FROM messages 
        WHERE 
            (sender_id = ? AND receiver_id = ? AND sender_type = 'admin') OR 
            (sender_id = ? AND receiver_id = ? AND sender_type = 'member') 
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("iiii", $admin_id, $member_id, $member_id, $admin_id);
    $stmt->execute();
    $messages = $stmt->get_result();

    while ($row = $messages->fetch_assoc()):
?>
    <div class="message <?= $row['sender_type'] === 'admin' ? 'admin-message' : 'member-message' ?>">
        <?= nl2br(htmlspecialchars($row['message'])) ?>
        <div class="timestamp"><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></div>
    </div>
<?php
    endwhile;
}
