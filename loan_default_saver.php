<?php
session_start();
require_once "db.php"; // database connection

// Ensure only admin can view


$today = new DateTime();

// Fetch all members with loans
$sql = "SELECT m.member_id, m.full_name, 
               l.loan_id AS loan_id, l.loan_amount, l.amount_paid, l.start_date, l.due_date, l.loan_category
        FROM members m
        JOIN loans l ON m.member_id = l.member_id
        ORDER BY l.start_date DESC";

$result = $conn->query($sql);

$totals = [
    "loanCollected" => 0,
    "outstanding" => 0,
    "defaultCharges" => 0,
    "dueWithCharges" => 0
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Loan Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body { background: #f0f2f5; }
      .loan-card { border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
      .summary-card { background: #fff; padding: 20px; border-radius: 14px; text-align: center; }
  </style>
</head>
<body class="container py-4">

<h1 class="mb-4">📊 Admin Loan Dashboard</h1>

<div class="row g-3 mb-4">
<?php
while ($row = $result->fetch_assoc()) {
    $loanAmount   = (float)$row['loan_amount'];
    $amountPaid   = (float)$row['amount_paid'];
    $dueDate      = new DateTime($row['due_date']);
    $outstanding  = $loanAmount - $amountPaid;
    $daysOverdue  = 0;
    if ($today > $dueDate && $outstanding > 0) {
        $daysOverdue = $today->diff($dueDate)->days;
    }

    // Apply category-specific rules
    if ($row['loan_category'] === "monthly_saver") {
        $defaultCharge = ($today > $dueDate && $outstanding > 0) ? 0.05 * $outstanding : 0;
        $dailyGrowth   = ($outstanding * 0.002) * $daysOverdue;
    } else { // borrower
        $defaultCharge = ($today > $dueDate && $outstanding > 0) ? 0.10 * $outstanding : 0;
        $dailyGrowth   = ($outstanding * 0.004) * $daysOverdue;
    }

    $totalDue = $outstanding + $defaultCharge + $dailyGrowth;

    // Totals
    $totals["loanCollected"] += $loanAmount;
    $totals["outstanding"]   += $outstanding;
    $totals["defaultCharges"]+= $defaultCharge;
    $totals["dueWithCharges"]+= $totalDue;

    // Card
    echo "
    <div class='col-md-3'>
      <div class='card loan-card p-3'>
        <h5 class='text-primary'>Member: {$row['full_name']} ({$row['loan_category']})</h5>
        <p><strong>Loan ID:</strong> {$row['loan_id']}</p>
        <p><strong>Loan Amount:</strong> ₦".number_format($loanAmount,2)."</p>
        <p><strong>Amount Paid:</strong> ₦".number_format($amountPaid,2)."</p>
        <p><strong>Outstanding:</strong> ₦".number_format($outstanding,2)."</p>
        <p><strong>Due Date:</strong> ".$dueDate->format("d M Y")."</p>
        <p><strong>Default Charge:</strong> ₦".number_format($defaultCharge,2)."</p>
        <p><strong>Daily Growth ({$daysOverdue} days):</strong> ₦".number_format($dailyGrowth,2)."</p>
        <p class='text-danger fw-bold'><strong>Total Due:</strong> ₦".number_format($totalDue,2)."</p>
      </div>
    </div>
    ";
}
?>
</div>

<!-- Dashboard Summary -->
<h2 class="mb-3">📌 Overall Summary</h2>
<div class="row g-3 mb-5">
  <div class="col-md-3"><div class="summary-card"><h6>Total Loan Collected</h6><p class="fw-bold">₦<?= number_format($totals["loanCollected"],2) ?></p></div></div>
  <div class="col-md-3"><div class="summary-card"><h6>Total Outstanding</h6><p class="fw-bold">₦<?= number_format($totals["outstanding"],2) ?></p></div></div>
  <div class="col-md-3"><div class="summary-card"><h6>Total Default Charges</h6><p class="fw-bold">₦<?= number_format($totals["defaultCharges"],2) ?></p></div></div>
  <div class="col-md-3"><div class="summary-card"><h6>Total Due (with Charges)</h6><p class="fw-bold text-danger">₦<?= number_format($totals["dueWithCharges"],2) ?></p></div></div>
</div>

</body>
</html>
