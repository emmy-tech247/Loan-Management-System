<?php
session_start();


// DB connection (secure & optimized)
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'loan_system';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Member Summary ---
$membersSummary = [];
$memberQuery = $conn->query("SELECT status, COUNT(*) AS count FROM members GROUP BY status");
if ($memberQuery) {
    $membersSummary = $memberQuery->fetch_all(MYSQLI_ASSOC);
}

// --- Loan Summary ---
$loanStats = $conn->query("SELECT COUNT(*) AS total_loans, SUM(loan_amount) AS total_amount FROM loans")->fetch_assoc();

// --- Savings Summary ---
$savingStats = $conn->query("SELECT COUNT(DISTINCT member_id) AS total_savers, SUM(amount_saved) AS total_saved FROM savings_transactions")->fetch_assoc();

// --- Fixed Deposit Summary ---
$fdStats = [];
if ($conn->query("SHOW TABLES LIKE 'fixed_deposits'")->num_rows > 0) {
    $fdStats = $conn->query("SELECT COUNT(*) AS total_fds, SUM(amount_deposited) AS total_fd_amount FROM fixed_deposits")->fetch_assoc();
}

// --- Recent Payments ---
$payments = [];
$paymentQuery = $conn->query("
    SELECT p.id, p.amount_paid, p.created_at, CONCAT(m.first_name, ' ', m.surname) AS full_name 
    FROM payment_transactions p
    JOIN members m ON p.member_id = m.member_id 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
if ($paymentQuery) {
    $payments = $paymentQuery->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📊 Admin Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      padding: 20px;
      margin: 0;
    }
    .report-box {
      max-width: 1000px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .section {
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
      font-size: 14px;
    }
    th {
      background: #f4f4f4;
    }
    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .logout-btn {
      display: inline-block;
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
    @media (max-width: 768px) {
      body {
        padding: 10px;
      }
      .report-box {
        padding: 20px;
      }
      th, td {
        font-size: 13px;
      }
    }
    @media (max-width: 480px) {
      th, td {
        font-size: 12px;
        padding: 8px;
      }
      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="report-box">
    <h2>📊 Admin Reports Overview</h2>

    <!-- 👥 Member Summary -->
    <div class="section">
      <h3>👥 Member Status Summary</h3>
      <table>
        <tr><th>Status</th><th>Count</th></tr>
        <?php foreach ($membersSummary as $row): ?>
          <tr>
            <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
            <td><?= htmlspecialchars($row['count']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <!-- 💳 Loan Summary -->
    <div class="section">
      <h3>💳 Loan Summary</h3>
      <p><strong>Total Loans:</strong> <?= (int)$loanStats['total_loans'] ?></p>
      <p><strong>Total Loan Amount:</strong> ₦<?= number_format((float)$loanStats['total_amount'], 2) ?></p>
    </div>

    <!-- 💰 Savings Summary -->
    <div class="section">
      <h3>💰 Savings Summary</h3>
      <p><strong>Total Savers:</strong> <?= (int)$savingStats['total_savers'] ?></p>
      <p><strong>Total Saved Amount:</strong> ₦<?= number_format((float)$savingStats['total_saved'], 2) ?></p>
    </div>

    <!-- 🏦 Fixed Deposit Summary -->
    <?php if (!empty($fdStats)): ?>
    <div class="section">
      <h3>🏦 Fixed Deposit Summary</h3>
      <p><strong>Total FDs:</strong> <?= (int)$fdStats['total_fds'] ?></p>
      <p><strong>Total FD Amount:</strong> ₦<?= number_format((float)$fdStats['total_fd_amount'], 2) ?></p>
    </div>
    <?php endif; ?>

    <!-- 🧾 Recent Payments -->
    <div class="section">
      <h3>🧾 Recent Payments</h3>
      <table>
        <tr><th>Member</th><th>Amount Paid</th><th>Date</th></tr>
        <?php foreach ($payments as $pay): ?>
          <tr>
            <td><?= htmlspecialchars($pay['full_name']) ?></td>
            <td>₦<?= number_format((float)$pay['amount_paid'], 2) ?></td>
            <td><?= date('d M Y H:i', strtotime($pay['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="center-container">
      <a class="logout-btn" href="admin1.php">⬅ Back to Admin Dashboard</a>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
