<?php 
require 'db.php';
session_start();

// Fetch member details if logged in
$member = null;

if (isset($_SESSION['member_id'])) {
    $member_id = $_SESSION['member_id'];
    $stmt = $conn->prepare("SELECT member_id, first_name FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Loan Communication and Support</title>
  <link rel="stylesheet" href="css/style8.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 0; margin: 3px;
      background: #f0f2f5;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color:  #004080;
      padding: 20px 25px;
    }

    .navbar a {
      color: #ecf0f1;
      text-decoration: none;
      margin: 0 10px;
      font-size: 16px;
    }

    .navbar a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    h2 {
      color: #2980b9;
      margin-bottom: 20px;
      font-size: 26px;
      border-left: 5px solid #2980b9;
      padding-left: 15px;
    }

    .page {
      line-height: 1.8;
      color: #000;
      font-size: 16px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        margin: 5px 0;
      }

      .container {
        margin: 20px;
        padding: 20px;
      }
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

  <!-- Navigation Bar -->
  <div class="navbar">
    <div class="left">
      <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style=" display: block; padding:0px;margin: -50px -50px -20px -50px;"> </a>
    </div>
    <div class="right">
      <a href="member.php">← Back</a>
      <a href="faq.php">FAQ</a>
      <a href="announcement.php">Announcements</a>
     <a href="member_chat_dashboard.php">Chat with Admin</a>

    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <h2>Welcome to the Communication Portal</h2>
    <p class="page">
      The communication page of the Loan Management System serves as a central hub for interaction between members and administrators.
      It allows users to send inquiries, report issues, and receive updates regarding their loans, savings, or fixed deposits.
    </p>
    <p>
      The page features a secure live chat, announcement board, and support ticket system to ensure quick and reliable communication.
      It also provides access to FAQs and contact details.
    </p>
    <p>
      This communication channel enhances transparency, builds trust, and improves service efficiency by ensuring timely feedback,
      status updates, and personalized support—ultimately creating a more responsive and user-friendly financial service environment
      for all stakeholders.
    </p>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
