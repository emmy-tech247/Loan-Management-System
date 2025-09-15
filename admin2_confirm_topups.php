<?php
session_start();
require_once 'db.php';

// Approval Logic
if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);

    $stmt = $conn->prepare("UPDATE savings_transactions SET status = 'approved', approved_by = ? WHERE id = ? AND status = 'acknowledged'");
    $stmt->bind_param("ii", $admin2_id, $id);
    $stmt->execute();

    echo "<div style='text-align:center; color:green; font-weight:bold;'>✅ Approved and savings updated.</div><br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Approve Acknowledged Savings</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f7fa;
      margin: 0;
      padding: 20px;
      color: #333;
    }

    h3 {
      color: #2c3e50;
      font-size: 24px;
      margin-bottom: 20px;
      text-align: center;
    }

    .transaction {
      background-color: #fff;
      border-left: 5px solid #3498db;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .transaction p {
      margin: 8px 0;
      font-size: 16px;
    }

    .transaction a {
      display: inline-block;
      margin-top: 5px;
      margin-right: 10px;
      text-decoration: none;
      color: #2980b9;
      font-weight: bold;
      transition: color 0.3s ease;
    }

    .transaction a:hover {
      color: #1abc9c;
    }

    .approve-btn {
      background-color: #2ecc71;
      color: white;
      padding: 6px 12px;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .approve-btn:hover {
      background-color: #27ae60;
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
      .transaction p {
        font-size: 14px;
      }

      .approve-btn {
        padding: 6px 10px;
        font-size: 14px;
      }

      .logout-btn {
        font-size: 14px;
        padding: 10px 20px;
      }

      body {
        padding: 10px;
      }
    }
  </style>
</head>
<body>

<h3>Admin2: Acknowledged Transactions to Approve</h3>

<?php
$result = $conn->query("SELECT * FROM savings_transactions WHERE status = 'acknowledged'");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='transaction'>
            <p><strong>Member ID:</strong> " . htmlspecialchars($row['member_id']) . "</p>
            <p><strong>Amount:</strong> ₦" . number_format((float)$row['amount_saved'], 2) . "</p>
            <a href='" . htmlspecialchars($row['receipt_file']) . "' target='_blank'>📄 View Receipt</a>
            <a class='approve-btn' href='admin2_confirm_savings.php?approve_id=" . intval($row['id']) . "'>✅ Approve</a>
        </div>";
    }
} else {
    echo "<p style='text-align:center;'>No acknowledged transactions found.</p>";
}
$conn->close();
?>

<div class="center-container">
  <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
