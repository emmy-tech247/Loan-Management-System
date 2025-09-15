<?php
session_start();
require_once('db.php');

// ✅ Security: Ensure Managing Director is authenticated
if (!isset($_SESSION['admin2Id'])) {
    header("Location: staff.php"); // redirect back to login
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: staff.php"); // redirect back to login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Managing Director Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #eef1f7;
      margin: 0;
      padding: 40px 20px;
    }

    .dashboard-container {
      background-color: #ffffff;
      border-radius: 12px;
      max-width: 1000px;
      margin: auto;
      padding: 40px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      color: #2c3e50;
      margin-bottom: 5px;
      font-size: 28px;
    }

    p {
      color: #6c757d;
      margin-bottom: 30px;
      font-size: 16px;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 15px;
    }

    td {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    td:hover {
      background-color: #e9ecef;
      transform: translateY(-3px);
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    a {
      text-decoration: none;
      color: #007bff;
      font-weight: 600;
      font-size: 16px;
      display: block;
    }

    a:hover {
      color: #0056b3;
      text-decoration: underline;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      table, tr, td {
        display: block;
        width: 100%;
      }
      td {
        margin-bottom: 15px;
      }
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

    .logout-btn:active {
      background-color: #0056b3;
      transform: translateY(0);
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
    <p>Select an action below:</p>

    <table>
      <tr>
        <td><a href="manage_members2.php">👥 Manage Members</a></td>
        <td><a href="admin_add_staff.php">📁 Manage Staff</a></td>
        <td><a href="confirm_deposits.php">✅ Approve Deposits</a></td>
      </tr>
      <tr>
        <td><a href="admin_announcement2.php">📢 Create Announcement</a></td>
        <td><a href="backup_recovery2.php">🗄️ Backup & Recovery</a></td>
        <td><a href="confirm_fd.php">📥 Confirm Fixed Deposits</a></td>
      </tr>
      <tr>
        <td><a href="admin2_confirm_topups.php">📝 Confirm Top-up Approvals</a></td>
        <td><a href="admin2_document.php">📁 View & Confirm Documents</a></td>
        <td><a href="admin2_approve_withdrawals.php">📁 Approve Withdrawals</a></td>
      </tr>
      <tr>
        <td><a href="admin2_confirm_repayment.php">📁 Confirm Repayment</a></td>
        <td><a href="view_signatures2.php">✍️ View & Confirm Signatures</a></td>
        <td><a href="admin_chat_list2.php">💬 Chat with Member</a></td>
      </tr>
      <tr>
        <td><a href="admin2_view_fd_withdrawal.php">📁 Confirm FD Withdrawal/Rollover</a></td>
        <td><a href="display_all_log.php">📁 Display All Logs</a></td>
        <td><a href="admin2_loan_record.php">📁 All Loan Records</a></td>
      </tr>
      <tr>
        <td><a href="admin1_activity_logs.php">📁 Admin Activities</a></td>
        <td><a href="admin_transactions.php">📁 All Financial Transactions</a></td>
        <td><a href="admin2_view_login_attempts.php">📁 Forgot Password & Login Attempts</a></td>
      </tr>
      <tr>
        <td><a href="loan_default_saver.php">📁 All Loan Report And Repayment</a></td>
        <td><a href="view_reports2.php">📊 View Reports</a></td>
      </tr>
    </table>

    <br><br><br>
    <div class="center-container">
      <a class="logout-btn" href="staff.php">Logout</a>
    </div>
  </div>
</body>
</html>
