<?php
session_start();
include('db.php'); // Optional: database logging


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - DCG</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fa;
      margin: 0;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 15px 20px;
      flex-wrap: wrap;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .navbar a:hover:not(.logo) {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .navbar img {
      width: 80px;
      height: 80px;
      margin: -40px 0 -20px -40px;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #004080;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    button {
      width: 100%;
      background-color: #004080;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    .feedback {
      text-align: center;
      margin-bottom: 20px;
      font-weight: bold;
      color: #d9534f;
    }

    .feedback.success {
      color: #28a745;
    }
    h1{
      text-align:center;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 100px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        margin: 10px 0;
      }

      .navbar img {
        margin: 0;
      }

      .container {
        margin: 20px 10px;
        padding: 20px;
      }

    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <div class="navbar">
    <a href="home.php" class="logo">
      <img src="images/logo2.png" alt="DCG Logo" />
    </a>
    <div class="right">
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php">Login</a>
      <a href="registration.php">Register</a>
    </div>
  </div>

  <!-- Contact Form -->
  <div class="container">
    
    <div class="contact-container">
      <h1>Contact Us</h1>
      <form method="post" action="send_mail.php" novalidate>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required autocomplete="name">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required autocomplete="email">

        <label for="subject">Subject</label>
        <input type="text" name="subject" id="subject" required autocomplete="off">

        <label for="message">Message</label>
        <textarea name="message" id="message" required></textarea>

        <button type="submit">Send Message</button>
      </form>
    </div>

  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
