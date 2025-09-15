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

$memberQuery = $conn->prepare("SELECT full_name, passport FROM members WHERE member_id = ?");
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

$loanBalance = fetchSingleValue(
    $conn,
    "SELECT SUM(loan_amount) FROM loans WHERE member_id = ? AND loan_status IN ('checked', 'disbursed')",
    $member_id
);


$savingsBalance = fetchSingleValue(
    $conn,
    "SELECT 
        COALESCE(SUM(CASE WHEN type = 'deposit' AND status = 'approved' THEN amount_saved ELSE 0 END), 0) -
        COALESCE(SUM(CASE WHEN type = 'withdrawal' AND status = 'approved' THEN amount_saved ELSE 0 END), 0) 
     AS net_balance
     FROM savings_transactions 
     WHERE member_id = ?",
    $member_id
);

$monthlySavings = fetchSingleValue($conn, "SELECT auto_amount FROM monthly_savings WHERE member_id = ? LIMIT 1", $member_id);


$loanRepayment = fetchSingleValue($conn, "SELECT SUM(loan_repayments.amount_paid) 
FROM loans
JOIN loan_repayments ON loans.loan_id = loan_repayments.loan_id WHERE loans.member_id = ?", $member_id);
$fdSummary = fetchSingleValue($conn,"SELECT SUM(amount_deposited) FROM fixed_deposits WHERE member_id = ? AND status = 'confirmed'", $member_id);



// Fixed Deposit Records

$fdRecordsStmt = $conn->prepare("
    SELECT id, amount_deposited AS amount, tenure_months, interest_rate, status, maturity_date
    FROM fixed_deposits 
    WHERE member_id = ? AND status = 'confirmed'
");
$fdRecordsStmt->bind_param("i", $member_id);
$fdRecordsStmt->execute();
$fdRecords = $fdRecordsStmt->get_result()->fetch_all(MYSQLI_ASSOC);


// Due Dates
$dueDatesStmt = $conn->prepare("
    SELECT due_dates.due_date, due_dates.amount_due, due_dates.status 
    FROM due_dates 
    JOIN loans ON due_dates.loan_id = loans.loan_id 
    WHERE loans.member_id = ?
");

$dueDatesStmt->bind_param("i", $member_id);
$dueDatesStmt->execute();
$dueDates = $dueDatesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Approvals
$approvals = [];

// Loan approvals
$loanStmt = $conn->prepare("
    SELECT a.item_type, a.status 
    FROM approvals a 
    JOIN loans l ON a.item_id = l.loan_id 
    WHERE a.item_type = 'loan' AND l.member_id = ?
");
$loanStmt->bind_param("i", $member_id);
$loanStmt->execute();
$loanApprovals = $loanStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$loanStmt->close();

// Total loan repaid by this member
$repayTotal = 0;
$repayStmt = $conn->prepare("SELECT SUM(amount_paid) 
                             AS total_repaid 
                             FROM loan_repayments 
                             WHERE member_id = ? AND status = 'approved'");
$repayStmt->bind_param("i", $member_id);
$repayStmt->execute();
$repayStmt->bind_result($repayTotal);
$repayStmt->fetch();
$repayStmt->close();


// Fixed deposit approvals
$fdStmt = $conn->prepare("
    SELECT a.item_type, a.status 
    FROM approvals a 
    JOIN fixed_deposits f ON a.item_id = f.id 
    WHERE a.item_type = 'fixed_deposit' AND f.member_id = ?
");
$fdStmt->bind_param("i", $member_id);
$fdStmt->execute();
$fdApprovals = $fdStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$fdStmt->close();

// Merge
$approvals = array_merge($loanApprovals, $fdApprovals);

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
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }

  
    .navbar img {
      display: block;
      margin: -50px -50px -20px -50px;
      padding: 0;
    }


    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 24px 25px;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
    }

    /* Remove hover from logo */
    .navbar .left a {
      background-color: transparent;
    }

    /* Hover effect only for nav links (not logo) */
    .navbar .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }



    
footer {
  text-align: center;
  padding: 30px;
  background-color: #004080;
  color: white;
  margin-top: 20px;

    <!-- status -->
    .status {
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 5px;
  display: inline-block;
  font-size: 0.85rem;
}

.status.pending {
  background-color: #fff3cd;
  color: #856404;
}

.status.reviewed {
  background-color: #d1ecf1;
  color: #0c5460;
}

.status.checked {
  background-color: #cce5ff;
  color: #004085;
}

.status.approved {
  background-color: #d4edda;
  color: #155724;
}

.status.rejected {
  background-color: #f8d7da;
  color: #721c24;
}

}

/* Ensure logo is clickable and doesn't respond to hover */
.logo-link {
  cursor: default;           /* Static cursor */
  display: inline-block;
}


  </style>
  <div class="navbar">
    <!-- Left section -->
    <div class="left">
      <a href="home.php" class="logo-link"><img src="images/logo2.png" alt="Logo" width="80" height="80"></a>
    </div>

    <!-- Right section -->
    <div class="right">
      
      <a href="loan_management.html">Loan <br> Management</a>
      <a href="monthly_savings.html">Monthly Savings<br>System</a>
      <a href="fixed_deposit.php">Fixed Deposit <br> Module</a>
      <a href="payment.php">Payment<br>Integration</a>
      <a href="communication.php">Communication<br>And Support</a>
      <a href="document.html">Document & <br> E-Signature</a>
      
    </div>
  </div>
  <h1>Welcome, <?= htmlspecialchars($member['full_name']) ?></h1>

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
      <div class="icon-title"><i class="fas fa-check-circle"></i><h3>Total Loan Repaid</h3></div>
      <div class="value">₦<?= number_format($repayTotal ?? 0, 2) ?></div>
    </div>
 
    <div class="card">
  <div class="icon-title"><i class="fas fa-file-invoice-dollar"></i><h3>Fixed Deposit Records</h3>
  </div>

  <?php if (count($fdRecords) > 0): ?>
    <?php foreach ($fdRecords as $row): ?>
      <?php
        $amount   = $row['amount'];
        $tenure   = $row['tenure_months'];
        $rate     = $row['interest_rate'];
        $interest = ($amount * $rate / 100) * ($tenure / 12);
        $maturity_date = isset($row['maturity_date']) ? strtotime($row['maturity_date']) : null;
        $today = strtotime(date('Y-m-d'));
        $days_left = $maturity_date ? ceil(($maturity_date - $today) / (60 * 60 * 24)) : null;
      ?>
      <div style="border:1px solid #ccc; padding:12px; margin-bottom:12px; border-radius:8px; background:#f9f9f9;">
        <p><strong>Amount:</strong> ₦<?= number_format($amount, 2) ?></p>
        <p><strong>Tenure:</strong> <?= (int)$tenure ?> months</p>
        <p><strong>Interest Rate:</strong> <?= htmlspecialchars($rate) ?>%</p>
        <p><strong>Estimated Interest:</strong> ₦<?= number_format($interest, 2) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($row['status'] ?? 'pending') ?></p>

        <?php if ($maturity_date): ?>
          <p><strong>Maturity Date:</strong> <?= date('F j, Y', $maturity_date) ?></p>
          <p><strong>Maturity Status:</strong>
            <?php if ($days_left > 0): ?>
              <span style="color: orange;"><?= $days_left ?> days left</span>
            <?php else: ?>
              <span style="color: green;">Matured</span>
            <?php endif; ?>
          </p>
        <?php endif; ?>

        <form method="POST" action="certificate.php">
          <input type="hidden" name="fd_id" value="<?= (int)$row['id'] ?>">
          <button type="submit" style="background:#004080; color:#fff; border:none; padding:8px 14px; border-radius:5px; cursor:pointer;">
            Download Certificate
          </button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>You have no fixed deposit records yet.</p>
  <?php endif; ?>
</div>


  <!-- Loan Due Dates - Written Format -->
<div class="card">
  <div class="icon-title">
    <i class="fas fa-calendar-check"></i>
    <h3>📅 Loan Due Dates</h3>
  </div>
  <?php
    if (!empty($dueDates)) {
        foreach ($dueDates as $due) {
            echo "<p>Amount Due: <strong>₦" . number_format($due['amount_due'], 2) . "</strong> on <strong>" . date('F j, Y', strtotime($due['due_date'])) . "</strong>. Status: <strong>" . ucfirst($due['status']) . "</strong>.</p>";
    }
    } else {
    echo "<p>You have no upcoming loan due dates.</p>";
    }


    
  ?>
  
</div>

<div class="card">
  <div class="icon-title"><i class="fas fa-user-check"></i><h3>Approval Status</h3></div>

  <!-- Loan Approvals -->
  <h4>Loan Approvals</h4>
  <ul>
    <?php if (empty($loanApprovals)): ?>
      <li>No loan approvals found.</li>
    <?php else: ?>
      <?php foreach ($loanApprovals as $loan): ?>
        <?php $status = strtolower($loan['status']); ?>
        <li>
          Loan - <span class="status <?= $status ?>"><?= ucfirst($status) ?></span>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>

  <!-- Fixed Deposit Approvals -->
  <h4>Fixed Deposit Approvals</h4>
  <ul>
    <?php if (empty($fdApprovals)): ?>
      <li>No fixed deposit approvals found.</li>
    <?php else: ?>
      <?php foreach ($fdApprovals as $fd): ?>
        <?php $status = strtolower($fd['status']); ?>
        <li>
          Fixed Deposit - <span class="status <?= $status ?>"><?= ucfirst($status) ?></span>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
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
