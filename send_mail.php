<?php
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// ✅ Collect form data safely
$name    = isset($_POST["name"]) ? trim($_POST["name"]) : "Anonymous";
$email   = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$subject = isset($_POST["subject"]) ? trim($_POST["subject"]) : "No Subject";
$message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

// ✅ Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Debugging

    $mail->isSMTP();
    $mail->SMTPAuth   = true;
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->Username   = "loansystem247@gmail.com";   // Your Gmail
    $mail->Password   = "xmihetehhjqhylea";          // App password

    // ✅ Always use your own email as sender
    $mail->setFrom("dave@example.com", "DCG MULTI-PURPOSE COOPERATIVE SOCIETY LIMITED ");

    // ✅ Add recipient (admin)
    $mail->addAddress("loansystem247@gmail.com  ", "Loan System");

    // ✅ Optional: show sender info in Reply-To
    if (!empty($email)) {
        $mail->addReplyTo($email, $name);
    }

    $mail->Subject = $subject;
    $mail->Body    = "Message from: $name <$email>\n\n" . $message;

    $mail->send();

    header("Location: sent.html");
    exit;
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}
