<?php
session_start();
require_once('db.php');

// ✅ Fetch pending fixed deposit approvals for Admin2 (Step 2)
$sql = "SELECT 
            a.id AS approval_id, 
            fd.id AS fd_id, 
            fd.amount_deposited, 
            fd.tenure_months, 
            fd.interest_rate, 
            fd.maturity_date
        FROM approvals a
        JOIN fixed_deposits fd ON a.item_id = fd.id
        WHERE a.item_type = 'fixed_deposit' 
          AND a.step = 2 
          AND a.status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Confirm Fixed Deposits</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    h2 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 30px;
    }

    table {
      width: 95%;
      margin: auto;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    th, td {
      padding: 12px 14px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #3498db;
      color: #fff;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    button {
      padding: 8px 16px;
      background-color: #2ecc71;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #27ae60;
    }

    form {
      margin: 0;
    }

    .center-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }

    .logout-btn {
      background-color: #007bff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    .logout-btn:active {
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 14px;
      }

      button {
        padding: 6px 12px;
        font-size: 14px;
      }

      .logout-btn {
        font-size: 14px;
        padding: 10px 20px;
      }

      body {
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <h2>✅ Confirm Fixed Deposit Applications</h2>

  <table>
    <tr>
      <th>FD ID</th>
      <th>Amount</th>
      <th>Tenure</th>
      <th>Rate</th>
      <th>Maturity</th>
      <th>Action</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($fd = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($fd['fd_id']) ?></td>
          <td>₦<?= number_format((float)$fd['amount_deposited'], 2) ?></td>
          <td><?= htmlspecialchars($fd['tenure_months']) ?> months</td>
          <td><?= htmlspecialchars($fd['interest_rate']) ?>%</td>
          <td><?= htmlspecialchars($fd['maturity_date']) ?></td>
          <td>
            <form action="confirm_fd_action.php" method="POST">
              <input type="hidden" name="approval_id" value="<?= htmlspecialchars($fd['approval_id']) ?>">
              <input type="hidden" name="fd_id" value="<?= htmlspecialchars($fd['fd_id']) ?>">
              <input type="hidden" name="action" value="confirm">
              <button type="submit">Confirm</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No pending fixed deposits to confirm.</td></tr>
    <?php endif; ?>
  </table>

  <div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
  </div>
</body>
</html>

<?php $conn->close(); ?>
