<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin1Id'])) {
    header("Location: login.php");
    exit();
}

$admin1_id = $_SESSION['admin1Id'];

// ✅ Handle acknowledgment
if (isset($_GET['acknowledge_id'])) {
    $id = intval($_GET['acknowledge_id']);
    $stmt = $conn->prepare("
        UPDATE savings_transactions 
        SET status = 'acknowledged', acknowledged_by = ? 
        WHERE id = ? AND type='withdrawal' AND status = 'pending'
    ");
    $stmt->bind_param("ii", $admin1_id, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin1_acknowledge_withdrawals.php");
    exit();
}

// ✅ Fetch pending withdrawals from savings_transactions
$result = $conn->query("
    SELECT st.id, m.full_name, st.amount_saved AS amount, st.reason, st.created_at 
    FROM savings_transactions st
    JOIN members m ON st.member_id = m.member_id
    WHERE st.type='withdrawal' AND st.status = 'pending'
    ORDER BY st.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pending Withdrawals</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f4f8;
      margin: 0;
      padding: 20px;
    }

    h3 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      word-break: break-word;
    }

    th {
      background-color: #007bff;
      color: white;
      text-transform: uppercase;
      font-size: 14px;
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
      align-items: center;
      margin: 40px 0;
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
      background-color: #0056b3;
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      th, td {
        font-size: 14px;
        padding: 10px;
      }

      a {
        font-size: 13px;
        padding: 5px 10px;
      }

      .logout-btn {
        font-size: 14px;
        padding: 10px 20px;
      }

      h3 {
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      table {
        width: 100%;
      }

      h3 {
        font-size: 18px;
      }
    }
  </style>
</head>
<body>

<h3>Pending Withdrawals (Admin)</h3>

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
    <td><a href="?acknowledge_id=<?= intval($row['id']) ?>">Acknowledge</a></td>
  </tr>
  <?php endwhile; ?>
</table>

<div class="center-container">
  <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
