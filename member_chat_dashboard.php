<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized access");
}
$member_id = $_SESSION['member_id'];

// Secure admin ID
$admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : 1;

// Handle POST message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'member', ?, NOW())");
        $stmt->bind_param("iis", $member_id, $admin_id, $msg);
        $stmt->execute();
        exit;
    }
}

// Handle AJAX fetch
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Chat with Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding-top: 0;
            text-align: center;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #004080;
            padding: 0 30px;
            margin:0;
     
           
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

        .navbar a:hover:not(.logo-hover-disabled) {
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .navbar img {
            width: 80px;
            height: 80px;
            margin: -50px -50px -20px -50px;
            display: block;
        }

        .container, .container2 {
            max-width: 90%;
            width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        .admin-button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin: 10px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        .admin1 { background-color: #28a745; }
        .admin2 { background-color: #007bff; }
        .admin-button:hover { opacity: 0.9; }

        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            margin-top: 20px;
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            background: #007bff;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
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
            margin-top: 10px;
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

        footer {
            text-align: center;
            padding: 30px;
            background-color: #004080;
            color: white;
            margin-top: 50px;
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                padding: 10px;
            }

            .navbar .right {
                flex-direction: column;
                width: 100%;
            }

            .chat-body {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="home.php" class="logo-hover-disabled">
                <img src="images/logo2.png" alt="Logo">
            </a>
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
            <div class="header">💬 Chat with Admin <?= htmlspecialchars($admin_id) ?></div>
        </div>
    </div>

    <div class="container">
        <div class="chat-body" id="chat-box"></div>
        <div class="chat-form">
            <textarea id="message" rows="2" placeholder="Type your message..."></textarea>
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
    </footer>

    <script defer>
    function fetchMessages() {
        fetch('member_chat_dashboard.php?fetch=1&admin_id=<?= htmlspecialchars($admin_id) ?>')
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

        fetch("member_chat_dashboard.php?admin_id=<?= htmlspecialchars($admin_id) ?>", {
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
