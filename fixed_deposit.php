<?php
session_start();
// Optional: Protect this page to allow only logged-in members
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fixed Deposit</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f5f7fa;
      padding: 0;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      font-family: Arial, sans-serif;
      padding: 10px;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      text-align: center;
      padding: 15px 20px;
      text-decoration: none;
    }

    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .header {
      background-color: #fff;
      color: #0056b3;
      padding: 50px 20px;
      text-align: center;
    }

    .header h1 {
      margin: 0 0 10px;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 30px;
      padding: 0 20px;
    }

    .container img {
      width: 100%;
      border-radius: 10px;
      flex: 1 1 45%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .text {
      flex: 1 1 45%;
    }

    .text h2 {
      color: #004080;
      margin-bottom: 15px;
    }

    .text p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 10px;
    }

    .text ul {
      padding-left: 20px;
    }

    .text ul li {
      margin-bottom: 8px;
    }

    .cta-button {
      display: inline-block;
      padding: 12px 20px;
      background-color: #004080;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      margin-top: 20px;
      transition: background-color 0.3s;
    }

    .cta-button:hover {
      background-color: #0056b3;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 100px;
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <div class="navbar">
    <div class="left">
      <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style=" display: block; padding:0px;margin: -50px -50px -20px -50px;"> </a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="apply_fd.php">Apply For<br>Fixed Deposit</a>
      <a href="view_fd.php">View Fixed <br>Deposit Record</a>
      <a href="view_interest_fd.php">View Interest<br>Earned Overtime</a>
      <a href="apply_fd_action.php">Withdrawal or<br>Rollover Options </a>
      <a href="view_fd_actions.php">Fixed Deposit <br> Actions Log</a>
     
    </div>
  </div>

  <!-- Header -->
  <div class="header">
    <h1>Secure Your Future with Fixed Deposits</h1>
    <p>Grow your savings with guaranteed returns and peace of mind.</p>
  </div>

  <!-- Main Content -->
  <div class="container">
    <img src="images/piggy.JPG" alt="Fixed Deposit" width="100" height="400px">
    <div class="text">
      <h2>Why Choose Our Fixed Deposit?</h2>
      <ul>
        <li>Tenures from 3 to 24 months</li>
        <li>Interest rates up to 15% per annum</li>
        <li>Guaranteed principal and returns</li>
        <li>Easy rollover or withdrawal options</li>
        <li>Real-time tracking via dashboard</li>
      </ul>
      <a href="apply_fd.php" class="cta-button">Open Fixed Deposit Now</a>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
