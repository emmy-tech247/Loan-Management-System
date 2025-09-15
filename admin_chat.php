<?php
session_start();
include 'db.php';

$admin_id = 1;
$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
if ($member_id <= 0) die("Invalid member ID.");

// Send message
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message) VALUES (?, ?, 'admin', ?)");
        $stmt->bind_param("iis", $admin_id, $member_id, $msg);
        $stmt->execute();
    }
}

// Mark member messages as read
$conn->query("UPDATE messages SET is_read = 1 WHERE receiver_id = $admin_id AND sender_id = $member_id AND sender_type = 'member'");

// Fetch chat history
$stmt = $conn->prepare("SELECT * FROM messages WHERE 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'admin') 
    OR 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'member') 
    ORDER BY created_at ASC");
$stmt->bind_param("iiii", $admin_id, $member_id, $member_id, $admin_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with Member</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .chat-box { background: #fff; padding: 15px; border: 1px solid #ccc; max-width: 600px; margin: auto; height: 400px; overflow-y: scroll; }
        .message { margin-bottom: 10px; }
        .admin { color: blue; }
        .member { color: green; }
        form { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Chat with Member #<?= $member_id ?></h2>
    <div class="chat-box">
        <?php while ($row = $messages->fetch_assoc()): ?>
            <div class="message">
                <strong class="<?= $row['sender_type']; ?>">
                    <?= ucfirst($row['sender_type']); ?>:
                </strong> <?= htmlspecialchars($row['message']); ?>
                <small style="color: #888;">(<?= $row['created_at']; ?>)</small>
            </div>
        <?php endwhile; ?>
    </div>
    <form method="post">
        <textarea name="message" rows="2" cols="60" placeholder="Type your message..." required></textarea><br>
        <button type="submit">Send</button>
    </form>
</body>
</html>
