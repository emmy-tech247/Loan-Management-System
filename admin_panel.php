<?php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['role'])) {
    header("Location: admin_panel_login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_panel_login.php");
    exit();
}


$role = $_SESSION['role'];
$full_name = $_SESSION['full_name'] ?? ucfirst($role); // Use stored name or fallback to role

// Define role-based dashboard content
$dashboardActions = [
    'relationship_officer' => [
        'title' => 'Relationship Officer Dashboard',
        'actions' => [
            'Review Loan Applications' => 'review_loans.php',
            'Assist Members' => 'assist_members.php',
            'Forward Cases to Admin' => 'forward_to_admin.php',
        ]
    ],
    'auditor' => [
        'title' => 'Auditor Dashboard',
        'actions' => [
            'View Pending Loans' => 'approve_loan.php?stage=auditor',
            'Send to Manager' => 'forward_to_admin.php?to=manager',
        ]
    ],
    'manager' => [
        'title' => 'Manager Dashboard',
        'actions' => [
            'Final Loan Approval' => 'approve_loan.php?stage=manager',
            'Review Reports' => 'view_reports.php',
        ]
    ],
    'accountant' => [
        'title' => 'Accountant Dashboard',
        'actions' => [
            'Review Approved Loans' => 'approve_loan.php?stage=accountant',
            'Forward to Manager' => 'forward_to_admin.php?to=manager',
        ]
    ],
   'admin' => [
        'title' => 'Admin Dashboard',
        'actions' => [
            'Manage Members' => 'manage_members.php',
            'Approve Deposits' => 'approve_deposits.php',
            'System Backup/Recovery' => 'backup_recovery.php',
        ]
    ],
    'md' => [
        'title' => 'Managing Director Dashboard',
        'actions' => [
            'Manage Members' => 'manage_members.php',
            'Approve Deposits' => 'approve_deposits.php',
            'System Backup/Recovery' => 'backup_recovery.php',
        ]
    ],

];

    

// Prevent unknown roles from proceeding
if (!array_key_exists($role, $dashboardActions)) {
    die("Unauthorized role access.");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($dashboardActions[$role]['title']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px;
    }
    .dashboard-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 90%;
      max-width: 500px;
    }
    h2 {
      margin-bottom: 20px;
      color: #2c3e50;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      background: #007bff;
      margin: 10px 0;
      padding: 12px 18px;
      border-radius: 8px;
      text-align: center;
    }
    li a {
      color: white;
      text-decoration: none;
      display: block;
    }
    .logout-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 16px;
      background: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administrators Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f4f6f9;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .card {
      background-color: #fff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      text-align: center;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card i {
      font-size: 2.5rem;
      color: #007bff;
      margin-bottom: 15px;
    }
    .card h3 {
      font-size: 1.2rem;
      color: #444;
      margin-bottom: 10px;
    }
    .card p {
      font-size: 0.95rem;
      color: #777;
      margin-bottom: 15px;
    }
    .card a {
      display: inline-block;
      padding: 8px 16px;
      background-color: #007bff;
      color: #fff;
      border-radius: 5px;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .card a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <h1>Staff Dashboard</h1>
  <div class="dashboard">

    <div class="card">
      <i class="fas fa-user-tie"></i>
      <h3>Relationship Officer</h3>
      <p>Review and forward loan applications.</p>
      <a href="relationship_officer.php">Go to Dashboard</a>
    </div>

    <div class="card">
      <i class="fas fa-users"></i>
      <h3>Auditor</h3>
      <p>Assist members and process transactions.</p>
      <a href="auditor.php">Go to Dashboard</a>
    </div>

    <div class="card">
      <i class="fas fa-user-check"></i>
      <h3>Manager</h3>
      <p>Approve high-level transactions and monitor workflow.</p>
      <a href="manager.php">Go to Dashboard</a>
    </div>

    <div class="card">
      <i class="fas fa-user-cog"></i>
      <h3>Accountant</h3>
      <p>Verify payments, disburse funds, and manage ledgers.</p>
      <a href="accountant.php">Go to Dashboard</a>
    </div>


    <div class="card">
      <i class="fas fa-user-gear"></i>
      <h3>Admin</h3>
      <p>Manage users, configure system settings, and backups.</p>
      <a href="admin1.php">Go to Dashboard</a>
    </div>

    <div class="card">
      <i class="fas fa-user-gear"></i>
      <h3>Managing Director</h3>
      <p>Control and Oversee every activity and transaction.</p>
      <a href="admin2.php">Go to Dashboard</a>
    </div>

    

  </div>
  
</body>
</html>