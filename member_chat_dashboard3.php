<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized access");
}
$member_id = $_SESSION['member_id'];

// Get admin ID (must be 1 or 2)
$admin_id = (isset($_GET['admin_id']) && in_array($_GET['admin_id'], [1, 2])) ? intval($_GET['admin_id']) : 1;

// Handle message send (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'member', ?, NOW())");
        $stmt->bind_param("iis", $member_id, $admin_id, $msg);
        $stmt->execute();
    }
    exit;
}

// Handle message fetch (AJAX)
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
    exit;
}
?>


<!-- HTML Below remains unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Chat with Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* All your styles preserved exactly */
        .container {
            max-width: 700px;
            margin: 2px auto;
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
            width: 500px;
            align-items: center; 
            padding: 30 40 40  50px;
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
            margin-top:0;
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

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            text-align: center;
            padding-top: 100px;
        }

        .container {
            display: inline-block;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 500px;
        }

        h2 {
            margin-bottom: 30px;
        }

        .admin-button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin: 10px;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
        }

        .admin1 {
            background-color: #28a745; /* Green */
        }

        .admin2 {
            background-color: #007bff; /* Blue */
        }

        .admin-button:hover {
            opacity: 0.9;
        }

        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 8vh;
            background-color: #f0f0f0;
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            color: #ffff;
            padding: 20 20 20px;    
        }

        body { font-family: Arial; padding: 0px;margin:3px; background: #f8f8f8; }
        form, table { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 10px; }
        input, select, textarea { display: block; width: 100%; margin: 10px 0; padding: 10px; }

        .navbar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          background-color: #004080;
          font-family: Arial, sans-serif;
          padding: 0 40px;
          margin:top 0;
        }

        .navbar .left,
        .navbar .right {
          display: flex;
          align-items: center;
        }

        .navbar a,
        .dropbtn {
          font-size: 16px;
          color: white;
          text-align: center;
          padding: 18px 25px;
          text-decoration: none;
          background: none;
          border: none;
          cursor: pointer;
        }

        .navbar a:hover {
          background-color: #007bff;
          padding: 10px 15px;
          border-radius: 5px;
        }

        footer {
          text-align: center;
          padding: 30px;
          background-color: #004080;
          color: white;
          margin-top: 70px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style=" display: block; padding:0px;margin: -50px -50px -20px -50px;"> </a>
        </div>
        <div class="right">
            <a href="member.php">Back</a>
            <a href="faq.php">FAQ</a>
            <a href="announcement.php">Announcements</a>
            <a href="member_chat_dashboard.php">Chat with Admin</a>
        </div>
    </div>

    <div class="container2">
        <h2>👤 Select Admin to Chat</h2>
        <a href="member_chat_dashboard.php?admin_id=1" class="admin-button admin1">Chat with Admin 1</a>
        <a href="member_chat_dashboard.php?admin_id=2" class="admin-button admin2">Chat with Admin 2</a>
        <div class="header-container">
            <div class="header">💬 Chat with Admin <?= $admin_id ?></div>
        </div>
    </div>

    <div class="container">
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

<footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
</footer>
</body>
</html>
