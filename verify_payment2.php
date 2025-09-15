<?php
$reference = $_GET['reference'];
if (!$reference) {
    die('No reference supplied');
}

$secretKey = "sk_test_xxxxxxxxxxxxxxxxxxxxxxxxx"; // Replace with your secret key

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $secretKey",
        "Cache-Control: no-cache",
    ],
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error: $err");
}

$result = json_decode($response, true);

if ($result && $result['status'] && $result['data']['status'] === 'success') {
    // ✅ Payment was successful
    $email = $result['data']['customer']['email'];
    $amount = $result['data']['amount'] / 100;
    $reference = $result['data']['reference'];

    // 👉 Save to DB (pseudo code)
    // $stmt = $conn->prepare("INSERT INTO payments (email, amount, reference) VALUES (?, ?, ?)");
    // $stmt->bind_param("sds", $email, $amount, $reference);
    // $stmt->execute();

    echo "<h2>Payment Successful</h2>";
    echo "Reference: " . htmlspecialchars($reference) . "<br>";
    echo "Amount: ₦" . number_format($amount, 2) . "<br>";
    echo "Email: " . htmlspecialchars($email);
} else {
    echo "<h2>Payment Verification Failed</h2>";
    var_dump($result);
}
