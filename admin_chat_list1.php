<?php
session_start();
require_once 'db.php';



// Fetch distinct members involved in chats using a prepared statement
$stmt = $conn->prepare("
    SELECT DISTINCT m.member_id, m.full_name
    FROM members m
    JOIN messages msg ON m.member_id = msg.sender_id OR m.member_id = msg.receiver_id
    ORDER BY m.full_name ASC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Chat List</title>
  <link rel="stylesheet" href="style.css"> <!-- Optional external CSS -->
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px 20px;
      background-color: #f8f9fa;
    }

    .member-list {
      max-width: 600px;
      margin: auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .member-list h2 {
      text-align: center;
      color: #343a40;
      font-size: 24px;
      margin-bottom: 30px;
    }

    .member-item {
      padding: 12px 20px;
      margin: 8px 0;
      background: #e9ecef;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.2s ease;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .member-item:hover {
      background-color: #d6eaff;
    }

    .center-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }

    .logout-btn {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      padding: 12px 28px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    .logout-btn:active {
      background-color: #004080;
      transform: translateY(0);
    }

    @media (max-width: 600px) {
      body {
        padding: 20px 10px;
      }

      .member-list {
        padding: 20px;
      }

      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

  <div class="member-list">
    <h2>💬 Chat with a Member</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
      <div class='member-item' onclick="location.href='admin_chat_dashboard.php?member_id=<?= htmlspecialchars($row['member_id']) ?>'">
        👤 <?= htmlspecialchars($row['full_name']) ?>
      </div>
    <?php endwhile; ?>

    <div class="center-container">
      <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
    </div>
  </div>

</body>
</html>
