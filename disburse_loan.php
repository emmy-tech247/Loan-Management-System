<?php
session_start();
require 'db.php';
require 'config.php';

if (!isset($_SESSION['accountantId'])) {
    header("Location: accountant_login.php");
    exit();
}

if (!isset($_GET['loan_id'])) {
    die("❌ Invalid Loan ID.");
}

$loan_id = intval($_GET['loan_id']);

// Get loan and member info
$sql = "SELECT l.id AS loan_id, l.amount, l.status, m.username, m.account_number, m.bank_name, m.id AS member_id
        FROM loans l
        JOIN members m ON l.member_id = m.id
        WHERE l.id = ? AND l.status = 'approved_by_manager'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$loan = $result->fetch_assoc()) {
    die("❌ Loan not found or already processed.");
}
$stmt->close();
$bank_code = getBankCode($loan['bank_name']);
if (empty($bank_code)) {
    die("❌ Unsupported or unrecognized bank: {$loan['bank_name']}");
}


// Validate bank info
if (empty($loan['account_number']) || empty($loan['bank_name'])) {
    die("❌ Member bank details are incomplete.");
}

// ✅ Create transfer recipient
$recipientData = [
    "type" => "nuban",
    "name" => $loan['username'],
    "account_number" => $loan['account_number'],
    "bank_code" => getBankCode($loan['bank_name']), // helper
    "currency" => "NGN"
];

$recipientResponse = paystackApi('/transferrecipient', $recipientData);

if (empty($recipientResponse['status']) || !$recipientResponse['status']) {
    $errorMessage = $recipientResponse['message'] ?? 'No message from Paystack';
    die("❌ Failed to create transfer recipient: $errorMessage");
}


$recipient_code = $recipientResponse['data']['recipient_code'];

// ✅ Initiate Transfer
$transferData = [
    "source" => "balance",
    "amount" => intval($loan['amount'] * 100),
    "recipient" => $recipient_code,
    "reason" => "Loan Disbursement - Loan ID: {$loan_id}"
];

$transferResponse = paystackApi('/transfer', $transferData);

if ($transferResponse['status']) {
    // ✅ Mark loan as disbursed
    $conn->query("UPDATE loans SET status = 'disbursed' WHERE id = $loan_id");
    echo "✅ Loan disbursed successfully.";
} else {
    echo "❌ Transfer failed: " . $transferResponse['message'];
}

// ---------- HELPER FUNCTIONS ----------
function getBankCode($bankName) {
    $bankName = strtolower(trim($bankName)); // normalize

    $banks = [
        'access bank' => '044',
        'gtbank' => '058',
        'gt bank' => '058', // alias
        'uba' => '033',
        'zenith bank' => '057',
        'zenith' => '057',  // alias
        'ecobank' => '050',
        'eco bank' => '050', // alias
        'fidelity bank' => '070',
        'fidelity' => '070', // alias
        'first bank' => '011',
        'firstbank' => '011', // alias
        'fcmb' => '214',
        'first city monument bank' => '214', // alias
        'polaris bank' => '076',
        'polaris' => '076', // alias
        'union bank' => '032',
        'union' => '032', // alias
        'stanbic ibtc' => '221',
        'stanbic' => '221', // alias
        'keystone bank' => '082',
        'keystone' => '082', // alias
        'wema bank' => '035',
        'wema' => '035', // alias
        'sterling bank' => '232',
        'sterling' => '232', // alias
        'jaiz bank' => '301',
        'jaiz' => '301', // alias
        'providus bank' => '101',
        'providus' => '101', // alias
        'titan trust bank' => '102',
        'titan bank' => '102', // alias
        'globus bank' => '00103',
        'globus' => '00103', // alias
        'sparkle microfinance bank' => '51310',
        'sparkle mfb' => '51310', // alias
        'kuda bank' => '50211',
        'kuda' => '50211', // alias
        'vfd microfinance bank' => '566',
        'vfd mfb' => '566', // alias
        'rubies microfinance bank' => '125',
        'rubies mfb' => '125', // alias
        'paycom' => '999992',
        'opay' => '999992', // alias
        'palmpay' => '999991',
        'moniepoint' => '50515',
        'moniepoint mfb' => '50515', // alias
        'mint bank' => '50304',
        'mint' => '50304', // alias
        'eye microfinance bank' => '51293',
        'eye mfb' => '51293', // alias
        'trustbond microfinance bank' => '51204',
        'trustbond mfb' => '51204', // alias
        'rosette microfinance bank' => '51313',
        'rosette mfb' => '51313', // alias
        'boi microfinance bank' => '51312',
        'boi mfb' => '51312', // alias
        'mutual trust microfinance bank' => '51229',
        'mutual trust mfb' => '51229', // alias
        'accion microfinance bank' => '51247',
        'accion mfb' => '51247', // alias
        'lapo microfinance bank' => '50549',
        'lapo mfb' => '50549', // alias
        'safe haven microfinance bank' => '51269',
        'safe haven mfb' => '51269', // alias
        'ab microfinance bank' => '51204',
        'ab mfb' => '51204', // alias
        'renmoney microfinance bank' => '101',
        'renmoney' => '101', // alias
        'paga' => '100002',
        'cellulant' => '100001',
        // Add more...

        // Add more aliases as needed
    ];

    return $banks[$bankName] ?? '';
}


function paystackApi($endpoint, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co$endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
?>
