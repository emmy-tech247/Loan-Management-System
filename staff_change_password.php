<?php
session_start();
require_once "db.php";

// ✅ Ensure staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_SESSION['staff_id'];
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];

    // ✅ Fetch existing password
    $stmt = $conn->prepare("SELECT password FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // ✅ Verify old password
    if (password_verify($current_password, $hashed_password)) {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE staff SET password=? WHERE staff_id=?");
        $update->bind_param("si", $new_hashed, $staff_id);
        if ($update->execute()) {
            $message = "✅ Password updated successfully!";
        } else {
            $message = "❌ Error updating password.";
        }
        $update->close();
    } else {
        $message = "❌ Current password incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success { color: green; }
        .error { color: red; }
        label {
            font-size: 14px;
            font-weight: 500;
            color: #555;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus {
            border-color: #007bff;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        @media (max-width: 500px) {
            .container {
                margin: 0 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if ($message): ?>
            <p class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>

            <label>New Password:</label>
            <input type="password" name="new_password" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
