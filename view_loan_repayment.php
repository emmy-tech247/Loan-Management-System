<?php
session_start();
require 'db.php';

if (!isset($_SESSION['member_id']) && !isset($_SESSION['adminId'])) {
    die("Unauthorized");
}

$member_id = $_SESSION['member_id'] ?? null;

if (isset($_SESSION['adminId'])) {
    $stmt = $conn->prepare("SELECT * FROM loan_repayments ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM loan_repayments WHERE member_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $member_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Repayment Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 2px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 1.6rem;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px auto;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            font-size: 15px;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .status.pending { color: orange; }
        .status.acknowledged { color: #007bff; }
        .status.approved { color: green; }
        .status.rejected { color: red; }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #004080;
            padding: 0;
            height: 60px;
            margin: 0;
        }

        .navbar .left,
        .navbar .right {
            display: flex;
            align-items: center;
        }

        .navbar a {
            font-size: 16px;
            color: white;
            padding: 26px 25px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .navbar a:not(:has(img)):hover {
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #004080;
            color: white;
            margin-top: 260px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 10px;
            }

            .navbar a {
                padding: 10px;
                font-size: 14px;
            }

            h2 {
                font-size: 1.2rem;
            }

            table, th, td {
                font-size: 14px;
            }

            .btn-view {
                font-size: 13px;
                padding: 5px 8px;
            }
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

    <h2>Loan Repayment Evidence</h2>

    <table>
        <tr>
            <th>Member ID</th>
            <th>Loan ID</th>
            <th>Amount (₦)</th>
            <th>Date</th>
            <th>Receipt</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['member_id']) ?></td>
                <td><?= htmlspecialchars($row['loan_id']) ?></td>
                <td><?= number_format((float)$row['amount_paid'], 2) ?></td>
                <td><?= htmlspecialchars($row['created_at'] ?? 'N/A') ?></td>
                <td>
                    <?php if (file_exists($row['receipt_file'])): ?>
                        <a class="btn-view" href="<?= htmlspecialchars($row['receipt_file']) ?>" target="_blank">View Receipt</a>
                    <?php else: ?>
                        <span style="color: #999;">Missing File</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status <?= htmlspecialchars($row['status']) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <footer>
        <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
