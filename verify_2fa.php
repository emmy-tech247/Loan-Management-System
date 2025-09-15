<?php
session_start();

if (!isset($_SESSION['pending_member'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$user = 'root';
$password_db = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password_db, $dbname);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otpInput = trim($_POST['otp']);
    $userId = $_SESSION['pending_member'];

    $stmt = $conn->prepare("SELECT * FROM members WHERE id=? AND otp_code=? AND otp_expires > NOW()");
    $stmt->bind_param("is", $userId, $otpInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // OTP is valid
        $_SESSION['member_id'] = $userId;
        unset($_SESSION['pending_member']);

        // Clear OTP
        $conn->query("UPDATE members SET otp_code=NULL, otp_expires=NULL WHERE id=$userId");

        header("Location: dashboard4.php");
        exit;
    } else {
        $error = "Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <form method="POST" action="">
        <h2>Verify OTP</h2>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </form>
</body>
</html>
