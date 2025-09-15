<?php
session_start();
include('db.php');

$error = "";

// ✅ Login process
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // plain password input

    // Fetch user by email + role
    $query = "SELECT * FROM admin_panel WHERE email = ? AND role = 'auditor' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash'])) {
            // ✅ Login successful
            $_SESSION['auditor_loginId'] = $row['username'];
            $_SESSION['auditorId'] = $row['staff_id'];
            $_SESSION['role'] = 'auditor';

            // Log the login action
            $member_id = $row['staff_id'];
            $action = "Auditor login";
            $ip = $_SERVER['REMOTE_ADDR'];
            $agent = $_SERVER['HTTP_USER_AGENT'];

            $logStmt = $conn->prepare("INSERT INTO logs (member_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
            $logStmt->bind_param("isss", $member_id, $action, $ip, $agent);
            $logStmt->execute();
            $logStmt->close();

            header("Location: auditor.php");
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Auditor Login</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 10px;
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 320px;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .login-box input, .login-box button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .login-box button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
    }
    .login-box button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      text-align: center;
    }
    .center-container {
      margin-top: 15px;
      text-align: center;
    }
    .center-container a {
      display: block;
      color: #007bff;
      text-decoration: none;
      margin: 5px 0;
      font-size: 14px;
    }
    .center-container a:hover {
      text-decoration: underline;
    }
    @media (max-width: 400px) {
      .login-box {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Auditor Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="submit">Login</button> 
    </form>
    <div class="center-container">
      <a href="forgot_password.php">Forgot Password?</a>
      <a href="admin_panel.php">Back to Administrators Dashboard</a>
    </div>
  </div>
</body>
</html>
