<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin1Id'])) {
    die("Unauthorized access");
}
$admin1_id = $_SESSION['admin1Id'];

$message = "";

// ✅ Handle acknowledgement
if (isset($_GET['ack_id']) && is_numeric($_GET['ack_id'])) {
    $repayment_id = intval($_GET['ack_id']);

    $stmt = $conn->prepare("SELECT loan_repayments_id FROM loan_repayments WHERE loan_repayments_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $repayment_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $ack = $conn->prepare("UPDATE loan_repayments 
                               SET status = 'acknowledged', acknowledged_by = ?, acknowledged_at = NOW() 
                               WHERE loan_repayments_id = ?");
        $ack->bind_param("ii", $admin1_id, $repayment_id);
        $ack->execute();

        $message = "<div class='message'>✅ Repayment acknowledged and sent to Admin2 for approval.</div>";
    } else {
        $message = "<div class='message error'>❌ Invalid repayment or already processed.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin1 - Acknowledge Loan Repayments</title>
  <style>
    body{font-family:Arial, sans-serif;background:#f4f6f8;margin:0;padding:20px;}
    h2{text-align:center;color:#333;}
    table{width:100%;background:#fff;border-collapse:collapse;margin-top:20px;box-shadow:0 0 10px rgba(0,0,0,.05);}
    th,td{padding:12px 15px;text-align:left;border-bottom:1px solid #ddd;}
    th{background:#007bff;color:#fff;font-size:14px;text-transform:uppercase;}
    tr:hover{background:#f1f1f1;}
    .btn{padding:7px 12px;background:#28a745;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;}
    .btn-receipt{background:#17a2b8;}
    .btn:hover{opacity:.9}
    .message{max-width:600px;margin:15px auto;padding:10px;border-radius:4px;text-align:center;}
    .message{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
    .message.error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    .center-container{display:flex;justify-content:center;margin:30px 0;}
    .logout-btn{background:#007bff;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;}
    .logout-btn:hover{background:#0056b3;}
    @media(max-width:768px){th,td{font-size:13px;padding:10px}.btn{font-size:12px;padding:6px 10px}.logout-btn{font-size:13px}}
  </style>
</head>
<body>
<h2>Pending Loan Repayments - Admin1 Acknowledgment</h2>
<?= $message ?>
<table>
  <tr><th>Member ID</th><th>Loan ID</th><th>Amount (₦)</th><th>Receipt</th><th>Action</th></tr>
  <?php
  $result = $conn->query("SELECT loan_repayments_id, member_id, loan_id, amount_paid, receipt_file FROM loan_repayments WHERE status = 'pending' ORDER BY loan_repayments_id DESC");
  while($row=$result->fetch_assoc()):
  ?>
  <tr>
    <td><?= htmlspecialchars($row['member_id']) ?></td>
    <td><?= htmlspecialchars($row['loan_id']) ?></td>
    <td><?= number_format((float)$row['amount_paid'],2) ?></td>
    <td><a class="btn btn-receipt" href="<?= htmlspecialchars($row['receipt_file']) ?>" target="_blank">View</a></td>
    <td><a class="btn" href="?ack_id=<?= (int)$row['loan_repayments_id'] ?>" onclick="return confirm('Acknowledge this repayment?')">Acknowledge</a></td>
  </tr>
  <?php endwhile; ?>
</table>
<div class="center-container">
  <a class="logout-btn" href="admin1.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
