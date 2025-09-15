<?php
session_start();
require 'db.php';

// Check if the user is coming from a valid 2FA step
if (!isset($_SESSION['pending_member'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);
    $member_id = $_SESSION['pending_member'];

    // Fetch OTP and expiry from the database
    $stmt = $conn->prepare("SELECT otp_code, otp_expires FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $stmt->bind_result($otp_code, $otp_expiry);
    $stmt->fetch();
    $stmt->close();

    if (!$otp_code || strtotime($otp_expiry) < time()) {
        echo "OTP expired or invalid.";
        exit();
    }

    if ($entered_otp === $otp_code) {
        // OTP is valid, finalize login
        $_SESSION['member_id'] = $user_id;
        unset($_SESSION['pending_member']);

        // Clear the OTP fields
        $stmt = $conn->prepare("UPDATE members SET otp_code = NULL, otp_expires = NULL WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Invalid OTP. Please try again.";
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
