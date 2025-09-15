<?php
session_start();
include("db.php"); // assumes your db connection is here

$member_id = $_SESSION['member_id'] ?? 1; // logged-in member ID


// Account Overview
$loan_result = mysqli_query($conn, "SELECT SUM(balance) AS loan_balance FROM loans WHERE member_id='$member_id' AND status='approved'");
$loan_balance = mysqli_fetch_assoc($loan_result)['loan_balance'] ?? 0;

$savings_result = mysqli_query($conn, "SELECT SUM(amount) AS savings_balance FROM savings WHERE member_id='$member_id'");
$savings_balance = mysqli_fetch_assoc($savings_result)['savings_balance'] ?? 0;

$fd_result = mysqli_query($conn, "SELECT SUM(amount) AS fd_total FROM fixed_deposits WHERE member_id='$member_id'");
$fd_total = mysqli_fetch_assoc($fd_result)['fd_total'] ?? 0;

// Transaction History
$transactions = mysqli_query($conn, "SELECT * FROM transactions WHERE member_id='$member_id' ORDER BY date DESC LIMIT 10");

// Notifications
$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE member_id='$member_id' OR member_id IS NULL ORDER BY date DESC LIMIT 5");
?>

<h1 class="page-header">Member Dashboard</h1>

<!-- Account Overview -->
<div class="row placeholders">
  <div class="col-xs-6 col-sm-4 placeholder">
    <img src="../images/loanficn.png" width="100" height="100" class="img-responsive" alt="Loan">
    <h4>Loan Balance</h4>
    <span class="text-muted">₦<?php echo number_format($loan_balance, 2); ?></span>
  </div>
  <div class="col-xs-6 col-sm-4 placeholder">
    <img src="../images/savingsficn.png" width="100" height="100" class="img-responsive" alt="Savings">
    <h4>Savings Balance</h4>
    <span class="text-muted">₦<?php echo number_format($savings_balance, 2); ?></span>
  </div>
  <div class="col-xs-6 col-sm-4 placeholder">
    <img src="../images/fixeddeposit.png" width="100" height="100" class="img-responsive" alt="Fixed Deposit">
    <h4>Fixed Deposit</h4>
    <span class="text-muted">₦<?php echo number_format($fd_total, 2); ?></span>
  </div>
</div>

<hr>

<!-- Transaction History -->
<h3>Transaction History</h3>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Date</th>
      <th>Type</th>
      <th>Amount</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($transactions)): ?>
      <tr>
        <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
        <td><?php echo ucfirst($row['type']); ?></td>
        <td>₦<?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo $row['description']; ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<hr>

<!-- Notifications -->
<h3>Notifications</h3>
<ul class="list-group">
  <?php while ($note = mysqli_fetch_assoc($notifications)): ?>
    <li class="list-group-item">
      <strong><?php echo $note['title']; ?>:</strong> <?php echo $note['message']; ?>
      <br><small class="text-muted"><?php echo date('d M Y', strtotime($note['date'])); ?></small>
    </li>
  <?php endwhile; ?>
</ul>
