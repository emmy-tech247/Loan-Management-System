<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT * FROM fixed_deposits WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$res = $stmt->get_result();

$records = [];
while ($row = $res->fetch_assoc()) {
    $amount = $row['amount_deposited'];
    $tenure = $row['tenure_months'];
    $rate = $row['interest_rate'];
    $interest = ($amount * $rate / 100) * ($tenure / 12);
    $row['interest'] = $interest;
    $records[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="robots" content="noindex, nofollow" />
  <title>View Fixed Deposit</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f0f2f5;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 0 20px;
      flex-wrap: wrap;
    }

    .navbar .left, .navbar .right {
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

    h1 {
      text-align: center;
      padding: 30px;
      color: #004080;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .fd-card {
      border: 1px solid #ccc;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      background-color: #f9f9f9;
    }

    .fd-card p {
      margin: 8px 0;
      font-size: 15px;
    }

    .fd-card strong {
      color: #333;
    }

    .fd-card form {
      margin-top: 15px;
    }

    .fd-card button {
      background-color: #004080;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      cursor: pointer;
    }

    .fd-card button:hover {
      background-color: #0056b3;
    }

    .no-records {
      text-align: center;
      color: #888;
      margin-top: 30px;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 100px;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 20px;
      }

      .container {
        padding: 20px;
        margin: 15px;
      }

      .fd-card {
        padding: 15px;
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
        <img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; padding:0; margin: -50px -50px -20px -50px;">
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

  <h1>Your Fixed Deposit Records</h1>

  <div class="container">
    <?php if (count($records) > 0): ?>
      <?php foreach ($records as $row): ?>
        <div class="fd-card">
          <p><strong>Amount:</strong> ₦<?= number_format($row['amount_deposited'], 2) ?></p>
          <p><strong>Tenure:</strong> <?= (int)$row['tenure_months'] ?> months</p>
          <p><strong>Interest Rate:</strong> <?= htmlspecialchars($row['interest_rate']) ?>%</p>
          <p><strong>Estimated Interest:</strong> ₦<?= number_format($row['interest'], 2) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
          <p><strong>Maturity Date:</strong> <?= date('F j, Y', strtotime($row['maturity_date'])) ?></p>

          <?php
            $maturity_date = strtotime($row['maturity_date']);
            $today = strtotime(date('Y-m-d'));
            $days_left = ceil(($maturity_date - $today) / (60 * 60 * 24));
          ?>

          <p>
            <strong>Maturity Status:</strong>
            <?php if ($days_left > 0): ?>
              <span style="color: orange;"><?= $days_left ?> days left</span>
            <?php else: ?>
              <span style="color: green;">Matured</span>
            <?php endif; ?>
          </p>

          <form method="POST" action="certificate.php">
            <input type="hidden" name="fd_id" value="<?= (int)$row['id'] ?>">
            <button type="submit">Download Certificate</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-records">You have no fixed deposit records yet.</p>
    <?php endif; ?>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
