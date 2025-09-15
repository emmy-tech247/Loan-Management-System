<?php
// ---------- DATABASE CONNECTION ----------
$conn = new mysqli("localhost", "root", "", "loan_system");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ---------- FILTER LOGIC ----------
$filter = $_GET['filter'] ?? 'all';
$where = "";

switch ($filter) {
    case 'daily':
        $where = "WHERE DATE(transaction_date) = CURDATE()";
        break;
    case 'monthly':
        $where = "WHERE YEAR(transaction_date) = YEAR(CURDATE()) AND MONTH(transaction_date) = MONTH(CURDATE())";
        break;
    case '3months':
        $where = "WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        break;
    case 'all':
    default:
        $where = "";
}

// ---------- FETCH TRANSACTIONS ----------
// ---------- FETCH TRANSACTIONS ----------
$sql = "
    SELECT 
        t.transaction_id,
        t.member_id,
        m.full_name,
        t.transaction_type,
        t.amount,
        t.transaction_date,
        t.reference
    FROM (
        -- Savings deposits & withdrawals
        SELECT 
            id AS transaction_id,
            member_id,
            type AS transaction_type,
            amount_saved AS amount,
            created_at AS transaction_date,
            CONCAT('SAV-', id) AS reference
        FROM savings_transactions

        UNION ALL

        -- Loan disbursements
        SELECT 
            loan_id AS transaction_id,
            member_id,
            'loan_disbursement' AS transaction_type,
            loan_amount AS amount,
            disbursed_at AS transaction_date,
            CONCAT('LOAN-', loan_id) AS reference
        FROM loans
        WHERE loan_status = 'approved'

        UNION ALL

        -- Loan repayments
        SELECT 
            loan_repayments_id AS transaction_id,
            member_id,
            'loan_repayment' AS transaction_type,
            amount_paid AS amount,
            repayment_date AS transaction_date,
            CONCAT('REPAY-', loan_repayments_id) AS reference
        FROM loan_repayments

        UNION ALL

        -- Fixed deposit applications
        SELECT 
            id AS transaction_id,
            member_id,
            'fixed_deposit' AS transaction_type,
            amount_deposited AS amount,
            created_at AS transaction_date,
            CONCAT('FD-', id) AS reference
        FROM fixed_deposits
    ) AS t
    LEFT JOIN members m ON t.member_id = m.member_id
    $where
    ORDER BY t.transaction_date DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Financial Transactions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; margin: 10px; background: #f8f9fa; }
        h2 { text-align: center; margin-bottom: 20px; }
        .filters { margin-bottom: 15px; text-align: center; }
        .filters a {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px 3px;
            text-decoration: none;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            font-size: 14px;
        }
        .filters a.active { background: #0056b3; }
        .download-btn {
            display: inline-block;
            margin: 10px auto;
            padding: 10px 14px;
            background: green;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .table-container {
            overflow-x: auto;
            background: #fff;
            border-radius: 6px;
            padding: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }
        th { background: #f4f4f4; }
        @media (max-width: 768px) {
            th, td { font-size: 13px; padding: 6px; }
            .filters a, .download-btn { font-size: 12px; padding: 6px 10px; }
        }
        @media (max-width: 480px) { table { min-width: unset; } }
    </style>
</head>
<body>

<h2>Financial Transactions</h2>

<div class="filters">
    <a href="?filter=all" class="<?= $filter == 'all' ? 'active' : '' ?>">All</a>
    <a href="?filter=daily" class="<?= $filter == 'daily' ? 'active' : '' ?>">Today</a>
    <a href="?filter=monthly" class="<?= $filter == 'monthly' ? 'active' : '' ?>">This Month</a>
    <a href="?filter=3months" class="<?= $filter == '3months' ? 'active' : '' ?>">Last 3 Months</a>
</div>

<div style="text-align:center;">
    <a href="download_report.php?filter=<?= $filter ?>" class="download-btn">Download Report</a>
</div>

<div class="table-container">
    <table>
    <tr>
        <th>ID</th>
        <th>Member ID</th>
        <th>Member Name</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Reference</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['transaction_id'] ?></td>
                <td><?= $row['member_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= ucfirst($row['transaction_type']) ?></td>
                <td>₦<?= number_format($row['amount'], 2) ?></td>
                <td><?= $row['transaction_date'] ?></td>
                <td><?= $row['reference'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">No records found</td></tr>
    <?php endif; ?>
</table>

</div>

</body>
</html>
