<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    die("Unauthorized access.");
}

$admin_id = $_SESSION['adminId'];

if (!isset($_GET['member_id'])) {
    die("No member selected.");
}
$member_id = intval($_GET['member_id']);

// Get member name
$stmt = $conn->prepare("SELECT first_name FROM members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$stmt->bind_result($fullname);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Chat with <?= htmlspecialchars($fullname) ?></title>
  <style>
    body { font-family: Arial; background: #f5f5f5; margin: 0; }
    .chat-box {
        max-width: 700px;
        margin: 40px auto;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 80vh;
    }
    .chat-header {
        background: #007bff;
        color: white;
        padding: 15px;
        font-size: 18px;
    }
    .chat-body {
        padding: 15px;
        overflow-y: auto;
        flex: 1;
        background: #f9f9f9;
    }
    .chat-form {
        display: flex;
        border-top: 1px solid #ddd;
    }
    .chat-form textarea {
        flex: 1;
        padding: 10px;
        resize: none;
        border: none;
        outline: none;
    }
    .chat-form button {
        padding: 10px 20px;
        border: none;
        background: #28a745;
        color: white;
        cursor: pointer;
    }
    .message {
        padding: 8px 12px;
        margin: 5px 0;
        border-radius: 10px;
        max-width: 75%;
        clear: both;
    }
    .admin-message {
        background: #007bff;
        color: white;
        margin-left: auto;
        text-align: right;
    }
    .member-message {
        background: #e4e6eb;
        color: #333;
        margin-right: auto;
        text-align: left;
    }
    .timestamp {
        font-size: 10px;
        color: gray;
        margin-top: 3px;
    }
  </style>
</head>
<body>
  <div class="chat-box">
    <div class="chat-header">
      💬 Chat with <?= htmlspecialchars($fullname) ?>
    </div>

    <div class="chat-body" id="chat-body">
      <!-- Messages will load here via AJAX -->
    </div>

    <form class="chat-form" id="chat-form">
      <textarea placeholder="Type your message..." id="msg" required></textarea>
      <button type="submit">Send</button>
    </form>
  </div>

  <script>
    const chatBody = document.getElementById("chat-body");
    const msgInput = document.getElementById("msg");
    const form = document.getElementById("chat-form");

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function fetchMessages() {
        fetch("fetch_messages_admin.php?member_id=<?= $member_id ?>")
            .then(res => res.text())
            .then(data => {
                chatBody.innerHTML = data;
                scrollToBottom();
            });
    }

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const message = msgInput.value.trim();

        if (message !== "") {
            fetch("send_message_admin.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    member_id: "<?= $member_id ?>",
                    message: message
                })
            })
            .then(res => res.text())
            .then(response => {
                if (response === "success") {
                    msgInput.value = "";
                    fetchMessages();
                }
            });
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
  </script>
</body>
</html>
