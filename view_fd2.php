<?php
session_start();
require 'db.php';
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}
$member_id = $_SESSION['member_id'];

$stmt = $conn->prepare("SELECT id, amount_deposited, tenure_months, interest_rate, status, created_at 
                        FROM fixed_deposits 
                        WHERE member_id = ? 
                        ORDER BY created_at DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>My Fixed Deposit Records</title></head>
<body>
<h2>My Fixed Deposits</h2>
<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Amount</th>
  <th>Tenure</th>
  <th>Rate</th>
  <th>Status</th>
  <th>Applied On</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td>₦<?= number_format($row['amount_deposited'],2) ?></td>
  <td><?= $row['tenure_months'] ?> months</td>
  <td><?= $row['interest_rate'] ?>%</td>
  <td><?= ucfirst($row['status']) ?></td>
  <td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
