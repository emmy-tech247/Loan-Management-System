<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount_deposited']);
    $tenure = intval($_POST['tenure']);
    $rate = floatval($_POST['rate']);

    if ($amount > 0 && $tenure > 0 && $rate > 0) {
        $start_date = date('Y-m-d');
        $maturity_date = date('Y-m-d', strtotime("+$tenure months", strtotime($start_date)));

        $stmt = $conn->prepare("INSERT INTO fixed_deposits (member_id, amount_deposited, tenure_months, interest_rate, start_date, maturity_date, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ididss", $member_id, $amount, $tenure, $rate, $start_date, $maturity_date);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $fd_id = $stmt->insert_id;

            $insertApproval = $conn->prepare("INSERT INTO approvals (item_type, item_id, status, approver_id, step, created_at) VALUES ('fixed_deposit', ?, 'pending', 1, 1, NOW())");
            $insertApproval->bind_param("i", $fd_id);
            $insertApproval->execute();

            echo "<script>alert('✅ Fixed Deposit Application Submitted. Awaiting Admin Approval.'); window.location='member.php';</script>";
        } else {
            echo "<script>alert('❌ Error submitting application.');</script>";
        }
    } else {
        echo "<script>alert('❌ Invalid input. Please check your entries.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Apply for Fixed Deposit - DCG Cooperative Society" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Apply for Fixed Deposit</title>
  <link rel="stylesheet" href="css/style5.css">
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
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
      padding: 16px 25px;
      text-decoration: none;
    }

  .navbar .left a:hover,
.navbar .left a:hover img {
  background-color: transparent !important;
  padding: 0 !important;
  margin: -50px -50px -20px -50px !important;
  border-radius: 0 !important;
  cursor: default;
}


    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }


    h2 {
      text-align: center;
      margin-top: 40px;
      color: #333;
    }

    .form-container {
      max-width: 450px;
      margin: 30px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container label {
      font-weight: bold;
      display: block;
      margin: 15px 0 5px;
    }

    .form-container input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .form-container button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background-color: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .form-container button:hover {
      background-color: #007bff;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 100px;
    }

    @media (max-width: 768px) {
      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 20px;
      }

      .form-container {
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
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; padding: 0; margin: -50px -50px -20px -50px;">
      </a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="apply_fd.php">Apply For<br>Fixed Deposit</a>
      <a href="view_fd.php">View Fixed <br>Deposit Record</a>
      <a href="view_interest_fd.php">View Interest<br>Earned Overtime</a>
      <a href="apply_fd_action.php">Withdrawal or<br>Rollover Options</a>
      <a href="view_fd_actions.php">Fixed Deposit <br>Actions Log</a>
    </div>
  </div>

  <h2>📥 Apply for Fixed Deposit</h2>

  <div class="form-container">
    <form action="apply_fd.php" method="POST" autocomplete="off">
      <label for="amount">Amount (₦):</label>
      <input type="number" name="amount_deposited" id="amount" required min="1" step="0.01">

      <label for="tenure">Tenure (Months):</label>
      <input type="number" name="tenure" id="tenure" required min="1" max="60">

      <label for="rate">Interest Rate (%):</label>
      <input type="number" name="rate" id="rate" step="0.01" required min="0.01" max="100">

      <button type="submit" name="submit">Submit Application</button>
    </form>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
