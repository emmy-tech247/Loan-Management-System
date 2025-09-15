<?php
session_start();
require_once "db.php";

$error = "";

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch staff user by email
    $stmt = $conn->prepare("SELECT * FROM admin_panel WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash'])) {
            // ✅ Store general session data
            $_SESSION['staff_id'] = $row['staff_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            // ✅ Role-based session + redirect
            switch (strtolower($row['role'])) {
                case 'admin':
                    $_SESSION['admin1Id'] = $row['staff_id'];
                    header("Location: admin1.php");
                    break;

                case 'auditor':
                    $_SESSION['auditorId'] = $row['staff_id'];
                    header("Location: auditor.php");
                    break;

                case 'accountant':
                    $_SESSION['accountantId'] = $row['staff_id'];
                    header("Location: accountant.php");
                    break;

                case 'relationship_officer':
                    $_SESSION['relationship_officerId'] = $row['staff_id'];
                    header("Location: relationship_officer.php");
                    break;

                case 'manager':
                    $_SESSION['managerId'] = $row['staff_id'];
                    header("Location: manager.php");
                    break;

                case 'md':
                    $_SESSION['admin2Id'] = $row['staff_id'];
                    header("Location: admin2.php");
                    break;

                default:
                    $error = "Role not recognized. Contact system administrator.";
                    break;
            }
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
  <title>Staff Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
      max-width: 360px;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
      color: #333;
    }
    .login-box input, .login-box button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    .login-box button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }
    .login-box button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
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
    <h2>Staff Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="submit">Login</button>
    </form>
    <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
  </div>
</body>
</html>
