<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin2Id'])) {
    header("Location: login.php");
    exit();
}

$admin2_id = $_SESSION['admin2Id'];

// ✅ Handle approval
// ✅ Handle approval
if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);

    // Get withdrawal info
    $stmt = $conn->prepare("SELECT member_id, amount_saved 
                            FROM savings_transactions 
                            WHERE id = ? AND type='withdrawal' AND status = 'acknowledged'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($member_id, $amount);
    if ($stmt->fetch()) {
        $stmt->close();

        // ✅ Calculate current balance from savings_transactions
        $stmt = $conn->prepare("
            SELECT COALESCE(SUM(
                CASE WHEN type='deposit' AND status='approved' THEN amount_saved
                     WHEN type='withdrawal' AND status='approved' THEN -amount_saved
                     ELSE 0 END
            ),0) AS balance
            FROM savings_transactions
            WHERE member_id = ?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $stmt->bind_result($current_balance);
        $stmt->fetch();
        $stmt->close();

        if ($current_balance >= $amount) {
            $conn->begin_transaction();

            // ✅ Approve withdrawal
            $stmt2 = $conn->prepare("UPDATE savings_transactions 
                                     SET status = 'approved', 
                                         approved_by = ?, 
                                         approved_at = NOW() 
                                     WHERE id = ?");
            $stmt2->bind_param("ii", $admin2_id, $id);
            $stmt2->execute();
            $stmt2->close();

            $conn->commit();
            echo "<p style='color: green; text-align: center;'>✅ Withdrawal approved successfully.</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>❌ Insufficient balance.</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>❌ Invalid request.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdrawal Approval – Admin 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f8;
            margin: 0;
            padding: 20px;
        }

        h3 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto 30px;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #eef6ff;
        }

        a {
            display: inline-block;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.2s ease-in-out;
        }

        a:hover {
            background-color: #218838;
        }

        .center-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .logout-btn {
            background-color: #007bff;
            color: #fff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logout-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }

            a {
                font-size: 13px;
                padding: 5px 10px;
            }

            .logout-btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

<h3>Withdrawals Awaiting Approval (Admin 2)</h3>

<?php
$result = $conn->query("SELECT st.id, m.full_name, st.amount_saved AS amount, st.reason, st.created_at 
                        FROM savings_transactions st
                        JOIN members m ON st.member_id = m.member_id
                        WHERE st.type='withdrawal' AND st.status = 'acknowledged'
                        ORDER BY st.created_at DESC");
?>

<table>
    <tr>
        <th>Member</th>
        <th>Amount</th>
        <th>Reason</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= number_format($row['amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><a href="?approve_id=<?= intval($row['id']) ?>" onclick="return confirm('Approve this withdrawal?')">Approve</a></td>
    </tr>
    <?php endwhile; ?>
</table>

<div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
