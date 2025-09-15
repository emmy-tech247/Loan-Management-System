<?php
session_start();
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - DCG Cooperative</title>
  <link rel="stylesheet" href="about.css">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      color: #222;
      line-height: 1.6;
    }
    .navbar {
      display: flex; justify-content: space-between; align-items: center;
      background-color: #004080; padding: 0 20px;
    }
    .navbar .left, .navbar .right { display: flex; align-items: center; }
    .navbar a, .dropbtn {
      font-size: 16px; color: white; text-align: center;
      padding: 20px 25px; text-decoration: none; background: none;
      border: none; cursor: pointer; transition: background 0.3s ease;
    }
    .navbar a:hover:not(.logo) {
      background-color: #007bff; padding: 10px 15px; border-radius: 5px;
    }
    header {
      background-color: #fff; color: #007bff;
      text-align: center; padding: 40px 20px;
    }
    .container { max-width: 960px; margin: 30px auto; padding: 0 20px; }
    .section {
      background-color: #fff; padding: 30px; margin-bottom: 30px;
      border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .section h2 { margin-top: 0; color: #007bff; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    table th, table td {
      border: 1px solid #ccc; padding: 12px; text-align: left;
    }
    table th { background-color: #e0ecf8; }
    ul { margin: 10px 0 0 20px; padding-left: 20px; }
    a { color: #007bff; text-decoration: none; }
    footer {
      text-align: center; padding: 20px; background-color: #004080; color: white;
    }
    .navbar img {
      display: block; margin: -50px -50px -20px -50px;
      width: 80px; height: 80px;
    }
    @media (max-width: 768px) {
      .navbar { flex-direction: column; align-items: flex-start; }
      .navbar .right { flex-direction: column; width: 100%; }
      .navbar a { padding: 15px; width: 100%; }
      header { padding: 20px 10px; }
      .container { padding: 0 10px; }
      .section { padding: 20px; }
      table th, table td { font-size: 14px; padding: 8px; }
    }

    
  </style>
</head>
<body>
  <div class="navbar">
    <div class="left">
      <a href="home.php" class="logo"><img src="images/logo2.png" alt="Logo"></a>
    </div>
    <div class="right">
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact Us</a>
      <a href="login.php">Login</a>
      <a href="registration.php">Register</a>
    </div>
  </div>

  <header>
    <h1>DCG Multi-Purpose Cooperative Society Ltd</h1>
    <p>Motto: "Prosperity Built on Integrity"</p>
  </header>

  <main class="container">

    <section class="section">
      <h2>About DCG Multi-purpose Cooperative Society Limited</h2>
      <p>DCG Multi-purpose Cooperative Society Limited, with the motto "Prosperity Built on Integrity," was established in 2022 as DCG Thrift and Loans Cooperative Society Limited. The organization's name was later changed to DCG Multi-purpose Cooperative Society Limited in 2023. We are registered with the Benue State Government and the Federal Capital Territory (FCT), Abuja.</p>
    </section>

    <section class="section">
      <h2>Our Offices</h2>
      <ul>
        <li><strong>Benue Office:</strong> B12, Suswam Plaza, New Garage, Wadata, Makurdi</li>
        <li><strong>Abuja Office:</strong> Plot A506U/A506W, Mpape II Layout, back of Crush Rock, Mpape</li>
      </ul>
    </section>

    <section class="section">
      <h2>Cooperative Formation & Leadership</h2>
      <p>The cooperative is founded by 10 serious minded personalities called initial founding members. <strong>Anyam T. Daniel Ph.D.</strong> is the President.</p>
      <p>We have two sets of registered members:</p>
      <ul>
        <li><strong>Investors:</strong> Those who invested their capital in the cooperative for monthly or yearly dividends. The investment can be a fixed deposit or monthly savings.</li>
        <li><strong>Borrowers:</strong> Those who registered with the cooperative for the purpose of borrowing.</li>
      </ul>
      <p><em>Note:</em> One can be both an investor and a borrower if such a person invests for dividends and also borrows from the cooperative.</p>
    </section>

    <section class="section">
      <h2>Cooperative Management</h2>
      <p>The cooperative is managed by the Credit and Investment Experts Team, composed of experienced financial analysts. It is headed by the President, <strong>Dr. Anyam Daniel Terkimbi</strong>, a Chartered Accountant and Financial Expert. He leads the team with 11 dedicated members.</p>
    </section>

    <section class="section">
      <h2>Services Offered</h2>
      <ul>
        <li>Financial services (loans, savings, and investments)</li>
        <li>Real Estate</li>
        <li>Agrobusiness</li>
        <li>Education</li>
        <li>Other lucrative and legal businesses</li>
        <li>Humanitarian services</li>
      </ul>
    </section>

    <section class="section">
      <h2>Investment & Dividends</h2>
      <table>
        <thead>
          <tr><th>Investment Range</th><th>1 Year</th><th>2 Years</th><th>3 Years</th></tr>
        </thead>
        <tbody>
          <tr><td>₦1M and below</td><td>20%</td><td>43%</td><td>70%</td></tr>
          <tr><td>₦1M – ₦5M</td><td>22%</td><td>50%</td><td>80%</td></tr>
          <tr><td>₦5M – ₦10M</td><td>24%</td><td>55%</td><td>90%</td></tr>
          <tr><td>Above ₦10M</td><td>27%</td><td>60%</td><td>100%</td></tr>
        </tbody>
      </table>
      <p>👉 Investors may also opt for <strong>monthly dividends at 1.5% per month</strong>. Example: ₦20M investment yields ₦300,000 monthly while keeping the capital of N20M intact.</p>
      <p><em>Note:</em> Once investors deposit money to the designated cooperative account, the President issues investment certificates signed solely by him.</p>
    </section>

    <section class="section">
      <h2>Monthly Savings Dividends</h2>
      <p>Our monthly savings plan offers <strong>100% dividends</strong> on your monthly savings amount after 12 months. For example, if you save ₦100,000 monthly for 12 months, you will receive ₦1,300,000 instead of ₦1,200,000.</p>
    </section>

    <section class="section">
      <h2>Borrowing Rates</h2>
      <ul>
        <li>Amount less than or equal to your Saved Amount: 2% per month</li>
        <li>Amount 2 times your saved Amount: 3% per month</li>
        <li>Amount 3 times your saved Amount: 4% per month</li>
        <li>Amount 4 times your Saved Amount: 5% per month</li>
        <li>Amount 5 times your Saved Amount: 6% per month</li>
        <li>Loan to keep captal and service interest every month until capital is refunded: 7% per month</li>
      </ul>
      <p>However, monthly savers who joined the monthly savings up to one year can be admitted into DCG Multipurpose Cooperative Society Foundation where they can borrow:<br>2 times savings at 12% per year.<br>3 times savings at 18% per year.<br>While their first year savings is generating a minimum of 20% per year.</p>
    </section>

    <section class="section">
      <h2>DCG Multipurpose Cooperative Society Foundation Launch</h2>
      <p>DCG Multipurpose Cooperative Society Foundation </strong> shall be launched on <strong>December 5, 2025</strong>. This milestone will mark a significant step in our mission to empower our members and create a supportive community.</p>
    </section>

    <section class="section">
      <h2>Benefits for Monthly Contributors</h2>
      <p>After reaching one year of savings with us, monthly contributors who choose to continue saving will enjoy the following benefits:</p>
      <ul>
        <li><strong>Competitive Dividend Rates</strong>: Keep earning a minimum of 20% per annum on their first-year savings, which will be converted to a fixed deposit.</li>
        <li><strong>Savings Growth</strong>: Earn 100% dividends on their monthly savings amount in a year.</li>
        <li><strong>Access to Loans</strong>: Borrow up to two times their total savings at 1% per month or three times their total savings at 1.5% per month.</li>
        <li><strong>Free Financial Coaching</strong>: Have free access to our financial coaching and consultation services.</li>
        <li><strong>Social Responsibility Benefits</strong>: Be opportune to benefit from the cooperative's social responsibility initiatives.</li>
      </ul>
    </section>

    <section class="section">
      <h2>Our Vision</h2>
      <p>The DCG Multipurpose Cooperative Society Foundation aims to create a supportive community that helps members build wealth and achieve their financial goals. We foster cooperation and mutual support, enabling members to thrive and live fulfilling lives.</p>
    </section>

    <section class="section">
      <h2>Join Our Community</h2>
      <p>By joining our community, you'll become part of a network of like-minded individuals who share a commitment to financial growth and well-being. We look forward to welcoming you to the DCG Multipurpose Cooperative Society Foundation and working together to achieve our goals.</p>
    </section>

    <section class="section">
      <h2>For inquiries, please contact:
</h2>
      <p><strong>Dr. Anyam Daniel Terkimbi</strong> (President): <a href="tel:08162577877">08162577877</a></p>
      <p><strong>Iorgbo Ortserga Raphael</strong> (PRO): <a href="tel:07060437732">07060437732</a></p>
    </section>

    <section class="section">
      <h2>Join Our WhatsApp Group</h2>
      <p>👉 <a href="https://chat.whatsapp.com/LyDTbDDVxhG6u8PAGev8Jy" target="_blank" rel="noopener noreferrer">Click to Join WhatsApp Group</a></p>
    </section>
  </main>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
