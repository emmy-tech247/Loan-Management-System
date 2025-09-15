<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'db.php';

// Check if fd_id is set and valid
if (!isset($_POST['fd_id']) || !is_numeric($_POST['fd_id'])) {
    echo "<!DOCTYPE html>
    <html><head><title>Invalid Request</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f2f2f2; text-align: center; padding: 100px; }
      .error-box { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #aaa; display: inline-block; }
      h2 { color: #c0392b; }
      a { display: inline-block; margin-top: 20px; text-decoration: none; color: #2980b9; font-weight: bold; }
    </style></head>
    <body>
      <div class='error-box'>
        <h2>⚠️ Invalid or Missing Fixed Deposit ID</h2>
        <p>Please return to the dashboard and try again.</p>
        <a href='fixed_deposit.php'>&larr; Back to Dashboard</a>
      </div>
    </body></html>";
    exit;
}

$fd_id = (int)$_POST['fd_id'];

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("SELECT * FROM fixed_deposits WHERE id = ?");
$stmt->bind_param("i", $fd_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<!DOCTYPE html>
    <html><head><title>Not Found</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f2f2f2; text-align: center; padding: 100px; }
      .error-box { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #aaa; display: inline-block; }
      h2 { color: #e67e22; }
      a { display: inline-block; margin-top: 20px; text-decoration: none; color: #2980b9; font-weight: bold; }
    </style></head>
    <body>
      <div class='error-box'>
        <h2>❌ No Fixed Deposit Record Found</h2>
        <p>We couldn’t find a record for ID: <strong>$fd_id</strong></p>
        <a href='fixed_deposit.php'>&larr; Back to Dashboard</a>
      </div>
    </body></html>";
    exit;
}

$data = $result->fetch_assoc();

$amount = number_format($data['amount_deposited'], 2);
$tenure = $data['tenure_months'];
$rate = $data['interest_rate'];
$start = $data['start_date'];
$maturity = $data['maturity_date'];

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: "Times New Roman", serif;
      padding: 40px;
      background: #fff;
      color: #000;
    }
    .certificate-box {
      border: 10px solid #333;
      padding: 40px;
      text-align: center;
    }
    .certificate-box h1 {
      font-size: 36px;
      margin-bottom: 10px;
      text-transform: uppercase;
      color: #2c3e50;
    }
    .certificate-box h2 {
      font-size: 24px;
      margin-top: 0;
      color: #555;
    }
    .details {
      margin: 40px 0;
      font-size: 18px;
      text-align: left;
    }
    .details p {
      margin: 8px 0;
    }
    .footer {
      margin-top: 50px;
      text-align: right;
      font-size: 16px;
    }
    .footer p {
      border-top: 1px solid #aaa;
      display: inline-block;
      padding-top: 5px;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="certificate-box">
    <h1>Certificate of Fixed Deposit</h1>
    <h2>This certifies that the following deposit has been duly recorded</h2>

    <div class="details">
      <p><strong>Amount Deposited:</strong> ₦' . $amount . '</p>
      <p><strong>Tenure:</strong> ' . $tenure . ' months</p>
      <p><strong>Interest Rate:</strong> ' . $rate . '% per annum</p>
      <p><strong>Start Date:</strong> ' . $start . '</p>
      <p><strong>Maturity Date:</strong> ' . $maturity . '</p>
    </div>

    <div class="footer">
      <p>Authorized Signature</p>
    </div>
  </div>
</body>
</html>
';

// Generate PDF
$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream("fd_certificate_{$fd_id}.pdf");
?>
