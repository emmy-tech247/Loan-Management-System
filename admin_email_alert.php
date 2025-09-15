<?php
// admin_alert.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

function send_admin_alert($alert_type, $member_email) {
    $mail = new PHPMailer(true);

    // Admin emails
    $admin1 = "alaoemmanuel1978@gmail.com";
    $admin2 = "alaoemmanuel1978@gmail.com";

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com"; 
        $mail->SMTPAuth = true;
        $mail->Username = "loansystem247@gmail.com";      // <-- Your Gmail
        $mail->Password = "xmihetehhjqhylea";         // <-- Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("no-reply@example.com", "DCG MULTI-PURPOSE COOPERATIVE SOCIETY LIMITED");
        $mail->addAddress($admin1);
        $mail->addAddress($admin2);

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date("Y-m-d H:i:s");

        $mail->isHTML(true);
        $mail->Subject = "🚨 Loan System Alert: {$alert_type}";
        $mail->Body = "
            <p>Dear Admin,</p>
            <p>An important security event occurred:</p>
            <ul>
                <li><b>Event:</b> {$alert_type}</li>
                <li><b>User Email:</b> {$member_email}</li>
                <li><b>IP Address:</b> {$ip}</li>
                <li><b>Time:</b> {$time}</li>
            </ul>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Admin alert email failed: {$mail->ErrorInfo}");
    }
}
