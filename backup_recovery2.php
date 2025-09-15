<?php
session_start();

// Restrict to admin1, admin2, or auditor roles


// Secure DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

// Fetch logs (limit to 20)
$logs = [];
$stmt = $conn->prepare("SELECT l.*, a.email AS full_name, a.role 
                        FROM logs l 
                        JOIN admin_panel a ON l.member_id = a.staff_id 
                        ORDER BY l.created_at DESC 
                        LIMIT 20");
if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    $logs = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Loan summary
$loanSummary = ['total_loans' => 0, 'total_loan_amount' => 0];
$loanResult = $conn->query("SELECT COUNT(*) AS total_loans, SUM(loan_amount) AS total_loan_amount FROM loans");
if ($loanResult) {
    $loanSummary = $loanResult->fetch_assoc();
    $loanResult->free();
}

// Savings summary
$savingsSummary = ['total_savings' => 0, 'total_savings_amount' => 0];
$savingsResult = $conn->query("SELECT COUNT(*) AS total_savings, SUM(amount) AS total_savings_amount FROM savings");
if ($savingsResult) {
    $savingsSummary = $savingsResult->fetch_assoc();
    $savingsResult->free();
}

// Role-based activities
$roleActivities = [];
$activityResult = $conn->query("SELECT a.role, COUNT(*) AS actions 
                                FROM logs l 
                                JOIN admin_panel a ON l.member_id = a.staff_id 
                                GROUP BY a.role");
if ($activityResult) {
    $roleActivities = $activityResult->fetch_all(MYSQLI_ASSOC);
    $activityResult->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Backup & Recovery – Admin</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    .login-box {
      background: #fff;
      max-width: 900px;
      margin: auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .section {
      background: #fafafa;
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px 14px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #f0f0f0;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 30px;
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
      background-color: #0056b3;
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      body {
        padding: 10px;
      }

      .login-box {
        padding: 20px;
      }

      table, th, td {
        font-size: 14px;
      }

      .logout-btn {
        font-size: 14px;
        padding: 10px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Backup & Recovery – Admin</h2>

    <div class="section">
      <h3>Financial Summary</h3>
      <p><strong>Total Loans:</strong> <?= (int)$loanSummary['total_loans'] ?> | 
         <strong>Amount:</strong> ₦<?= number_format((float)$loanSummary['total_loan_amount'], 2) ?></p>
      <p><strong>Total Savings:</strong> <?= (int)$savingsSummary['total_savings'] ?> | 
         <strong>Amount:</strong> ₦<?= number_format((float)$savingsSummary['total_savings_amount'], 2) ?></p>
    </div>

    <div class="section">
      <h3>Recent System Logs</h3>
      <table>
        <tr><th>User</th><th>Role</th><th>Action</th><th>Date</th></tr>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td><?= htmlspecialchars($log['full_name']) ?></td>
              <td><?= htmlspecialchars($log['role']) ?></td>
              <td><?= htmlspecialchars($log['action']) ?></td>
              <td><?= htmlspecialchars($log['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4">No logs found.</td></tr>
        <?php endif; ?>
      </table>
    </div>

    <div class="section">
      <h3>Actions Per Role</h3>
      <table>
        <tr><th>Role</th><th>Actions</th></tr>
        <?php if (!empty($roleActivities)): ?>
          <?php foreach ($roleActivities as $activity): ?>
            <tr>
              <td><?= htmlspecialchars($activity['role']) ?></td>
              <td><?= htmlspecialchars($activity['actions']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="2">No activity records found.</td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
  </div>
</body>
</html>

<?php $conn->close(); ?>
