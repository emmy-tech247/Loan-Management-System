<?php
session_start();
require 'db.php';

// Secure session check
if (!isset($_SESSION['member_id'])) {
    die("You must be logged in to view your loan status.");
}

$member_id = intval($_SESSION['member_id']); // Secure cast

// Fetch loan applications with extended fields
$stmt = $conn->prepare("SELECT 
        facility_type, loan_category, loan_amount, loan_status, created_at,
        repayment_source, bank_name, account_number, account_name, bvn
    FROM loans 
    WHERE member_id = ? 
    ORDER BY created_at DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching loans: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Loan Status</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: #f0f2f5;
      margin: 0; padding: 0 20px;
    }
    h2 { text-align: center; margin: 30px 0 10px; color: #333; }
    .navbar {
      display: flex; justify-content: space-between; align-items: center;
      background-color: #004080; padding: 10px 25px; flex-wrap: wrap;
    }
    .navbar a {
      font-size: 16px; color: white; padding: 20px 18px;
      text-decoration: none; transition: background 0.3s ease;
    }
    .navbar a:hover:not(:first-child) {
      background-color: #007bff; border-radius: 5px;
    }
    table {
      width: 100%; margin: 30px auto; border-collapse: collapse;
      background: #fff; border-radius: 10px; overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    th, td { padding: 14px 20px; text-align: left; }
    th { background-color: #004080; color: white; font-weight: 600; }
    tr:nth-child(even) { background-color: #f8f9fa; }
    tr:hover { background-color: #e2e6ea; }
    td { color: #333; }
    footer {
      text-align: center; padding: 30px;
      background-color: #004080; color: white; margin-top: 330px;
    }
    @media (max-width: 768px) {
      .navbar { flex-direction: column; align-items: flex-start; }
      .navbar a { padding: 10px 15px; font-size: 14px; width: 100%; }
      table th, table td { font-size: 14px; padding: 10px; }
      h2 { font-size: 1.2rem; }
      footer { margin-top: 150px; }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="left">
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80"
             style="padding: 0; margin: -30px -30px -10px -30px;">
      </a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="loan_saver_borrower.php">Loan Application Form</a>
      <a href="loan_status.php">Status Tracking</a>
      <a href="upload_loan_repayment.php">Loan Repayment</a>
      <a href="view_loan_repayment.php">Loan Repayment Status</a>
      <a href="loan_report.php">Loan Report</a>
      <a href="loan_calculator.html">Loan Calculator</a>
    </div>
  </div>

  <h2>Your Loan Applications</h2>

  <table>
    <tr>
      <th>Facility Type</th>
      <th>Category</th>
      <th>Amount</th>
      <th>Status</th>
      <th>Submitted</th>
      <th>Repayment Source</th>
      <th>Bank Name</th>
      <th>Account Number</th>
      <th>Account Name</th>
      <th>BVN</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['facility_type']) ?></td>
        <td><?= htmlspecialchars($row['loan_category']) ?></td>
        <td>₦<?= number_format($row['loan_amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['loan_status']) ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><?= htmlspecialchars($row['repayment_source']) ?></td>
        <td><?= htmlspecialchars($row['bank_name']) ?></td>
        <td><?= htmlspecialchars($row['account_number']) ?></td>
        <td><?= htmlspecialchars($row['account_name']) ?></td>
        <td><?= htmlspecialchars($row['bvn']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
