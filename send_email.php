<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer's autoloader

function sendResetEmail($to, $link) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // e.g., smtp.gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password'; // NOT your Gmail password. Use App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'DCG Cooperative');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Link';
        $mail->Body    = "Click the link below to reset your password:<br><br><a href='$link'>$link</a><br><br>This link will expire in 30 minutes.";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
