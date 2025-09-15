<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM admin_panel WHERE email = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['admin_panelId'] = $row['id'];

            header("Location: admin_panel.php");
            exit();
        } else {
            $error = "Incorrect email or password.";
        }
    } else {
        $error = "Incorrect email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Login</title>
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
      width: 100%;
      max-width: 360px;
      box-sizing: border-box;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .login-box input,
    .login-box button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    .login-box button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    .login-box button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .forgot-password {
      text-align: center;
      margin-top: 10px;
    }
    .forgot-password a {
      text-decoration: none;
      color: #007bff;
      font-size: 14px;
    }
    .forgot-password a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Staff Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="submit">Login</button>
    </form>

    <div class="forgot-password">
      <a href="admin_forgot_password.php">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
