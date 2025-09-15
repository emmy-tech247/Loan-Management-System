<?php
// Database Connection
function db_connect() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'loan_system';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'DB connection failed: ' . $conn->connect_error]));
    }

    return $conn;
}

// Sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if email already exists
function email_exists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM members WHERE email_address = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

// Generate random OTP
function generate_otp($length = 6) {
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}

// Get future time for OTP expiration (e.g., 15 minutes from now)
function otp_expiry($minutes = 15) {
    return date('Y-m-d H:i:s', strtotime("+$minutes minutes"));
}

// (Optional) Send OTP via email - requires SMTP setup
function send_otp_email($to, $otp) {
    $subject = "Your Verification Code";
    $message = "Your verification code is: $otp\n\nIt will expire in 15 minutes.";
    $headers = "From: no-reply@yourdomain.com";

    // Use PHP mail() or SMTP (PHPMailer recommended)
    return mail($to, $subject, $message, $headers);
}
?>
