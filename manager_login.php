<?php
session_start();
require_once "db.php";

$error = "";

if (isset($_POST['submit'])) {
    $username = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Fetch user by email
    $stmt = $conn->prepare("SELECT * FROM admin_panel WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Check role first
        if ($row['role'] !== 'manager') {
            $error = "Access denied. Manager login only.";
        }
        // ✅ Verify password
        elseif (password_verify($password, $row['password_hash'])) {
            $_SESSION['manager_loginId'] = $row['username'];
            $_SESSION['managerId'] = $row['staff_id'];
            $_SESSION['role'] = $row['role']; // store role for checks later

            // ✅ Log action (optional, if you have logs table)
            $staff_id = $row['staff_id'];
            $action = "Manager login";
            $ip = $_SERVER['REMOTE_ADDR'];
            $agent = $_SERVER['HTTP_USER_AGENT'];

            if ($logStmt = $conn->prepare("INSERT INTO logs (member_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)")) {
                $logStmt->bind_param("isss", $staff_id, $action, $ip, $agent);
                $logStmt->execute();
                $logStmt->close();
            }

            header("Location: manager.php");
            exit();
        } else {
            $error = "Incorrect email or password.";
        }
    } else {
        $error = "Incorrect email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manager Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 90%;
      max-width: 340px;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
    }

    .login-box input, .login-box button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    .login-box button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
    }

    .login-box button:hover {
      background: #0056b3;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }

    .center-container {
      text-align: center;
      margin-top: 15px;
    }

    .logout-btn {
      display: inline-block;
      font-size: 14px;
      color: #007bff;
      text-decoration: none;
    }

    .logout-btn:hover {
      text-decoration: underline;
    }

    .forgot-link {
      display: block;
      margin-top: 8px;
      font-size: 14px;
      color: #007bff;
      text-align: center;
      text-decoration: none;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Manager Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="submit">Login</button> 
    </form>

    <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>

    <div class="center-container">
      <a class="logout-btn" href="admin_panel.php">Back to Administrators Dashboard</a>
    </div>
  </div>
</body>
</html>
