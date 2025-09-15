<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

// ---- Database Connection ----
$mysqli = require __DIR__ . "/db.php";

// ---- STEP 1: Validate Token ----
$token = $_GET["token"] ?? null;

if (!$token) {
    die("❌ Token not found.");
}

$token_hash = hash("sha256", $token);

$sql = "SELECT member_id, reset_token_expires_at 
        FROM members 
        WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("❌ Invalid or expired token.");
}

// ---- Check if token is expired ----
if (strtotime($user["reset_token_expires_at"]) < time()) {
    die("❌ Token has expired. Please request a new password reset.");
}

// ---- STEP 2: If form submitted, update password ----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";
    $password_confirm = $_POST["password_confirm"] ?? "";

    if (empty($password) || empty($password_confirm)) {
        die("⚠️ Both password fields are required.");
    }

    if ($password !== $password_confirm) {
        die("⚠️ Passwords do not match.");
    }

    // Hash new password
    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Update in DB
    $sql = "UPDATE members
            SET password_hash = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE member_id = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("si", $password_hash, $user["member_id"]);
    $stmt->execute();

    echo "✅ Password has been reset successfully!";
    exit;
}
?>

<!-- STEP 3: Show Reset Password Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Base reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #222;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.3s ease;
        }

        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #007BFF;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                margin: 10px;
                padding: 20px;
            }
            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <form method="post">
            <label>New Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="password_confirm" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
