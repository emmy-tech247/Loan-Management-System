<?php
session_start();

// DB connection
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>📊 Admin Reports</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      background: #f0f2f5;
    }

    .report-box {
      max-width: 1000px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    h2, h3 {
      text-align: center;
      color: #333;
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
      font-size: 15px;
    }

    th {
      background: #f4f4f4;
      font-weight: 600;
    }

    p {
      font-size: 15px;
      color: #444;
      margin: 6px 0;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 40px;
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

    .logout-btn:active {
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      .report-box {
        padding: 20px;
      }

      table, th, td {
        font-size: 14px;
      }

      .logout-btn {
        width: 100%;
        text-align: center;
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
      <p><strong>Total Loans:</strong> <?= htmlspecialchars($loanStats['total_loans'] ?? 0) ?></p>
      <p><strong>Total Loan Amount:</strong> ₦<?= number_format($loanStats['total_amount'] ?? 0, 2) ?></p>
    </div>

    <!-- 💰 Savings Summary -->
    <div class="section">
      <h3>💰 Savings Summary</h3>
      <p><strong>Total Savers:</strong> <?= htmlspecialchars($savingStats['total_savers'] ?? 0) ?></p>
      <p><strong>Total Saved Amount:</strong> ₦<?= number_format($savingStats['total_saved'] ?? 0, 2) ?></p>
    </div>

    <!-- 🏦 Fixed Deposit Summary -->
    <?php if (!empty($fdStats)): ?>
    <div class="section">
      <h3>🏦 Fixed Deposit Summary</h3>
      <p><strong>Total FDs:</strong> <?= htmlspecialchars($fdStats['total_fds'] ?? 0) ?></p>
      <p><strong>Total FD Amount:</strong> ₦<?= number_format($fdStats['total_fd_amount'] ?? 0, 2) ?></p>
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
            <td>₦<?= number_format($pay['amount_paid'], 2) ?></td>
            <td><?= date('d M Y H:i', strtotime($pay['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <!-- Back Button -->
    <div class="center-container">
      <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
