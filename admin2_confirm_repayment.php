<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin2Id'])) {
    die("Unauthorized access");
}
$admin2_id = $_SESSION['admin2Id'];
$message = "";

// ✅ Approve repayment
if (isset($_GET['approve_id']) && is_numeric($_GET['approve_id'])) {
    $repayment_id = intval($_GET['approve_id']);

    // Fetch repayment details
    $stmt = $conn->prepare("
        SELECT member_id, loan_id, amount_paid 
        FROM loan_repayments 
        WHERE loan_repayments_id = ? AND status = 'acknowledged'
    ");
    $stmt->bind_param("i", $repayment_id);
    $stmt->execute();
    $stmt->bind_result($member_id, $loan_id, $amount_paid);

    if ($stmt->fetch()) {
        $stmt->close();
        $conn->begin_transaction();

        try {
            // ✅ Deduct repayment from loan balance
            $update = $conn->prepare("
                UPDATE loans 
                SET loan_balance = CASE 
                                      WHEN loan_balance >= ? THEN loan_balance - ? 
                                      ELSE loan_balance 
                                   END
                WHERE loan_id = ? AND member_id = ?
            ");
            $update->bind_param("ddii", $amount_paid, $amount_paid, $loan_id, $member_id);
            $update->execute();
            $update->close();

            // ✅ Mark repayment as approved
            $approve = $conn->prepare("
                UPDATE loan_repayments 
                SET status = 'approved', approved_by = ?, approved_at = NOW() 
                WHERE loan_repayments_id = ?
            ");
            $approve->bind_param("ii", $admin2_id, $repayment_id);
            $approve->execute();
            $approve->close();

            // ✅ If loan is fully repaid, mark as completed
            $check = $conn->prepare("SELECT loan_balance FROM loans WHERE loan_id = ?");
            $check->bind_param("i", $loan_id);
            $check->execute();
            $check->bind_result($loan_balance);
            if ($check->fetch() && $loan_balance <= 0) {
                $check->close();
                $done = $conn->prepare("UPDATE loans SET loan_status = 'Fully Repaid' WHERE loan_id = ?");
                $done->bind_param("i", $loan_id);
                $done->execute();
                $done->close();
            } else {
                $check->close();
            }

            $conn->commit();
            $message = "<div class='message'>✅ Loan repayment approved and balance updated.</div>";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "<div class='message error'>❌ Transaction failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='message error'>❌ Invalid or unacknowledged repayment.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin2 - Approve Loan Repayments</title>
  <style>
    body{font-family:Arial,sans-serif;background:#f4f6f8;margin:0;padding:20px;}
    h2{text-align:center;color:#333;}
    table{width:100%;background:#fff;border-collapse:collapse;margin-top:20px;box-shadow:0 0 8px rgba(0,0,0,.05);}
    th,td{padding:12px 14px;text-align:left;border-bottom:1px solid #ddd;}
    th{background:#007bff;color:#fff;text-transform:uppercase;font-size:14px;}
    tr:hover{background:#eef6ff;}
    .btn{padding:7px 12px;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;display:inline-block}
    .btn-receipt{background:#17a2b8}.btn-action{background:#28a745}
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
<h2>📥 Loan Repayments Awaiting Final Approval</h2>
<?= $message ?>
<table>
  <tr><th>Member ID</th><th>Loan ID</th><th>Amount (₦)</th><th>Receipt</th><th>Action</th></tr>
  <?php
  $stmt = $conn->prepare("
      SELECT loan_repayments_id, member_id, loan_id, amount_paid, receipt_file 
      FROM loan_repayments 
      WHERE status = 'acknowledged' 
      ORDER BY loan_repayments_id DESC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()):
  ?>
  <tr>
    <td><?= htmlspecialchars($row['member_id']) ?></td>
    <td><?= htmlspecialchars($row['loan_id']) ?></td>
    <td><?= number_format((float)$row['amount_paid'],2) ?></td>
    <td>
      <?php if (!empty($row['receipt_file'])): ?>
        <a class="btn btn-receipt" href="<?= htmlspecialchars($row['receipt_file']) ?>" target="_blank">View</a>
      <?php else: ?>
        No Receipt
      <?php endif; ?>
    </td>
    <td>
      <a class="btn btn-action" href="?approve_id=<?= (int)$row['loan_repayments_id'] ?>" onclick="return confirm('Approve this repayment?')">✅ Approve</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<div class="center-container">
  <a class="logout-btn" href="admin2.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
