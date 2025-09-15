<?php
session_start();
require_once 'db.php';

// Secure session check
if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch FD actions
$sql = "
    SELECT 
        a.id,
        m.surname,
        m.email,
        f.amount_deposited,
        f.tenure_months,
        f.interest_rate,
        a.action_type,
        a.action_date,
        a.note
    FROM fd_withdrawals a
    JOIN members m ON a.member_id = m.member_id
    JOIN fixed_deposits f ON a.fd_id = f.id
    ORDER BY a.action_date DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fixed Deposit Actions</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0 10px;
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
      transition: background 0.3s;
    }

    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    
  .navbar .left a:hover,
.navbar .left a:hover img {
  background-color: transparent !important;
  padding: 0 !important;
  margin: -50px -50px -20px -50px !important;
  border-radius: 0 !important;
  cursor: default;
}



    .navbar .left a img {
      display: block;
      padding: 0;
      margin: -50px -50px -20px -50px;
    }

    h2 {
      text-align: center;
      margin: 40px 0 20px;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
      font-size: 14px;
    }

    th {
      background-color: #004080;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top:360px;
    }

    @media (max-width: 768px) {
      .navbar a {
        font-size: 14px;
        padding: 12px 15px;
      }

      table, th, td {
        font-size: 13px;
      }

      .navbar .left a img {
        width: 60px;
        height: auto;
        margin: -30px -30px -10px -30px;
      }

      h2 {
        font-size: 20px;
      }

      footer {
        font-size: 14px;
        padding: 20px;
      }
    }

    @media (max-width: 480px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
      }

      .navbar a {
        width: 100%;
        padding: 10px 15px;
      }

      table, th, td {
        font-size: 12px;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="left">
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80">
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

  <h2>Fixed Deposit Actions Log</h2>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Member</th>
        <th>Email</th>
        <th>Amount</th>
        <th>Tenure (Months)</th>
        <th>Interest Rate (%)</th>
        <th>Action</th>
        <th>Action Date</th>
        <th>Note</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['surname']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>₦<?= number_format($row['amount_deposited'], 2) ?></td>
          <td><?= htmlspecialchars($row['tenure_months']) ?></td>
          <td><?= htmlspecialchars($row['interest_rate']) ?></td>
          <td><?= ucfirst(htmlspecialchars($row['action_type'])) ?></td>
          <td><?= htmlspecialchars($row['action_date']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['note'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p style="text-align:center;">No fixed deposit actions recorded yet.</p>
  <?php endif; ?>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
