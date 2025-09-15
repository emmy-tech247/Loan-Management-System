<?php
session_start();
include('db.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admin_panel WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['accountant_loginId'] = $row['username'];
                $_SESSION['accountantId'] = $row['id'];
                header("Location: accountant.php");
                exit();
            }
        }
        $stmt->close();
    }

    $error = "Incorrect email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accountant Login</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 0 10px;
    }
    .login-box {
      background: #fff;
      padding: 30px 25px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      box-sizing: border-box;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    input, button {
      width: 100%;
      margin: 10px 0;
      padding: 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 16px;
      box-sizing: border-box;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      transition: background 0.3s ease;
    }
    button:hover {
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
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }
    .logout-btn:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .login-box {
        padding: 20px 15px;
      }
      input, button {
        font-size: 14px;
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Accountant Login</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="accountant_login.php" autocomplete="off">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="submit">Login</button>
    </form>
    <div class="center-container">
      <a class="logout-btn" href="forgot_password.php">Forgot Password?</a><br>
      <a class="logout-btn" href="admin_panel.php">Back to Administrators Dashboard</a>
    </div>
  </div>
</body>
</html>
