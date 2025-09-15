<?php
session_start();
require_once "db.php"; // database connection

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    die("Unauthorized. Please log in.");
}

$member_id = (int) $_SESSION['member_id'];

// Fetch member name (assuming full_name exists in members table)
$memberQuery = $conn->prepare("SELECT full_name FROM members WHERE member_id = ?");
$memberQuery->bind_param("i", $member_id);
$memberQuery->execute();
$memberResult = $memberQuery->get_result()->fetch_assoc();
$memberName = $memberResult['full_name'] ?? "Member";

// Fetch member loans (including loan_category)
$sql = "SELECT loan_id, loan_amount, amount_paid, start_date, due_date, loan_category
        FROM loans WHERE member_id = ? ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$totalLoanCollected = 0;
$totalOutstanding = 0;
$totalDefaultCharges = 0;
$totalDueWithCharges = 0;

$today = new DateTime();
$chartLabels = [];
$chartOutstanding = [];
$chartPaid = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Loan Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
      body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
      .navbar { background: linear-gradient(90deg, #004080, #007bff); }
      .navbar-brand, .navbar .text-white { color: #fff !important; }
      .loan-card { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff; padding: 20px; transition: 0.3s; }
      .loan-card:hover { transform: translateY(-4px); }
      .summary-card { padding: 20px; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
      .progress { height: 16px; border-radius: 10px; }

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
    .navbar .left a:hover {
      background-color: transparent;
    }

    /* Hover effect only for nav links (not logo) */
    .navbar .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    h2 {
      text-align: center;
      margin-top: 30px;
    }

  </style>
</head>
<body>
    <div class="navbar">
    <div class="left">
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; margin: -50px -50px -20px -50px;">
      </a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="loan_saver_borrower.php">Loan Application Form</a>
      <a href="loan_status.php">Status Tracking</a>
      <a href="upload_loan_repayment.php">Loan Repayment</a>
      <a href="view_loan_repayment.php">Loan Repayment Status</a>
      <a href="loan_report.php">Loan Report </a>
      <a href="loan_calculator.html">Loan Calculator</a>
    </div>
  </div>


  <a class="navbar-brand" href="#">📊 Loan Report</a>
  <div class="ms-auto text-white">
    Welcome, <strong><?= htmlspecialchars($memberName) ?></strong>
  </div>


<div class="container">

  <div class="row g-1 mb-12">
    <?php
    while ($loan = $result->fetch_assoc()) {
        $loanAmount   = (float)$loan['loan_amount'];
        $amountPaid   = (float)$loan['amount_paid'];
        $dueDate      = new DateTime($loan['due_date']);
        $outstanding  = $loanAmount - $amountPaid;
        $loanCategory = strtolower($loan['loan_category']); // e.g., 'saver', 'borrower'

        $totalLoanCollected += $loanAmount;

        // Rules by loan category
        if ($loanCategory === "loan_savers") {
            $defaultRate = 0.05;   // 5%
            $dailyRate   = 0.002;  // 0.2%
        } else { // loan_borrowers
            $defaultRate = 0.10;   // 10%
            $dailyRate   = 0.004;  // 0.4%
        }

        // Default & overdue
        $defaultCharge = 0;
        $daysOverdue   = 0;
        if ($today > $dueDate && $outstanding > 0) {
            $defaultCharge = $defaultRate * $outstanding;
            $daysOverdue   = $today->diff($dueDate)->days;
        }

        $dailyGrowth = ($outstanding * $dailyRate) * $daysOverdue;
        $totalDue    = $outstanding + $defaultCharge + $dailyGrowth;

        $totalOutstanding    += $outstanding;
        $totalDefaultCharges += $defaultCharge;
        $totalDueWithCharges += $totalDue;

        $progressPercent = $loanAmount > 0 ? ($amountPaid / $loanAmount) * 100 : 0;

        $chartLabels[]      = $dueDate->format("M Y");
        $chartOutstanding[] = $outstanding;
        $chartPaid[]        = $amountPaid;

        // Display loan card
        echo "
        <div class='col-md-6'>
          <div class='loan-card'>
            <h5 class='text-primary'>Loan ID: {$loan['loan_id']} ({$loanCategory})</h5>
            <p><strong>Loan Amount:</strong> ₦".number_format($loanAmount,2)."</p>
            <p><strong>Amount Paid:</strong> ₦".number_format($amountPaid,2)."</p>
            <p><strong>Outstanding:</strong> ₦".number_format($outstanding,2)."</p>
            <p><strong>Due Date:</strong> ".$dueDate->format("d M Y")."</p>
            <p><strong>Default Charge (".($defaultRate*100)."%)</strong>: ₦".number_format($defaultCharge,2)."</p>
            <p><strong>Daily Growth (".($dailyRate*100)."% × $daysOverdue days)</strong>: ₦".number_format($dailyGrowth,2)."</p>
            <p class='text-danger fw-bold'><strong>Total Due:</strong> ₦".number_format($totalDue,2)."</p>
            
            <div class='progress mb-2'>
              <div class='progress-bar bg-success' style='width:".min($progressPercent,100)."%;'>
                ".round($progressPercent,2)."% Paid
              </div>
            </div>
          </div>
        </div>";
    }
    ?>
  </div>

  <!-- Summary -->
  <h2 class="mb-3">📌 Summary</h2>
  <div class="row g-3 mb-5">
    <div class="col-md-3"><div class="summary-card"><h6>Total Loan Collected</h6><p class="fw-bold text-primary">₦<?= number_format($totalLoanCollected,2) ?></p></div></div>
    <div class="col-md-3"><div class="summary-card"><h6>Total Outstanding</h6><p class="fw-bold text-warning">₦<?= number_format($totalOutstanding,2) ?></p></div></div>
    <div class="col-md-3"><div class="summary-card"><h6>Total Default Charges</h6><p class="fw-bold text-danger">₦<?= number_format($totalDefaultCharges,2) ?></p></div></div>
    <div class="col-md-3"><div class="summary-card"><h6>Total Due w/ Charges</h6><p class="fw-bold text-danger">₦<?= number_format($totalDueWithCharges,2) ?></p></div></div>
  </div>

  <!-- Chart -->
  <h2 class="mb-3">📈 Monthly Loan Repayment Tracking</h2>
  <div class="card p-4 mb-5">
    <canvas id="repaymentChart" height="100"></canvas>
  </div>
</div>

<script>
const ctx = document.getElementById('repaymentChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            { label: 'Outstanding', data: <?= json_encode($chartOutstanding) ?>, backgroundColor: 'rgba(255,99,132,0.6)' },
            { label: 'Paid', data: <?= json_encode($chartPaid) ?>, backgroundColor: 'rgba(54,162,235,0.6)' }
        ]
    },
    options: {
        responsive: true,
        plugins: { title: { display: true, text: 'Loan Repayment Overview' }, legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { callback: val => '₦' + val.toLocaleString() } } }
    }
});
</script>
</body>
</html>
