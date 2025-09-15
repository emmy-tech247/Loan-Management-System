<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

// ---- Database Connection ----
$mysqli = require __DIR__ . "/db.php";

// ---- Get Email from Form ----
$email = $_POST["email"] ?? null;

if (!$email) {
    die("Email address is required.");
}

// ---- Generate Reset Token ----
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// ---- Save Token in Database ----
$sql = "UPDATE members
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // ---- Setup Mailer ----
    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("loansystem247@gmail.com", "Loan System");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset Request";
    $mail->isHTML(true);
    $mail->Body = <<<END
        <p>Click the link below to reset your password:</p>
        <p><a href="http://localhost/LOAN_SYSTEM/reset_password.php?token=$token">Reset Password</a></p>
        <p>This link will expire in 30 minutes.</p>
    END;

    try {
        $mail->send();
        echo "✅ Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    echo "⚠️ No account found with that email.";
}
