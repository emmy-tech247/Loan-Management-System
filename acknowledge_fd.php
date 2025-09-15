<?php
session_start();
require_once('db.php');

// Secure query using prepared statement if needed later
$sql = "SELECT a.id AS approval_id, fd.* FROM approvals a
        JOIN fixed_deposits fd ON a.item_id = fd.id
        WHERE a.item_type = 'fixed_deposit' AND a.step = 1 AND a.status = 'pending'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acknowledge Fixed Deposits</title>
  <meta name="robots" content="noindex, nofollow" />
  <style>
    *{box-sizing:border-box}body{font-family:Arial,sans-serif;background:#f4f6f8;margin:0;padding:20px}h2{text-align:center;color:#333;margin-bottom:30px;font-size:24px}table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 0 10px rgba(0,0,0,0.05)}th,td{padding:12px 15px;border:1px solid #ddd;text-align:center;font-size:14px}th{background:#007bff;color:#fff}tr:nth-child(even){background:#f9f9f9}form{margin:0}.center-container{display:flex;justify-content:center;align-items:center;margin:40px 0}.logout-btn{background:#007bff;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;box-shadow:0 4px 6px rgba(0,0,0,0.1);border:none;cursor:pointer;transition:background-color .3s}.logout-btn:hover{background:#0056b3}button[name="action"]{background:#28a745;color:#fff;border:none;padding:8px 14px;border-radius:4px;cursor:pointer;transition:.2s}button[name="action"]:hover{background:#218838}@media(max-width:768px){table{font-size:12px}th,td{padding:8px 10px}.logout-btn{font-size:14px;padding:10px 20px}button[name="action"]{padding:6px 10px;font-size:12px}}@media(max-width:480px){h2{font-size:20px}table{width:100%;display:block;overflow-x:auto}.center-container{flex-direction:column;padding:0 10px}}
  </style>
</head>
<body>

  <h2>📝 Acknowledge Fixed Deposit Applications</h2>

  <table>
    <tr>
      <th>FD ID</th>
      <th>Amount</th>
      <th>Tenure</th>
      <th>Rate</th>
      <th>Start Date</th>
      <th>Actions</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($fd = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($fd['id']) ?></td>
          <td>₦<?= number_format((float)$fd['amount_deposited'], 2) ?></td>
          <td><?= htmlspecialchars($fd['tenure_months']) ?> months</td>
          <td><?= htmlspecialchars($fd['interest_rate']) ?>%</td>
          <td><?= htmlspecialchars($fd['start_date']) ?></td>
          <td>
            <form method="POST" action="acknowledge_fd_action.php">
              <input type="hidden" name="approval_id" value="<?= htmlspecialchars($fd['approval_id']) ?>">
              <input type="hidden" name="fd_id" value="<?= htmlspecialchars($fd['id']) ?>">
              <button type="submit" name="action" value="acknowledge">Acknowledge</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No pending approvals found.</td></tr>
    <?php endif; ?>
  </table>

  <div class="center-container">
    <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
  </div>

</body>
</html>
