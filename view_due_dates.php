<?php
session_start();
include('db.php');

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Fetch Loan Due Dates
$loan_query = $conn->prepare("SELECT id, amount, due_date, status FROM loans WHERE member_id = ? AND status = 'active'");
$loan_query->bind_param("i", $member_id);
$loan_query->execute();
$loan_result = $loan_query->get_result();

// Fetch Fixed Deposit Maturity Dates
$fd_query = $conn->prepare("SELECT id, amount, maturity_date, status FROM fixed_deposits WHERE member_id = ? AND status = 'active'");
$fd_query->bind_param("i", $member_id);
$fd_query->execute();
$fd_result = $fd_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Due Dates</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 20px;
    }
    .due-section {
      background: #fff;
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    h2 {
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }
    th {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body>

<div class="due-section">
  <h2>📅 Loan Due Dates</h2>
  <table>
    <tr>
      <th>Loan ID</th>
      <th>Amount</th>
      <th>Due Date</th>
      <th>Status</th>
    </tr>
    <?php while ($loan = $loan_result->fetch_assoc()): ?>
    <tr>
      <td><?= $loan['id'] ?></td>
      <td>₦<?= number_format($loan['amount'], 2) ?></td>
      <td><?= date('F j, Y', strtotime($loan['due_date'])) ?></td>
      <td><?= ucfirst($loan['status']) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="due-section">
  <h2>📆 Fixed Deposit Maturity Dates</h2>
  <table>
    <tr>
      <th>FD ID</th>
      <th>Amount</th>
      <th>Maturity Date</th>
      <th>Status</th>
    </tr>
    <?php while ($fd = $fd_result->fetch_assoc()): ?>
    <tr>
      <td><?= $fd['id'] ?></td>
      <td>₦<?= number_format($fd['amount'], 2) ?></td>
      <td><?= date('F j, Y', strtotime($fd['maturity_date'])) ?></td>
      <td><?= ucfirst($fd['status']) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
