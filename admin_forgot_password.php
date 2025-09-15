<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer with correct path
require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/vendor/autoload.php';


include('db.php');
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_panel WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save token to DB
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();

            $reset_link = "http://localhost/LOAN_SYSTEM/admin_reset_password.php?token=$token";

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';         // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@gmail.com';  // Your Gmail address
                $mail->Password = 'your_app_password';     // Use an App Password if Gmail
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Email content
                $mail->setFrom('your_email@gmail.com', 'Loan Admin');
                $mail->addAddress($email);
                $mail->Subject = 'Admin Password Reset Request';
                $mail->Body = "Click the link below to reset your password:\n\n$reset_link\n\nThis link expires in 1 hour.";

                $mail->send();
                $success = "Password reset link sent to your email.";
            } catch (Exception $e) {
                $error = "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>
