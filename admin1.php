<?php
session_start();
include('db.php');


if (!isset($_SESSION['admin1Id'])) {
    header("Location: admin1.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin1.php");
    exit();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        padding: 30px;
    }
    .dashboard-container {
        background: #fff;
        border-radius: 8px;
        max-width: 900px;
        margin: 0 auto;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        color: #333;
        margin-bottom: 10px;
    }
    p {
        margin-bottom: 20px;
        color: #555;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }
    a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
    .logout-btn {
        display: inline-block;
        background-color: #dc3545;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
    }
    .logout-btn:hover {
        background-color: #c82333;
    }

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

  /* Responsive for small devices */
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
        <td><a href="manage_members1.php">👥 Manage Members</a></td>
        <td><a href="acknowledge_deposits.php">✅ Acknowledge Deposits</a></td>
        <td><a href="view_reports1.php">📊 View Reports</a></td>
      </tr>
      <tr>
        <td><a href="admin_announcement1.php">📢 Create Announcement</a></td>
        <td><a href="backup_recovery1.php">🗄️ Backup & Recovery</a></td>
        <td><a href="acknowledge_fd.php">📥 Acknowledge Fixed Deposits</a></td>
      </tr>
      <tr>
        <td><a href="admin1_acknowledge_topups.php">📝 Acknowledge Top-up Requests</a></td>
        <td><a href="admin1_document.php">📁 View & Acknowledge Documents</a></td>
        <td><a href="admin1_acknowledge_withdrawals.php">📁 Acknowledge Withdrawal</a></td>
      </tr>
      <tr>
        <td><a href="admin1_acknowledge_repayment.php">📁 Acknowledge Repayment</a></td>
        <td><a href="view_signatures1.php">✍️ View & Acknowledge Signatures</a></td>
        <td><a href="admin_chat_list1.php">💬 Chat with Member</a></td>
      <tr>
        <td><a href="admin1_view_fd_withdrawal.php">📁 Acknowledge Fixed Deposit Withdrawal/Rollover</a></td>
        <td><a href="admin_transactions.php">📁 View All Financial Transactions</a></td>
        <td><a href="loan_default_saver.php">📁 View All Loan Report And Repayment</a></td>
      </tr>
    </table>

  <br><br><br>
   <div class="center-container">
      <a class="logout-btn" href="staff.php">Logout</a>
    </div>


  </div>
</body>
</html>
