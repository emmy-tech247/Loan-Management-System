<?php
session_start();
require_once "db.php";

$loan_id = (int) ($_GET['id'] ?? 0);

// Fetch loan details with member info
$sql = "SELECT l.*, 
               m.first_name, m.surname, m.other_names, m.email AS member_email, 
               m.phone_number AS member_phone, m.permanent_address 
        FROM loans l 
        JOIN members m ON l.member_id = m.member_id
        WHERE l.loan_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_assoc();

if (!$loan) {
    die("Loan not found.");
}

// === Workflow for Accountant ===
$workflow = [
    "manager_approved" => ["next_status" => "accountant_approved", "label" => "✅ Approve Loan"],
];

// Handle actions
// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'approve' && isset($workflow[$loan['loan_status']])) {
        $next = $workflow[$loan['loan_status']];
        $update = $conn->prepare("UPDATE loans SET loan_status=?, assigned_to=NULL WHERE loan_id=?");
        $update->bind_param("si", $next['next_status'], $loan_id);
        $update->execute();

        $msg = "✅ Loan approved successfully.";
        header("Location: accountant_review_loan.php?id=$loan_id&msg=" . urlencode($msg));
        exit;

    // ✅ Disburse without status restriction
    } elseif ($action === 'disburse') {
        $update = $conn->prepare("UPDATE loans SET loan_status='disbursed', assigned_to=NULL, disbursed_at=NOW() WHERE loan_id=?");
        $update->bind_param("i", $loan_id);
        $update->execute();

        $msg = "💸 Loan successfully disbursed.";
        header("Location: accountant_review_loan.php?id=$loan_id&msg=" . urlencode($msg));
        exit;

    } elseif ($action === 'reject') {
        $update = $conn->prepare("UPDATE loans SET loan_status='rejected_by_accountant', assigned_to=NULL WHERE loan_id=?");
        $update->bind_param("i", $loan_id);
        $update->execute();

        $msg = "❌ Loan has been rejected by Accountant.";
        header("Location: accountant_review_loan.php?id=$loan_id&msg=" . urlencode($msg));
        exit;
    }
}


if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Accountant Loan Disbursement</title>
  <style>
    body { font-family: "Segoe UI", Arial, sans-serif; background: #eef2f7; margin: 0; padding: 20px; color: #333; }
    .container { background: #fff; padding: 25px; border-radius: 12px; max-width: 1100px; margin: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    h2 { color: #003d6a; margin-bottom: 20px; border-bottom: 2px solid #003d6a; padding-bottom: 8px; }
    h3 { margin-top: 30px; color: #444; font-size: 1.1rem; border-left: 4px solid #003d6a; padding-left: 8px; }
    .card { background: #fafafa; border: 1px solid #e0e0e0; border-radius: 10px; padding: 15px 20px; margin: 15px 0; }
    .card table { width: 100%; border-collapse: collapse; }
    .card th, .card td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
    .card th { width: 220px; font-weight: 600; color: #555; }
    .actions { margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap; }
    .btn { padding: 12px 22px; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; transition: 0.2s ease; }
    .approve { background: #28a745; color: #fff; }
    .approve:hover { background: #218838; }
    .reject { background: #dc3545; color: #fff; }
    .reject:hover { background: #c82333; }
    .back { background: #6c757d; color: #fff; text-decoration: none; padding: 12px 22px; border-radius: 6px; display: inline-block; }
    .back:hover { background: #5a6268; }
    .msg { padding: 12px; margin: 15px 0; border-radius: 6px; font-weight: 500; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  </style>
  <script>
    function confirmDisburse() {
        return confirm("Are you sure you want to disburse this loan?");
    }
  </script>
</head>
<body>
  <div class="container">
    <h2>Loan #<?= htmlspecialchars($loan['loan_id']) ?> (Accountant)</h2>

    <?php if (isset($msg)): ?>
      <div class="msg <?= (strpos($msg,'✅')!==false || strpos($msg,'💸')!==false)?'success':'error' ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Borrower Info -->
    <h3>Borrower Information</h3>
    <div class="card">
      <table>
        <tr><th>Full Name</th><td><?= htmlspecialchars($loan['surname']." ".$loan['first_name']." ".$loan['other_names']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($loan['member_email']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($loan['member_phone']) ?></td></tr>
        <tr><th>Address</th><td><?= htmlspecialchars($loan['permanent_address'] ?? '') ?></td></tr>
        <tr><th>BVN</th><td><?= htmlspecialchars($loan['bvn']) ?></td></tr>
      </table>
    </div>

    <!-- Guarantors -->
    <h3>First Guarantor</h3>
    <div class="card">
      <table>
        <tr><th>Name</th><td><?= htmlspecialchars($loan['guarantor1_name']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($loan['guarantor1_phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($loan['guarantor1_email']) ?></td></tr>
        <tr><th>Address</th><td><?= htmlspecialchars($loan['guarantor1_address']) ?></td></tr>
      </table>
    </div>

    <h3>Second Guarantor</h3>
    <div class="card">
      <table>
        <tr><th>Name</th><td><?= htmlspecialchars($loan['guarantor2_name']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($loan['guarantor2_phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($loan['guarantor2_email']) ?></td></tr>
        <tr><th>Address</th><td><?= htmlspecialchars($loan['guarantor2_address']) ?></td></tr>
      </table>
    </div>

    <!-- Loan Details -->
    <h3>Loan Details</h3>
    <div class="card">
      <table>
        <tr><th>Facility Type</th><td><?= htmlspecialchars($loan['facility_type']) ?></td></tr>
        <tr><th>Purpose</th><td><?= htmlspecialchars($loan['purpose'] ?? '') ?></td></tr>
        <tr><th>Amount</th><td>₦<?= number_format($loan['loan_amount'],2) ?></td></tr>
        <tr><th>Interest Rate</th><td><?= htmlspecialchars($loan['interest_rate']) ?> % per month</td></tr>
        <tr><th>Tenure</th><td><?= htmlspecialchars($loan['tenure_month']) ?> Months</td></tr>
        <tr><th>Total Loan</th><td>₦<?= number_format($loan['total_loan'],2) ?></td></tr>
        <tr><th>Total Repayment</th><td>₦<?= number_format($loan['total_repayment'],2) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($loan['loan_status']) ?></td></tr>
      </table>
    </div>

    <!-- Repayment -->
    <h3>Repayment Details</h3>
    <div class="card">
      <table>
        <tr><th>Repayment Source</th><td><?= htmlspecialchars($loan['repayment_source'] ?? '') ?></td></tr>
        <tr><th>Bank Name</th><td><?= htmlspecialchars($loan['bank_name'] ?? '') ?></td></tr>
        <tr><th>Account Number</th><td><?= htmlspecialchars($loan['account_number'] ?? '') ?></td></tr>
        <tr><th>Account Name</th><td><?= htmlspecialchars($loan['account_name'] ?? '') ?></td></tr>
      </table>
    </div>

    <!-- Actions -->
     <!-- Actions -->
<div class="actions">
  <form method="post">
    <?php if (isset($workflow[$loan['loan_status']])): ?>
      <button type="submit" name="action" value="approve" class="btn approve">
        <?= $workflow[$loan['loan_status']]['label'] ?>
      </button>
    <?php endif; ?>

    <!-- ✅ Disburse button always visible -->
    <button type="submit" name="action" value="disburse" class="btn approve" onclick="return confirmDisburse();">
      💸 Disburse Loan
    </button>

    <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
    <input type="hidden" name="loan_id" value="<?= $loan_id ?>">
  </form>
  <a href="accountant_dashboard.php" class="back">⬅ Back to Dashboard</a>
</div>

    
</body>
</html>
