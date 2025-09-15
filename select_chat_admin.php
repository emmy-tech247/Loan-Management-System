<?php
session_start();
if (isset($_GET['admin_id'])) {
    $_SESSION['admin_id'] = intval($_GET['admin_id']);
    header("Location: member_chat_dashboard.php");
    exit;
}

// No PHP logic is needed unless you want to set sessions or handle login later.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Admin</title>
    <style>
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
    </style>
</head>
<body>

<div class="container">
    <h2>👤 Select Admin to Chat</h2>
    <a href="member_chat_dashboard.php?admin_id=1" class="admin-button admin1">Chat with Admin 1</a>
    <a href="member_chat_dashboard.php?admin_id=2" class="admin-button admin2">Chat with Admin 2</a>
   

</div>

</body>
</html>
