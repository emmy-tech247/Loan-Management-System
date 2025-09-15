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

        if ($role !== 'md') {
            $error = "Access restricted to Managing Director.";
        } elseif (password_verify($password, $passwordFromDB)) { 
            // ✅ Secure password check
            $_SESSION['adminId'] = $adminId;
            $_SESSION['admin_loginId'] = $adminEmail;
            $_SESSION['role'] = $role;

            header("Location: admin2.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Managing Director Login</title>
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
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 360px;
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
      background:rgb(11, 66, 184);
      color: white;
      border: none;
      font-weight: bold;
    }
    button:hover {
      background:rgb(56, 54, 197);
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔐 Managing Director Login</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <div class="center-container">
    <a class="logout-btn" href="admin_login_list.php">Back </a>
  </div>
  
  </div>
</body>
</html>

