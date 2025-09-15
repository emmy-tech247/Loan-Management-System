<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Function to calculate interest
function calculateInterest($amount, $rate, $start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $months_elapsed = ($interval->y * 12) + $interval->m;
    if ($interval->d > 0) $months_elapsed++;
    $interest = ($amount * ($rate / 100) * $months_elapsed) / 12;
    return round($interest, 2);
}

$stmt = $conn->prepare("SELECT id, amount_deposited, tenure_months, interest_rate, start_date, end_date, status FROM fixed_deposits WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fixed Deposit Interest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f4f4;
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

    .navbar .left a:hover {
      background: none;
      padding: 16px 25px;
    }

    .navbar .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .navbar img {
      display: block;
      padding: 0;
      margin: -50px -50px -20px -50px;
    }

    h2 {
      text-align: center;
      margin: 30px 0;
      color: #004080;
    }

    table {
      width: 95%;
      margin: auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
      font-size: 15px;
    }

    th {
      background: #004080;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 200px;
    }

    @media (max-width: 768px) {
      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 20px;
      }

      table, th, td {
        font-size: 13px;
      }

      th, td {
        padding: 8px;
      }

      table {
        width: 100%;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="left">
      <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80"></a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="apply_fd.php">Apply For<br>Fixed Deposit</a>
      <a href="view_fd.php">View Fixed <br>Deposit Record</a>
      <a href="view_interest_fd.php">View Interest<br>Earned Overtime</a>
      <a href="apply_fd_action.php">Withdrawal or<br>Rollover Options</a>
      <a href="view_fd_actions.php">Fixed Deposit <br> Actions Log</a>
    </div>
  </div>

  <h2>📊 Fixed Deposit Interest Summary</h2>

  <table>
    <tr>
      <th>FD ID</th>
      <th>Amount</th>
      <th>Tenure (Months)</th>
      <th>Rate (%)</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Status</th>
      <th>Interest Earned</th>
    </tr>
    <?php while ($fd = $result->fetch_assoc()): 
        $interest = calculateInterest(
            $fd['amount_deposited'],
            $fd['interest_rate'],
            $fd['start_date'],
            $fd['end_date']
        );
    ?>
    <tr>
      <td><?= htmlspecialchars($fd['id']) ?></td>
      <td>₦<?= number_format($fd['amount_deposited'], 2) ?></td>
      <td><?= htmlspecialchars($fd['tenure_months']) ?></td>
      <td><?= htmlspecialchars($fd['interest_rate']) ?>%</td>
      <td><?= htmlspecialchars($fd['start_date']) ?></td>
      <td><?= htmlspecialchars($fd['end_date']) ?></td>
      <td><?= htmlspecialchars(ucfirst($fd['status'])) ?></td>
      <td>₦<?= number_format($interest, 2) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
