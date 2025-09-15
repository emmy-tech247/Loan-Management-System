<?php
session_start();
require 'vendor/autoload.php'; // For PHPMailer
require __DIR__ . "/admin_email_alert.php"; // ✅ include the reusable admin alert

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ---------- DATABASE CONNECTION ----------
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->query("SET SESSION wait_timeout = 28800");
$conn->query("SET SESSION interactive_timeout = 28800");
$conn->query("SET NAMES 'utf8mb4'");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password_hash'] ?? '';

    $sql = "SELECT * FROM members WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($member = $result->fetch_assoc()) {
        // Check if account is locked
        if ($member['account_locked'] == 1) {
            $lockTime = strtotime($member['lock_time']);
            $unlockTime = strtotime('+5 hours', $lockTime);

            if (time() >= $unlockTime) {
                $unlock = $conn->prepare("UPDATE members SET account_locked = 0, failed_attempts = 0, lock_time = NULL WHERE member_id = ?");
                $unlock->bind_param("i", $member['member_id']);
                $unlock->execute();
                $member['account_locked'] = 0;
                $member['failed_attempts'] = 0;
            } else {
                $remaining = $unlockTime - time();
                $hours = floor($remaining / 3600);
                $minutes = floor(($remaining % 3600) / 60);
                $error = "Your account is locked. Please wait {$hours}h {$minutes}m or contact support.";
            }
        }

        // If account is not locked now
        if (empty($error)) {
            if (password_verify($password, $member['password_hash'])) {
                $reset = $conn->prepare("UPDATE members SET failed_attempts = 0 WHERE member_id = ?");
                $reset->bind_param("i", $member['member_id']);
                $reset->execute();

                $_SESSION['member_id'] = $member['member_id'];
                $_SESSION['role'] = $member['role'];
                header("Location: member.php");
                exit();
            } else {
                $failed = $member['failed_attempts'] + 1;

                if ($failed >= 5) {
                    $lock = $conn->prepare("UPDATE members SET failed_attempts = ?, account_locked = 1, lock_time = NOW() WHERE member_id = ?");
                    $lock->bind_param("ii", $failed, $member['member_id']);
                    $lock->execute();

                    $error = "Your account has been locked due to too many failed login attempts.";

                    // 🚨 use the reusable alert function
                    send_admin_alert("FAILED LOGIN LOCK", $member['email']);
                } else {
                    $update = $conn->prepare("UPDATE members SET failed_attempts = ? WHERE member_id = ?");
                    $update->bind_param("ii", $failed, $member['member_id']);
                    $update->execute();
                    $error = "Incorrect password. Attempt $failed of 5.";
                }
                log_failed_login($conn, $member['member_id'], $member['email'], "FAILED_LOGIN");
            }
        }
    } else {
        $error = "Account not found.";
    }
}

/**
 * Handle Forgot Password Requests
 */
if (isset($_GET['forgot'])) {
    $ip = $_SERVER['REMOTE_ADDR'];

    // Log the request
    $stmt = $conn->prepare("INSERT INTO login_attempts (member_id, email, attempt_time, ip_address, status) VALUES (NULL, ?, NOW(), ?, 'FORGOT_PASSWORD')");
    $stmt->bind_param("ss", $_GET['forgot'], $ip);
    $stmt->execute();

    // 🚨 Reuse admin alert for forgot password
    send_admin_alert("FORGOT PASSWORD REQUEST", $_GET['forgot']);

    // Delay functionality by 5 minutes
    sleep(300);

    header("Location: forgot_password.php?email=" . urlencode($_GET['forgot']));
    exit();
}

/**
 * Log failed login attempt
 */
function log_failed_login($conn, $member_id, $email, $type) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO login_attempts (member_id, email, attempt_time, ip_address, status) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->bind_param('isss', $member_id, $email, $ip, $type);
    $stmt->execute();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Secure Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 10px;
    }
    .login-box {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }
    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin-bottom: 1rem;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }
    button {
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background-color: #0056b3;
    }
    .login-box a {
      text-align: center;
      display: block;
      font-size: 0.9rem;
      color: #007bff;
      text-decoration: none;
    }
    .login-box a:focus,
    .login-box a:hover {
      text-decoration: underline;
    }
    .error {
      color: red;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      text-align: center;
    }

    @media (max-width: 480px) {
      .login-box {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Email or Phone" required />
      <input type="password" name="password_hash" placeholder="Password" required />
      <button type="submit" name="submit">Login</button>
      <a href="forgot_password.php">Forgot Password?</a>
    </form>
  </div>
</body>
</html>
