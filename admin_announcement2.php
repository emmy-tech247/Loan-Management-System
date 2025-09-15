<?php
session_start();


// Secure DB connection (Production Note: Use environment variables in real deployments)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'loan_system';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $msg = "✅ Announcement posted successfully!";
        } else {
            $msg = "❌ Error posting announcement.";
        }
        $stmt->close();
    } else {
        $msg = "⚠️ Both fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Announcement</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px 20px;
      background: #f0f2f5;
    }

    .form-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 15px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      margin-top: 20px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .message {
      margin-top: 20px;
      font-weight: bold;
      color: #2e7d32;
      text-align: center;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 30px;
    }

    .logout-btn {
      background-color: #007bff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover,
    .logout-btn:active {
      background-color: #0056b3;
    }

    @media (max-width: 640px) {
      body {
        padding: 20px 10px;
      }

      .form-box {
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
  <div class="form-box">
    <h2>📢 Post New Announcement</h2>
    <form method="POST" autocomplete="off">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" required>

      <label for="content">Content</label>
      <textarea id="content" name="content" rows="5" required></textarea>

      <button type="submit">Post Announcement</button>
    </form>

    <?php if ($msg): ?>
      <div class="message"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
  </div>

  <div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>
