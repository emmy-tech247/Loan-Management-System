<?php
session_start();
include('db.php');

$error = "";

// ✅ Login process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admin_panel WHERE email = ? AND role = 'relationship_officer' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                // ✅ Set session values
                $_SESSION['relationship_officer_loginId'] = $user['full_name'];
                $_SESSION['relationship_officerId'] = $user['staff_id'];
                $_SESSION['role'] = 'relationship_officer';

                header("Location: relationship_officer.php");
                exit();
            } else {
                $error = "Incorrect email or password.";
            }
        } else {
            $error = "Incorrect email or password.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Relationship Officer Login</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 10px;
    }
    .login-box {
      background: #fff;
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
      font-size: 14px;
    }
    .login-box button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease-in-out;
    }
    .login-box button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      text-align: center;
      font-size: 14px;
    }
    .center-container {
      text-align: center;
      margin-top: 10px;
    }
    .plain-link {
      font-size: 13px;
      color: #007bff;
      text-decoration: none;
      display: block;
      margin: 5px 0;
    }
    .plain-link:hover {
      text-decoration: underline;
    }
    @media (max-width: 400px) {
      .login-box {
        padding: 20px;
      }
      .login-box h2 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Relationship Officer Login</h2>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="POST" action="">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit" name="submit">Login</button>
  </form>
  <div class="center-container">
    <a class="plain-link" href="forgot_password.php">Forgot Password?</a>
    <a class="plain-link" href="admin_panel.php">Back to Staff Dashboard</a>
  </div>
</div>

</body>
</html>
