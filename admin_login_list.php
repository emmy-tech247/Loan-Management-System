<?php
// Security Best Practice: Start session and define a password hash example
session_start();

// EXAMPLE - how you could hash passwords securely (used in login scripts)
$plainPassword = 'example123'; // This would come from user input during registration
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT); // Store this in your DB

// To verify during login:
// password_verify($inputPassword, $hashedPasswordFromDB);

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
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .admin {
      color: #2c3e50;
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
    }

    .center-container {
      display: flex;
      justify-content: center;
      margin: 10px 0;
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

    @media (max-width: 480px) {
      body {
        padding: 20px 10px;
      }

      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }

      .admin {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>
  <div>
    <div class="admin">Admin Login Panel</div>

    <div class="center-container">
      <a class="logout-btn" href="admin_login1.php">Login As Admin1</a>
    </div>

    <div class="center-container">
      <a class="logout-btn" href="admin_login2.php">Login As Admin2</a>
    </div>

    <div class="center-container">
      <a class="logout-btn" href="home.php">Back to Home Page</a>
    </div>
  </div>
</body>
</html>
