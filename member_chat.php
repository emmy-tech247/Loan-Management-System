<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Invalid member ID.");
}
$member_id = $_SESSION['member_id'];
$admin_id = 1; // Single admin ID

// Handle sending message
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message) VALUES (?, ?, 'member', ?)");
        $stmt->bind_param("iis", $member_id, $admin_id, $msg);
        $stmt->execute();
    }
}

// Mark admin messages as read
$conn->query("UPDATE messages SET is_read = 1 WHERE receiver_id = $member_id AND sender_id = $admin_id AND sender_type = 'admin'");

// Fetch chat history
$stmt = $conn->prepare("SELECT * FROM messages WHERE 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'member') 
    OR 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'admin') 
    ORDER BY created_at ASC");
$stmt->bind_param("iiii", $member_id, $admin_id, $admin_id, $member_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Chat</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        .chat-box { background: #fff; padding: 15px; border: 1px solid #ccc; max-width: 600px; margin: auto; height: 400px; overflow-y: scroll; }
        .message { margin-bottom: 10px; }
        .admin { color: blue; }
        .member { color: green; }
        form { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Chat with Admin</h2>
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
