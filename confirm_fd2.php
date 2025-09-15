<?php
session_start();
require 'db.php';
// ensure admin2 is logged in
if (!isset($_SESSION['admin_loginId'])) { header("Location: admin_login2.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fd_id = intval($_POST['fd_id']);
    $action = $_POST['action']; // approve or reject
    $status = ($action === 'approve') ? 'confirmed' : 'rejected';

    $stmt = $conn->prepare("UPDATE fixed_deposits SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $fd_id);
    $stmt->execute();
}

$result = $conn->query("SELECT id, member_id, amount_deposited, tenure_months, interest_rate, status, created_at 
                        FROM fixed_deposits 
                        WHERE status = 'pending' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Confirm Fixed Deposits</title></head>
<body>
<h2>Pending Fixed Deposits</h2>
<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Member</th>
  <th>Amount</th>
  <th>Tenure</th>
  <th>Rate</th>
  <th>Applied On</th>
  <th>Action</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['member_id'] ?></td>
  <td>₦<?= number_format($row['amount_deposited'],2) ?></td>
  <td><?= $row['tenure_months'] ?> months</td>
  <td><?= $row['interest_rate'] ?>%</td>
  <td><?= $row['created_at'] ?></td>
  <td>
    <form method="POST">
      <input type="hidden" name="fd_id" value="<?= $row['id'] ?>">
      <button type="submit" name="action" value="approve">✅ Approve</button>
      <button type="submit" name="action" value="reject">❌ Reject</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
