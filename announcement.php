<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "loan_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepared statement for security
$stmt = $conn->prepare("SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Announcements</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 0;
      margin: 0;
    }

    .announcement {
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin: 20px auto;
      max-width: 900px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .announcement h3 {
      margin-top: 0;
    }

    .announcement small {
      color: gray;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .left, .navbar .right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar a, .dropbtn {
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

    .navbar .left a:hover {
      background: none !important;
      padding: 0 !important;
      border-radius: 0 !important;
      cursor: pointer;
    }

    .navbar img {
      display: block;
      margin: -50px -50px -20px -50px;
      padding: 0;
    }

    h2 {
      text-align: center;
      padding: 20px 10px;
      color: #004080;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 50px;
    }

    @media (max-width: 768px) {
      .navbar .left, .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 15px;
        width: 100%;
      }

      .announcement {
        margin: 10px;
        padding: 15px;
      }
    }

     .navbar .left a:hover,
.navbar .left a:hover img {
  background-color: transparent !important;
  padding: 0 !important;
  margin: -50px -50px -20px -50px !important;
  border-radius: 0 !important;
  cursor: default;
}

  </style>
</head>
<body>

  <div class="navbar">
    <!-- Left section -->
    <div class="left">
      <a href="home.php" class="logo-link">
        <img src="images/logo2.png" alt="Logo" width="80" height="80">
      </a>
    </div>

    <!-- Right section -->
    <div class="right">
      <a href="member.php">Back</a>
      <a href="faq.php">FAQ</a>
      <a href="announcement.php">Announcements</a>
      <a href="member_chat_dashboard.php">Chat with Admin</a>
    </div>
  </div>

  <h2>📢 Latest Announcements</h2>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="announcement">
      <h3><?= htmlspecialchars($row['title']) ?></h3>
      <small><?= date('d M Y H:i', strtotime($row['created_at'])) ?></small>
      <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
    </div>
  <?php endwhile; ?>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
