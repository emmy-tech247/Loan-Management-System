<?php
$conn = new mysqli('localhost', 'root', '', 'loan_system');
if ($conn->connect_error) { die("DB error: " . $conn->connect_error); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT otp_code, otp_expires FROM members WHERE email_address=? AND otp_verified=0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_otp, $otp_expires);
    if ($stmt->fetch()) {
        if ($otp == $db_otp && strtotime($otp_expires) > time()) {
            $stmt->close();
            $update = $conn->prepare("UPDATE members SET otp_verified=1 WHERE email_address=?");
            $update->bind_param("s", $email);
            $update->execute();
            echo "✅ Email verified successfully! You may now log in.";
        } else {
            echo "❌ Invalid or expired OTP.";
        }
    } else {
        echo "❌ No pending verification found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Verify Email</title>
</head>
<body>
  <h2>Email Verification</h2>
  <form method="POST">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
    <label>Enter OTP:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
  </form>
</body>
</html>
