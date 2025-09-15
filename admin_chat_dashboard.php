<?php
session_start();
include 'db.php';


$admin_id = intval($_SESSION['adminId']);
$session_member_id = intval($_SESSION['member_id']);

// Validate member_id from GET
if (!isset($_GET['member_id']) || !is_numeric($_GET['member_id'])) {
    die("Invalid or missing member ID.");
}
$member_id = intval($_GET['member_id']);

// Check admin exists
// Check if admin exists
$stmt = $conn->prepare("SELECT id, username FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $admin_name = htmlspecialchars($admin['username']);
} else {
    // Optional: destroy session if invalid admin ID found
    session_unset();
    session_destroy();

    // Optional: redirect to login instead of showing error
    header("Location: admin_login.php?error=admin_not_found");
    exit;

    // Or use a cleaner message if you want to keep die()
    // die("⚠️ Admin account not found. Please log in again.");
}



// Get member info
$stmt = $conn->prepare("SELECT first_name, surname FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member_result = $stmt->get_result();
if ($member_result->num_rows === 0) {
    die("❌ Member not found.");
}
$member = $member_result->fetch_assoc();
$member_name = htmlspecialchars($member['first_name'] . " " . $member['surname']);

// Handle AJAX fetch messages
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
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

    while ($row = $messages->fetch_assoc()) {
        $class = $row['sender_type'] === 'admin' ? 'admin-message' : 'member-message';
        echo "<div class='message $class'>";
        echo nl2br(htmlspecialchars($row['message']));
        echo "<div class='timestamp'>" . date('M j, Y g:i A', strtotime($row['created_at'])) . "</div>";
        echo "</div>";
    }
    exit;
}

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_type, message, created_at) VALUES (?, ?, 'admin', ?, NOW())");
        $stmt->bind_param("iis", $admin_id, $member_id, $msg);
        $stmt->execute();
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?= $member_name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 80px);
        }
        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            font-size: 18px;
            text-align: center;
        }
        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            margin-bottom: 10px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
        }
        .member-message {
            background-color: #e6f7ff;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }
        .admin-message {
            background-color: #d1e7dd;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }
        .timestamp {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
            text-align: right;
        }
        .chat-form {
            display: flex;
            border-top: 1px solid #ccc;
            background: #f9f9f9;
        }
        .chat-form textarea {
            flex: 1;
            resize: none;
            border: none;
            padding: 15px;
            font-size: 14px;
            outline: none;
        }
        .chat-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0 25px;
            cursor: pointer;
            font-size: 15px;
            transition: background 0.3s ease;
        }
        .chat-form button:hover {
            background-color: #0056b3;
        }
        @media (max-width: 600px) {
            .chat-container {
                margin: 10px;
                height: auto;
            }
            .chat-body {
                height: 400px;
            }
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">💬 Chat with <?= $member_name ?></div>
    <div class="chat-body" id="chat-box"></div>
    <div class="chat-form">
        <textarea id="message" rows="2" placeholder="Type your message..." required></textarea>
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
let memberId = <?= $member_id ?>;

function fetchMessages() {
    fetch(`admin_chat_dashboard.php?fetch=1&member_id=${memberId}`)
        .then(res => res.text())
        .then(data => {
            const box = document.getElementById('chat-box');
            box.innerHTML = data;
            box.scrollTop = box.scrollHeight;
        });
}

function sendMessage() {
    const msg = document.getElementById("message").value.trim();
    if (!msg) return;

    fetch(`admin_chat_dashboard.php?member_id=${memberId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: "message=" + encodeURIComponent(msg)
    }).then(() => {
        document.getElementById("message").value = "";
        fetchMessages();
        setTimeout(() => {
            const box = document.getElementById("chat-box");
            box.scrollTop = box.scrollHeight;
        }, 100);
    });
}

setInterval(fetchMessages, 3000);
window.onload = fetchMessages;
</script>

</body>
</html>
