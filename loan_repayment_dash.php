<?php
session_start();
require_once "db.php";

// Ensure logged in
if (!isset($_SESSION['member_id'])) {
    die("Unauthorized. Please log in.");
}

$member_id = (int) $_SESSION['member_id'];

// Fetch member info
$sql = "SELECT full_name, account_type FROM members WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$full_name = $user['full_name'];
$account_type = strtolower($user['account_type']); // 'monthly_saver' or 'borrower'

// Set penalty/growth rates
if ($account_type === "monthly_saver") {
    $default_rate = 0.05;  // 5%
    $growth_rate  = 0.002; // 0.2% daily
} else {
    $default_rate = 0.10;  // 10%
    $growth_rate  = 0.004; // 0.4% daily
}

// Fetch loan/savings records
$sql = "SELECT loan_id, loan_amount, amount_paid, due_date 
        FROM loans WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $loan_amount   = (float)$row['loan_amount'];
    $amount_paid   = (float)$row['amount_paid'];
    $due_date      = $row['due_date'];
    $outstanding   = $loan_amount - $amount_paid;

    $today = new DateTime();
    $due   = new DateTime($due_date);

    $default_amount = 0;
    $growth_amount  = 0;

    if ($today > $due && $outstanding > 0) {
        // Calculate days late
        $days_late = $due->diff($today)->days;

        // Apply default penalty
        $default_amount = $outstanding * $default_rate;

        // Apply daily growth
        $growth_amount = $outstanding * $growth_rate * $days_late;
    }

    $total_due = $outstanding + $default_amount + $growth_amount;

    $rows[] = [
        "loan_id"       => $row['loan_id'],
        "loan_amount"   => $loan_amount,
        "amount_paid"   => $amount_paid,
        "due_date"      => $due_date,
        "outstanding"   => $outstanding,
        "default_amt"   => $default_amount,
        "growth_amt"    => $growth_amount,
        "total_due"     => $total_due
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?php echo ucfirst($account_type); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        h2 { text-align: center; color: #0d6efd; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: center; }
        th { background: #0d6efd; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2><?php echo ucfirst($account_type); ?> Dashboard</h2>
    <p><strong>Member:</strong> <?php echo $full_name; ?></p>

    <table>
        <tr>
            <th>Loan ID</th>
            <th>Loan Amount</th>
            <th>Amount Paid</th>
            <th>Outstanding</th>
            <th>Due Date</th>
            <th>Default Penalty</th>
            <th>Growth</th>
            <th>Total Due</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo $r['loan_id']; ?></td>
            <td><?php echo number_format($r['loan_amount'], 2); ?></td>
            <td><?php echo number_format($r['amount_paid'], 2); ?></td>
            <td><?php echo number_format($r['outstanding'], 2); ?></td>
            <td><?php echo $r['due_date']; ?></td>
            <td><?php echo number_format($r['default_amt'], 2); ?></td>
            <td><?php echo number_format($r['growth_amt'], 2); ?></td>
            <td><strong><?php echo number_format($r['total_due'], 2); ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
