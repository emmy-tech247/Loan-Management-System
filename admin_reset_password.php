<?php
session_start();
include 'db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } else {
        // Hash the token for verification
        $token_hash = hash("sha256", $token);
        $stmt = $conn->prepare("SELECT id, reset_token_expires_at FROM admin_panel WHERE reset_token_hash = ?");
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {
            if (strtotime($admin['reset_token_expires_at']) >= time()) {
                // Token valid, reset password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_stmt = $conn->prepare("UPDATE admin_panel SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $admin['id']);
                $update_stmt->execute();

                $success = "✅ Password reset successful. You can now <a href='admin_login.php'>login</a>.";
            } else {
                $error = "❌ Token has expired.";
            }
        } else {
            $error = "❌ Invalid token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 350px;
    }
    .box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .box input, .box button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .box button {
      background: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    .box button:hover {
      background: #218838;
    }
    .error {
      color: red;
      text-align: center;
    }
    .success {
      color: green;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Reset Password</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
      <p class="success"><?= $success ?></p>
    <?php else: ?>
    <form method="POST" action="">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="password" name="password" placeholder="New Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
