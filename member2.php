<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header("Location:member.php");
    exit;
}

$host = 'localhost';
$user = 'root';
$password_db = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$member_id = $_SESSION['member_id'];

$memberQuery = $conn->prepare("SELECT username, passport FROM members WHERE id = ?");
$memberQuery->bind_param("i", $member_id);
$memberQuery->execute();
$memberResult = $memberQuery->get_result();
$member = $memberResult->fetch_assoc();

function fetchSingleValue($conn, $query, $param) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $param);
    $stmt->execute();
    $stmt->bind_result($value);
    $stmt->fetch();
    $stmt->close();
    return $value ?? 0;
}

$loanBalance = fetchSingleValue($conn, "SELECT SUM(amount) FROM loans WHERE member_id = ?", $member_id);
$savingsBalance = fetchSingleValue($conn, "SELECT SUM(amount_saved) FROM savings_transactions WHERE member_id = ?", $member_id);
$monthlySavings = fetchSingleValue($conn, "SELECT auto_amount FROM monthly_savings WHERE member_id = ? LIMIT 1", $member_id);
echo "<pre>Monthly Savings (Raw): ₦" . $monthlySavings . "</pre>";


$loanRepayment = fetchSingleValue($conn, "SELECT SUM(amount_paid) FROM repayments INNER JOIN loans ON repayments.loan_id = loans.id WHERE loans.member_id = ?", $member_id);
$fdSummary = fetchSingleValue($conn, "SELECT SUM(amount_deposited) FROM fixed_deposits WHERE member_id = ?", $member_id);

// Fixed Deposit Records
$fdRecordsStmt = $conn->prepare("SELECT amount_deposited AS amount, tenure_months, interest_rate FROM fixed_deposits WHERE member_id = ?");
$fdRecordsStmt->bind_param("i", $member_id);
$fdRecordsStmt->execute();
$fdRecords = $fdRecordsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Due Dates
$dueDatesStmt = $conn->prepare("
    SELECT due_dates.due_date, due_dates.amount_due, due_dates.status 
    FROM due_dates 
    JOIN loans ON due_dates.loan_id = loans.id 
    WHERE loans.member_id = ?
");

$dueDatesStmt->bind_param("i", $member_id);
$dueDatesStmt->execute();
$dueDates = $dueDatesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Approvals
$approvalsStmt = $conn->query("SELECT item_type AS type, status AS description FROM approvals JOIN admin_panel ON approvals.approver_id = admin_panel.id");
$approvals = $approvalsStmt->fetch_all(MYSQLI_ASSOC);

// Announcements
$announcementsStmt = $conn->query("SELECT title, content AS message FROM announcements ORDER BY created_at DESC LIMIT 5");
$announcements = $announcementsStmt->fetch_all(MYSQLI_ASSOC);
?>



<!-- HTML continues as in original, using PHP short tags and updated mysqli-fetched variables -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Member Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      background: #f0f2f5;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    .profile {
      text-align: center;
      margin-bottom: 30px;
    }
    .profile img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #007bff;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 20px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .icon-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }
    .icon-title i {
      font-size: 1.8rem;
      color: #007bff;
    }
    .icon-title h3 {
      font-size: 1.1rem;
      color: #333;
    }
    .value {
      font-size: 1.4rem;
      font-weight: bold;
      color: #2c3e50;
    }
    .card ul {
      padding-left: 20px;
      list-style-type: disc;
    }
    .card li {
      font-size: 0.95rem;
      margin-bottom: 5px;
    }
  </style>
</head>


<body>
  <?php if (isset($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'success'): ?>
  <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;">
    ✅ <?= ucwords(str_replace('_', ' ', $_SESSION['payment_type'])) ?> of ₦<?= number_format($_SESSION['payment_amount'], 2) ?> was successful!
  </div>
  <?php unset($_SESSION['payment_status'], $_SESSION['payment_type'], $_SESSION['payment_amount']); ?>
<?php endif; ?>



    <style>
    /* Basic navbar styling */
    body { font-family: Arial; padding: 0px; margin:5px; background: #f8f8f8; }
    form, table { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 10px; }
    input, select, textarea { display: block; width: 100%; margin: 10px 0; padding: 10px; }



    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color:#004080;
      font-family: Arial, sans-serif;
      padding: 0 20px;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar a,
    .dropbtn {
      font-size: 16px;
      color: white;
      text-align: center;
      padding: 14px 20px;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
    }

    
    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: #f9f9f9;
      min-width: 160px;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 6px;
      text-decoration: none;
      display: block;
      text-align: left;
    }

    .dropdown-content a:hover {
      background-color: #ddd;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    
footer {
  text-align: center;
  padding: 30px;
  background-color: #004080;
  color: white;
  margin-top: 20px;
}
  </style>
  <div class="navbar">
    <!-- Left section -->
    <div class="left">
      <a href="home.php">LOGO</a>
    </div>

    <!-- Right section -->
    <div class="right">
      
      <a href="loan_management.html">Loan <br> Management</a>
      <a href="monthly_savings.html">Monthly Savings<br>System</a>
      <a href="fixed_deposit.php">Fixed Deposit <br> Module</a>
      <a href="payment.html">Payment<br>Integration</a>
      <a href="communication.php">Communication<br>And Support</a>
      <a href="document.html">Document & <br> E-Signature</a>
      
    </div>
  </div>
  <h1>Welcome, <?= htmlspecialchars($member['username']) ?>!</h1>
<div class="profile">
  <img src="uploads/<?= htmlspecialchars($member['passport']) ?>" alt="User Photo">
</div>

  <div class="dashboard">
    <div class="card">
      <div class="icon-title"><i class="fas fa-hand-holding-usd"></i><h3>Loan Balance</h3></div>
      <div class="value">₦<?= number_format($loanBalance, 2) ?></div>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-piggy-bank"></i><h3>Savings Balance</h3></div>
      <div class="value">₦<?= number_format($savingsBalance, 2) ?></div>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-lock"></i><h3>Fixed Deposit Summary</h3></div>
      <div class="value">₦<?= number_format($fdSummary, 2) ?></div>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-calendar-alt"></i><h3>Monthly Savings</h3></div>
      <div class="value">₦<?= number_format($monthlySavings, 2) ?> / month</div>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-money-check-alt"></i><h3>Loan Repayment</h3></div>
      <div class="value">₦<?= number_format($loanRepayment, 2) ?> Paid</div>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-file-invoice-dollar"></i><h3>Fixed Deposit Records</h3></div>
      <ul>
        <?php foreach ($fdRecords as $fd): ?>
          <li>₦<?= number_format($fd['amount']) ?> - <?= $fd['tenure_months'] ?> Months @ <?= $fd['interest_rate'] ?>%</li>
        <?php endforeach; ?>
      </ul>
    </div>
    

    <div class="card">
      <div class="icon-title"><i class="fas fa-calendar-check"></i><h3>Due Dates</h3></div>
      <ul>
        <?php foreach ($dueDates as $due): ?>
          <li><?= htmlspecialchars($due['type']) ?>: <?= htmlspecialchars($due['due_date']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-user-check"></i><h3>Pending Approvals</h3></div>
      <ul>
        <?php foreach ($approvals as $app): ?>
          <li><?= htmlspecialchars($app['type']) ?> - <?= htmlspecialchars($app['description']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="card">
      <div class="icon-title"><i class="fas fa-bullhorn"></i><h3>Announcements</h3></div>
      <ul>
        <?php foreach ($announcements as $note): ?>
          <li><?= htmlspecialchars($note['message']) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
</footer>




</body>
</html>
