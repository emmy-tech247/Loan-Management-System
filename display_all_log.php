<?php
session_start();
include('db.php');

// AUTO DELETE logs older than 3 months
$conn->query("DELETE FROM full_activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH)");

// Date filter
$filter = $_GET['filter'] ?? 'daily'; // default daily

switch ($filter) {
    case 'monthly':
        $sql = "SELECT * FROM full_activity_logs 
                WHERE role IN ('member','relationship_officer','admin','manager','accountant','auditor','md')
                AND MONTH(created_at) = MONTH(CURRENT_DATE())
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
                ORDER BY created_at DESC";
        break;

    case 'quarterly':
        $sql = "SELECT * FROM full_activity_logs 
                WHERE role IN ('member','relationship_officer','admin','manager','accountant','auditor','md')
                AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)
                ORDER BY created_at DESC";
        break;

    default: // daily
        $sql = "SELECT * FROM full_activity_logs 
                WHERE role IN ('member','relationship_officer','admin','manager','accountant','auditor','md')
                AND DATE(created_at) = CURDATE()
                ORDER BY created_at DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs - Managing Director</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #004494; color: white; }
        tr:nth-child(even) { background-color: #f8f8f8; }
        .filter-links a { margin-right: 10px; text-decoration: none; color: #004494; }
        .filter-links a.active { font-weight: bold; text-decoration: underline; }

        
        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #6c757d;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
        }
        .back-link:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>

<h2>Loan Management System - Activity Logs (Managing Director)</h2>

<div class="filter-links">
    <a href="?filter=daily" class="<?= $filter == 'daily' ? 'active' : '' ?>">Daily</a>
    <a href="?filter=monthly" class="<?= $filter == 'monthly' ? 'active' : '' ?>">Monthly</a>
    <a href="?filter=quarterly" class="<?= $filter == 'quarterly' ? 'active' : '' ?>">Last 3 Months</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Role</th>
        <th>Activity</th>
        <th>IP Address</th>
        <th>Date & Time</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['activity']) ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">No activity records found for this period.</td></tr>
    <?php endif; ?>
</table>
 <a href="admin2.php" class="back-link">⬅️ Back </a>

</body>
</html>
