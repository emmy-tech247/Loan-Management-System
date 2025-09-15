<?php
session_start();
include('db.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['approval_id'], $_POST['fd_id']) && $_POST['action'] === 'acknowledge') {
    $approvalId = intval($_POST['approval_id']);
    $fd_id = intval($_POST['fd_id']);
    $adminId = intval($_SESSION['adminId']);

    // Secure update: using prepared statement for first update
    $stmt1 = $conn->prepare("UPDATE approvals SET status = 'acknowledged', approver_id = ? WHERE id = ?");
    $stmt1->bind_param("ii", $adminId, $approvalId);
    $stmt1->execute();

    // Insert next approval step (Admin2)
    $stmt2 = $conn->prepare("INSERT INTO approvals (item_type, item_id, status, approver_id, step, created_at)
                             VALUES ('fixed_deposit', ?, 'pending', 2, 2, NOW())");
    $stmt2->bind_param("i", $fd_id);
    $stmt2->execute();

    echo "<script>alert('✅ Acknowledged by Admin1.'); window.location='acknowledge_fd.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin1 Acknowledge FD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      padding: 20px;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    h1 {
      font-size: 24px;
      margin-bottom: 15px;
    }
    p {
      font-size: 16px;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      margin-top: 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }
    .btn:hover {
      background-color: #0056b3;
    }

    /* Prevent logo hover effect if used elsewhere */
    .logo:hover {
      transform: none !important;
      filter: none !important;
    }

    @media (max-width: 600px) {
      body {
        padding: 10px;
      }
      .container {
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔐 Fixed Deposit Acknowledgment</h1>
    <p>Processing your acknowledgment...</p>
    <a href="acknowledge_fd.php" class="btn">🔙 Back to List</a>
  </div>
</body>
</html>
