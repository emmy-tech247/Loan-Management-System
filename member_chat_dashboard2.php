<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized access");
}
$member_id = $_SESSION['member_id'];

// Which admin to chat with (optional switch)
$admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : 1;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'member', ?, NOW())");
        $stmt->bind_param("iis", $member_id, $admin_id, $msg);
        $stmt->execute();
        exit; // stop here when sending via fetch
    }
}

// Fetch messages (AJAX or initial load)
if (isset($_GET['fetch']) && $_GET['fetch'] === '1') {
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

    while ($row = $result->fetch_assoc()) {
        $class = $row['sender_type'] === 'member' ? 'member-message' : 'admin-message';
        echo "<div class='message $class'>";
        echo nl2br(htmlspecialchars($row['message']));
        echo "<div class='timestamp'>" . date('M j, Y g:i A', strtotime($row['created_at'])) . "</div>";
        echo "</div>";
    }
    exit; // important: prevent page render
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Chat with Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f5;
            margin: 0;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .chat-body {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }
        .message {
            max-width: 70%;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 15px;
            word-wrap: break-word;
        }
        .member-message {
            background: #d1ecf1;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }
        .admin-message {
            background: #d4edda;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }
        .timestamp {
            font-size: 11px;
            color: gray;
            margin-top: 4px;
            text-align: right;
        }
        .chat-form {
            display: flex;
            border-top: 1px solid #ccc;
        }
        .chat-form textarea {
            flex: 1;
            padding: 10px;
            border: none;
            resize: none;
            font-size: 14px;
        }
        .chat-form button {
            padding: 0 25px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .chat-form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">💬 Chat with Admin <?= $admin_id ?></div>
    <div class="chat-body" id="chat-box"></div>

    <div class="chat-form">
        <textarea id="message" rows="2" placeholder="Type your message..."></textarea>
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
function fetchMessages() {
    fetch('member_chat_dashboard.php?fetch=1&admin_id=<?= $admin_id ?>')
        .then(res => res.text())
        .then(data => {
            const box = document.getElementById('chat-box');
            box.innerHTML = data;
            box.scrollTop = box.scrollHeight;
        });
}

function sendMessage() {
    let msg = document.getElementById("message").value.trim();
    if (!msg) return;

    fetch("member_chat_dashboard.php?admin_id=<?= $admin_id ?>", {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: "message=" + encodeURIComponent(msg)
    }).then(() => {
        document.getElementById("message").value = "";
        fetchMessages();
    });
}

setInterval(fetchMessages, 3000);
window.onload = fetchMessages;
</script>
</body>
</html>
