<?php
include 'db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    die("⛔ Unauthorized. Please log in.");
}

$member_id = $_SESSION['member_id'];

// Fetch recent transactions
$stmt = $conn->prepare("SELECT * FROM savings_transactions WHERE member_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total savings and withdrawals
$summaryStmt = $conn->prepare("
    SELECT 
        SUM(CASE WHEN type = 'deposit' AND status = 'approved' THEN amount_saved ELSE 0 END) AS total_deposits,
        SUM(CASE WHEN type = 'withdrawal' AND status = 'approved' THEN amount_saved ELSE 0 END) AS total_withdrawals
    FROM savings_transactions
    WHERE member_id = ?
");
$summaryStmt->bind_param("i", $member_id);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>💰 Savings Alerts & Summary</title>
  <!-- 🔄 Auto-refresh every 10 seconds -->
  <meta http-equiv="refresh" content="10">
  <style>
    /* (your styles remain unchanged) */

    /* Base Styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f4;
      color: #333;
      margin: 0;
      padding: 0;
    }

    h2 {
      color: #2c3e50;
      margin-top: 40px;
      text-align: center;
      font-size: 26px;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar .left a:hover {
      background: none !important;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 18px 25px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .navbar a:hover:not(.left a) {
      background-color: #007bff;
      border-radius: 5px;
    }

    .navbar img {
      display: block;
      padding: 0;
      margin: -50px -50px -20px -50px;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 30px auto;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 12px 16px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }

    th {
      background-color: #2980b9;
      color: white;
      text-transform: uppercase;
      font-size: 14px;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #eaf2f8;
    }

    /* Summary */
    .summary {
      max-width: 800px;
      margin: 30px auto;
      font-size: 18px;
      padding: 20px;
      background-color: #fff;
      border-left: 5px solid #2ecc71;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .summary p {
      margin: 10px 0;
    }

    .summary span {
      font-weight: bold;
      font-size: 20px;
      color: #2ecc71;
    }

    .summary span.withdrawal {
      color: #e74c3c;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 100px;
    }

    /* Responsive Table */
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead {
        display: none;
      }

      tr {
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        background-color: #fff;
      }

      td {
        position: relative;
        padding-left: 50%;
        text-align: left;
        border: none;
        border-bottom: 1px solid #eee;
      }

      td::before {
        content: attr(data-label);
        position: absolute;
        left: 16px;
        top: 12px;
        font-weight: bold;
        color: #555;
        width: 45%;
        white-space: nowrap;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="left">
      <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80"></a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="set_saving.html">Monthly Savings<br>Amount</a>
      <a href="savings_transactions.php">Transaction Alerts<br>and Summaries</a>
      <a href="upload_topups_receipt.php">Savings</a>
      <a href="withdraw_funds.php">Withdrawal</a>
    </div>
  </div>

  <h2>🔔 Recent Savings Transactions</h2>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Amount</th>
        <th>Type</th>
        <th>Reference</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td data-label="#"><?= $row['id'] ?></td>
            <td data-label="Amount">₦<?= number_format($row['amount_saved'], 2) ?></td>
            <td data-label="Type"><?= ucfirst($row['type']) ?></td>
            <td data-label="Reference"><?= $row['reference'] ?></td>
            <td data-label="Status"><?= ucfirst($row['status']) ?></td>
            <td data-label="Date"><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">No transactions found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="summary">
    <p>✅ Total Deposits: <span>₦<?= number_format($summary['total_deposits'], 2) ?></span></p>
    <p>🔻 Total Withdrawals: <span class="withdrawal">₦<?= number_format($summary['total_withdrawals'], 2) ?></span></p>
    <p>📌 Net Balance: <span>₦<?= number_format($summary['total_deposits'] - $summary['total_withdrawals'], 2) ?></span></p>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
