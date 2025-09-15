<?php
session_start();
include('db.php');

// Get filter value from query string (default to 'all')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// =========================
// Summary stats query
// =========================
$summary_sql = "
    SELECT 
        IFNULL(SUM(l.loan_amount), 0) AS total_loan_given,
        IFNULL(SUM(l.amount_paid), 0) AS total_repaid,
        IFNULL(SUM(l.loan_amount + (l.loan_amount * l.interest_rate / 100) - l.amount_paid), 0) AS total_outstanding,
        IFNULL(SUM(
            CASE WHEN l.loan_status = 'defaulted' 
            THEN (l.loan_amount + (l.loan_amount * l.interest_rate / 100) - l.amount_paid) 
            ELSE 0 END
        ), 0) AS total_defaulted
    FROM loans l
";
$summary_result = $conn->query($summary_sql);
$summary_data = [
    'total_loan_given' => 0,
    'total_repaid' => 0,
    'total_outstanding' => 0,
    'total_defaulted' => 0
];
if ($summary_result && $summary_result->num_rows > 0) {
    $summary_data = $summary_result->fetch_assoc();
}

// =========================
// Main loan records query
// =========================
$sql = "
    SELECT 
        l.loan_id,
        m.member_id,
        CONCAT(m.surname, ' ', m.first_name) AS member_name,
        l.loan_amount,
        l.interest_rate,
        l.start_date,
        l.end_date,
        l.loan_status,
        l.purpose,
        CONCAT(g.surname, ' ', g.first_name) AS guarantor_name,
        l.monthly_repayment,
        l.amount_paid,
        (l.loan_amount + (l.loan_amount * l.interest_rate / 100) - l.amount_paid) AS loan_balance
    FROM loans l
    INNER JOIN members m ON l.member_id = m.member_id
    INNER JOIN guarantors g ON l.guarantor_id = g.guarantor_id
";

// Apply filters
if ($filter === 'day') {
    $sql .= " WHERE DATE(l.start_date) = CURDATE()";
} elseif ($filter === 'month') {
    $sql .= " WHERE YEAR(l.start_date) = YEAR(CURDATE()) AND MONTH(l.start_date) = MONTH(CURDATE())";
} elseif ($filter === '3months') {
    $sql .= " WHERE l.start_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
}

$sql .= " ORDER BY l.start_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Database query failed: " . $conn->error);
}



// Get filter value from query string (default to 'all')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Base query with new fields
$sql = "
    SELECT 
        l.loan_id,
        m.member_id,
        CONCAT(m.surname, ' ', m.first_name) AS member_name,
        l.loan_amount,
        l.interest_rate,
        l.start_date,
        l.end_date,
        l.loan_status,
        l.purpose,
        CONCAT(g.surname, ' ', g.first_name) AS guarantor_name,
        l.monthly_repayment,
        l.amount_paid,
        (l.loan_amount + (l.loan_amount * l.interest_rate / 100) - l.amount_paid) AS loan_balance
    FROM loans l
    INNER JOIN members m ON l.member_id = m.member_id
    INNER JOIN guarantors g ON l.guarantor_id = g.guarantor_id
";

// Apply filters
if ($filter === 'day') {
    $sql .= " WHERE DATE(l.start_date) = CURDATE()";
} elseif ($filter === 'month') {
    $sql .= " WHERE YEAR(l.start_date) = YEAR(CURDATE()) AND MONTH(l.start_date) = MONTH(CURDATE())";
} elseif ($filter === '3months') {
    $sql .= " WHERE l.start_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
}

$sql .= " ORDER BY l.start_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Loan Records</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            margin: 0;
            background: #f9f9f9;
        }
        h2 {
            text-align: center;
            margin-top: 10px;
            color: #333;
        }
        .filter-container {
            text-align: center;
            margin: 10px 0;
        }
        select {
            padding: 6px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #f4f4f4;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        tbody tr:nth-child(odd) {
            background: #fff;
        }
        .table-wrapper {
            overflow-x: auto;
        }

        /* Responsive Table for Mobile */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tbody tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                background: #fff;
                padding: 10px;
                border-radius: 6px;
            }
            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: left;
            }
            td::before {
                position: absolute;
                left: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #333;
            }
            td:nth-of-type(1)::before { content: "Loan ID"; }
            td:nth-of-type(2)::before { content: "Member ID"; }
            td:nth-of-type(3)::before { content: "Member Name"; }
            td:nth-of-type(4)::before { content: "Loan Amount"; }
            td:nth-of-type(5)::before { content: "Interest Rate"; }
            td:nth-of-type(6)::before { content: "Start Date"; }
            td:nth-of-type(7)::before { content: "End Date"; }
            td:nth-of-type(8)::before { content: "Status"; }
            td:nth-of-type(9)::before { content: "Purpose"; }
            td:nth-of-type(10)::before { content: "Guarantor Name"; }
            td:nth-of-type(11)::before { content: "Monthly Repayment"; }
            td:nth-of-type(12)::before { content: "Amount Paid"; }
            td:nth-of-type(13)::before { content: "Loan Balance"; }
        }
    </style>
</head>
<body>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 10px; }
        h2 { text-align: center; color: #333; }
        .stats-box { display: flex; justify-content: space-around; margin: 20px auto; max-width: 900px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; flex: 1; margin: 5px; }
        .stat-title { font-size: 14px; color: #666; }
        .stat-value { font-size: 18px; font-weight: bold; margin-top: 5px; }
    </style>
    <h2>All Loan Records</h2>

     <!-- Summary Stats -->
    <div class="stats-box">
        <div class="stat-card">
            <div class="stat-title">Total Loan Given Out</div>
            <div class="stat-value">₦<?php echo number_format($summary_data['total_loan_given'], 2); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Amount Repaid</div>
            <div class="stat-value">₦<?php echo number_format($summary_data['total_repaid'], 2); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Outstanding Loan</div>
            <div class="stat-value">₦<?php echo number_format($summary_data['total_outstanding'], 2); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Defaulted Amount</div>
            <div class="stat-value">₦<?php echo number_format($summary_data['total_defaulted'], 2); ?></div>
        </div>
    </div>

    <!-- Filter & Export Buttons -->
    

    <div class="filter-container">
        <form method="GET">
            <label for="filter">Filter by:</label>
            <select name="filter" id="filter" onchange="this.form.submit()">
                <option value="all" <?php if($filter==='all') echo 'selected'; ?>>All</option>
                <option value="day" <?php if($filter==='day') echo 'selected'; ?>>Today</option>
                <option value="month" <?php if($filter==='month') echo 'selected'; ?>>This Month</option>
                <option value="3months" <?php if($filter==='3months') echo 'selected'; ?>>Last 3 Months</option>
            </select>
        </form>
    </div>

    <div style="text-align:center; margin: 15px 0;">
    <form method="GET" action="export_loans.php" style="display:inline-block;">
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
        <input type="hidden" name="type" value="pdf">
        <button type="submit" style="padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Export as PDF
        </button>
    </form>

    <form method="GET" action="export_loans.php" style="display:inline-block;">
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
        <input type="hidden" name="type" value="excel">
        <button type="submit" style="padding:8px 15px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            Export as Excel
        </button>
    </form>
</div>


    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Member ID</th>
                    <th>Member Name</th>
                    <th>Loan Amount</th>
                    <th>Interest Rate</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Purpose</th>
                    <th>Guarantor Name</th>
                    <th>Monthly Repayment</th>
                    <th>Amount Paid</th>
                    <th>Loan Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['loan_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                        <td><?php echo number_format($row['loan_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['interest_rate']); ?>%</td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['loan_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                        <td><?php echo htmlspecialchars($row['guarantor_name']); ?></td>
                        <td><?php echo number_format($row['monthly_repayment'], 2); ?></td>
                        <td><?php echo number_format($row['amount_paid'], 2); ?></td>
                        <td><?php echo number_format($row['loan_balance'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
