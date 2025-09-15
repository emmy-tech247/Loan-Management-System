<?php
session_start();
include('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT staff_id, email, password_hash, role FROM admin_panel WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($adminId, $adminEmail, $passwordFromDB, $role);
        $stmt->fetch();

        // Check if role is admin1 only
        if ($role !== 'admin') {
            $error = "Access restricted to Admin1.";
        } elseif (password_verify($password, $passwordFromDB)) {
            $_SESSION['adminId'] = $adminId;
            $_SESSION['admin_loginId'] = $adminEmail;
            $_SESSION['role'] = $role;

            header("Location: admin1.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin1 not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin1 Login</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 10px;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 360px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    input, button {
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
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
    .center-container a {
      text-decoration: none;
      color: #007bff;
      font-size: 14px;
    }
    .center-container a:hover {
      text-decoration: underline;
    }

    @media (max-width: 400px) {
      .login-box {
        padding: 20px;
      }
      input, button {
        font-size: 14px;
        padding: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔐 Admin Login</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>

    <div class="center-container">
      <a href="forgot_password.php">Forgot Password?</a>
    </div>

    <div class="center-container" style="margin-top: 10px;">
      <a href="admin_panel.php">Back to Administrators Dashboard</a>
    </div>
  </div>
</body>
</html>
