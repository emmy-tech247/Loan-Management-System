<?php
session_start();
include("db.php");
require 'send_email.php'; // Your PHPMailer setup with sendResetEmail()

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT member_id FROM members WHERE email_address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Get user ID
        $stmt->bind_result($memberId);
        $stmt->fetch();

        // Generate secure reset token and hash it
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes from now

        // Save token hash and expiry to DB
        $update = $conn->prepare("UPDATE members SET reset_token = ?, reset_expires = ? WHERE member_id = ?");
        $update->bind_param("ssi", $token_hash, $expiry, $memberId);
        $update->execute();

        // Create actual reset link with plain token (not hashed)
        $resetLink = "http://localhost/reset_password.php?token=$token";

        // Send email
        if (sendResetEmail($email, $resetLink)) {
            $success = "✅ A password reset link has been sent to your email.";
        } else {
            $error = "❌ Failed to send the email. Try again later.";
        }
    } else {
        $error = "❌ No account found with that email address.";
    }

    $stmt->close();
    $conn->close();
}
?>


