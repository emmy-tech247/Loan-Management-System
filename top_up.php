<?php
require 'db.php';
session_start();

if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    // Unauthorized access – redirect to login or exit
    header("Location: login.php");
    exit();
}

$member_id = intval($_SESSION['member_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount_saved'] ?? null;
    $reference = 'TOPUP-' . uniqid();

    if ($amount !== null && is_numeric($amount)) {
        // Step 1: Insert top-up record
        $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, amount_saved, reference, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("ids", $member_id, $amount, $reference);

        if ($stmt->execute()) {
            $topup_id = $stmt->insert_id;

            // Step 2: Insert approval request
            $appr = $conn->prepare("INSERT INTO approvals (item_type, item_id, step, status) VALUES ('top_up', ?, 1, 'pending')");
            $appr->bind_param("i", $topup_id);
            $appr->execute();

            echo "✅ ₦" . number_format($amount, 2) . " top-up submitted. Awaiting admin approval. Ref: $reference";
        } else {
            echo "❌ Failed to record top-up: " . $stmt->error;
        }
    } else {
        echo "❌ Invalid amount.";
    }
} else {
    echo "❌ Invalid request method.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Top-Up Savings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 18px 25px;
      text-decoration: none;
    }

    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: #f9f9f9;
      min-width: 160px;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown-content a:hover {
      background-color: #ddd;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    h2 {
      text-align: center;
      margin-top: 40px;
      color: #333;
    }

    .form-container {
      max-width: 400px;
      margin: 30px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container label {
      font-weight: bold;
      display: block;
      margin-bottom: 10px;
    }

    .form-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .form-container button {
      width: 100%;
      padding: 12px;
      background-color: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .form-container button:hover {
      background-color: #007bff;
    }

    
footer {
  text-align: center;
  padding: 30px;
  background-color: #004080;
  color: white;
  margin-top: 195px;
}

  </style>
</head>
<body>

  <div class="navbar">
    <div class="left">
      <a href="home.html">Home</a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="set_saving.html">Monthly Savings<br>Amount</a>
      <a href="savings_transactions.php">Transaction Alerts<br>and Summaries</a>
      <a href="top_up.php">Manual Top-up</a>
      <a href="withdraw.html">Withdrawal</a>
    </div>
  </div>

  <h2>💵 Manual Savings Top-Up</h2>

  <div class="form-container">
  
    <form action="top_up.php" method="POST">
      <input type="number" step="0.01" name="amount_saved" placeholder="Enter amount" required>
      <button type="submit">Top Up</button>
    </form>

  </div>

  <script src="script4.js"></script>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
</footer>


</body>
</html>
