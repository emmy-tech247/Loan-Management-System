<?php
session_start();
require_once 'db.php';


// Acknowledge Action
if (isset($_GET['acknowledge_id'])) {
    $id = intval($_GET['acknowledge_id']);
    $stmt = $conn->prepare("UPDATE savings_transactions SET status = 'acknowledged', acknowledged_by = ? WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $admin1_id, $id);
    $stmt->execute();
    echo "<div class='message'>✅ Acknowledged by Admin1.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manual Savings Acknowledgement</title>
  <meta name="robots" content="noindex, nofollow" />
  <style>
    body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f8f9fa;color:#333;padding:20px;margin:0}
    h3{color:#004085;border-bottom:2px solid #ccc;padding-bottom:10px;margin-bottom:20px;font-size:22px;text-align:center}
    .receipt-card{background:#fff;border:1px solid #dee2e6;border-left:5px solid #007bff;padding:15px;margin-bottom:15px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.05)}
    .receipt-card p{margin:5px 0}
    .receipt-card a{text-decoration:none;color:#007bff;font-weight:500}
    .receipt-card a:hover{text-decoration:underline}
    .ack-button{display:inline-block;margin-top:10px;padding:6px 12px;background:#28a745;color:#fff;border-radius:5px;text-decoration:none;font-weight:bold;transition:.2s ease}
    .ack-button:hover{background:#218838}
    hr{border:none;border-top:1px solid #ccc}
    .message{background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:10px;margin-bottom:15px;border-radius:5px;text-align:center}
    .center-container{display:flex;justify-content:center;align-items:center;margin:40px 0}
    .logout-btn{display:inline-block;background:#007bff;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;transition:background-color .3s ease,transform .2s ease;box-shadow:0 4px 6px rgba(0,0,0,0.1)}
    .logout-btn:hover{background:#0056b3;transform:translateY(-2px)}
    .logout-btn:active{transform:translateY(0)}
    @media(max-width:768px){
      h3{font-size:20px}
      .receipt-card{padding:12px}
      .ack-button,.logout-btn{font-size:14px;padding:10px 16px}
    }
    @media(max-width:480px){
      .receipt-card p,.receipt-card a{font-size:14px}
      .center-container{flex-direction:column;padding:0 10px}
    }
  </style>
</head>
<body>

<h3>Pending Manual Savings (for Admin1)</h3>

<?php
$result = $conn->query("SELECT * FROM savings_transactions WHERE status = 'pending'");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $memberId = htmlspecialchars($row['member_id']);
        $amount = number_format((float)$row['amount_saved'], 2);
        $receiptFile = htmlspecialchars($row['receipt_file']);
        $rowId = intval($row['id']);

        echo "<div class='receipt-card'>
            <p><strong>Member ID:</strong> {$memberId}</p>
            <p><strong>Amount:</strong> ₦{$amount}</p>
            <a href='{$receiptFile}' target='_blank'>📄 View Receipt</a><br>
            <a class='ack-button' href='admin1_acknowledge_topups.php?acknowledge_id={$rowId}'>✅ Acknowledge</a>
        </div>";
    }
} else {
    echo "<p>No pending savings transactions found.</p>";
}
?>

<div class="center-container">
  <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
