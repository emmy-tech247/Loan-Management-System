<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $reason = trim($_POST['reason']);

    // ✅ Check balance (sum of approved deposits - approved withdrawals)
    $stmt = $conn->prepare("
        SELECT 
            (SELECT IFNULL(SUM(amount_saved), 0) 
             FROM savings_transactions 
             WHERE member_id = ? AND type='deposit' AND status = 'approved') 
          - 
            (SELECT IFNULL(SUM(amount_saved), 0) 
             FROM savings_transactions 
             WHERE member_id = ? AND type='withdrawal' AND status IN ('pending','approved')) 
        AS balance
    ");
    $stmt->bind_param("ii", $member_id, $member_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    if ($amount > $balance) {
        $message = '<div class="error">Insufficient funds.</div>';
    } else {
        // ✅ Insert withdrawal request into savings_transactions
        $reference = uniqid('wd_'); // unique reference for tracking
        $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, type, amount_saved, reference, reason, status) VALUES (?, 'withdrawal', ?, ?, ?, 'pending')");
        $stmt->bind_param("idss", $member_id, $amount, $reference, $reason);

        if ($stmt->execute()) {
            $message = '<div class="success">Withdrawal request submitted.</div>';
        } else {
            $message = '<div class="error">Error submitting request.</div>';
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Withdraw Funds</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f8;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 18px 25px;
      text-decoration: none;
    }

   
    .navbar .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    form {
      background: white;
      max-width: 400px;
      margin: 40px auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    input[type="number"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      resize: vertical;
    }

    button[type="submit"] {
      background: #007bff;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
      background: #0056b3;
    }

    .success {
      color: #28a745;
      text-align: center;
      margin-bottom: 10px;
    }

    .error {
      color: #dc3545;
      text-align: center;
      margin-bottom: 10px;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 85px;
    }

    @media (max-width: 768px) {
      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 20px;
      }

      form {
        margin: 20px;
        padding: 20px;
      }

      footer {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="navbar">
  <div class="left">
    <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; padding: 0; margin: -50px -50px -20px -50px;"></a>
  </div>
  <div class="right">
    <a href="member.php">Back</a>
    <a href="set_saving.html">Monthly Savings<br> Amount</a>
    <a href="savings_transactions.php">Transaction Alerts<br>and Summaries</a>
    <a href="upload_topups_receipt.php">Savings</a>
    <a href="withdraw_funds.php">Withdrawal</a>
  </div>
</div>

<form method="POST">
  <h2>Withdraw Funds</h2>
  <?= $message ?>
  <input type="number" name="amount" placeholder="Amount to withdraw" required>
  <textarea name="reason" placeholder="Reason for withdrawal" required></textarea>
  <button type="submit">Submit Withdrawal</button>
</form>

<footer>
  <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
</footer>

</body>
</html>
