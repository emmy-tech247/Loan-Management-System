<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: member.php");
    exit;
}

// Database connection
$host = 'localhost';
$user = 'root';
$password_db = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve GET parameters
$reference = $_GET['reference'] ?? '';
$type = $_GET['type'] ?? '';
$amount = $_GET['amount'] ?? '';

if (!$reference || !$type || !$amount) {
    die('Invalid request. Missing payment details.');
}

// Paystack Secret Key
$secretKey = "sk_test_22525ea6d05f20aa98a38ae1a4e2f7376f08bd6d"; // Replace with your secret key

// Verify payment via Paystack API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $secretKey",
        "Cache-Control: no-cache",
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("Paystack Error: $err");
}

$result = json_decode($response, true);

if ($result && $result['status'] && $result['data']['status'] === 'success') {
    $member_id = $_SESSION['member_id'];
    $email = $result['data']['customer']['email'];
    $amountPaid = $result['data']['amount'] / 100;
    $paidAt = $result['data']['paid_at'];
    $verifiedRef = $result['data']['reference'];

    // Prevent duplicate reference
    $check = $conn->prepare("SELECT id FROM transactions WHERE reference = ?");
    $check->bind_param("s", $verifiedRef);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // Record general transaction
        $insertTxn = $conn->prepare("INSERT INTO transactions (member_id, email, amount, type, reference, status, paid_at)
                                     VALUES (?, ?, ?, ?, ?, 'success', ?)");
        $insertTxn->bind_param("isdsss", $member_id, $email, $amountPaid, $type, $verifiedRef, $paidAt);
        $insertTxn->execute();

        // Record into specific table
        if ($type === "monthly_savings") {
            $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, amount, method, reference)
                                    VALUES (?, ?, 'Paystack', ?)");
            $stmt->bind_param("ids", $member_id, $amountPaid, $verifiedRef);
            $stmt->execute();
        } elseif ($type === "loan_repayment") {
            // Get latest loan
            $loanQuery = $conn->prepare("SELECT id FROM loans WHERE member_id = ? ORDER BY id DESC LIMIT 1");
            $loanQuery->bind_param("i", $member_id);
            $loanQuery->execute();
            $loanQuery->bind_result($loan_id);
            $loanQuery->fetch();
            $loanQuery->close();

            if (!empty($loan_id)) {
                $stmt = $conn->prepare("INSERT INTO repayments (loan_id, amount_paid, payment_date, method, reference)
                                        VALUES (?, ?, ?, 'Paystack', ?)");
                $stmt->bind_param("idss", $loan_id, $amountPaid, $paidAt, $verifiedRef);
                $stmt->execute();
            }
        }

        // Notify success
        $_SESSION['payment_status'] = 'success';
        $_SESSION['payment_type'] = $type;
        $_SESSION['payment_amount'] = $amountPaid;

        header("Location: ../dashboard/member.php");
        exit;
    } else {
        echo "<h3>This transaction has already been recorded.</h3>";
        echo "<a href='../dashboard/member.php'>← Back to Dashboard</a>";
    }
} else {
    echo "<h3>❌ Payment verification failed.</h3>";
    echo "Reference: " . htmlspecialchars($reference);
    echo "<br><a href='../dashboard/member.php'>← Back to Dashboard</a>";
}
?>
