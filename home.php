<?php 
  session_start();
  include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DCG Cooperative | Home</title>

  <style>
    /* Reset */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, sans-serif;
      background-color: #f9fbfd;
      color: #333;
      line-height: 1.6;
    }

    /* Navbar */
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
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 24px 25px;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
    }

    /* Remove hover from logo */
    .navbar .left a:hover {
      background-color: transparent;
    }

    /* Hover effect only for nav links (not logo) */
    .navbar .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    /* Top header */
    .top-header {
      background-color: #fff;
      color: #0056b3;
      padding: 20px;
      text-align: center;
      position: relative;
    }
    .badge {
      position: absolute;
      top: 10px;
      left: 10px;
      font-size: 12px;
      background: #74b9ff;
      color: #003d6a;
      padding: 5px 10px;
      border-radius: 4px;
      font-weight: bold;
    }

    /* Services */
    .services-section {
      text-align: center;
      padding: 40px 20px;
      background-color: #f9fbfd;
      color: #0056b3;
    }
    .services-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      margin-top: 30px;
    }
    .service-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      width: 220px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    .service-card:hover { transform: translateY(-5px); }
    .service-card img {
      width: 200px;
      height: auto;
      max-height: 180px;
      object-fit: contain;
      margin-bottom: 10px;
      border-radius: 10px;
    }
    .service-card h3 {
      font-size: 16px;
      color: #004080;
    }

    /* Deposit & Savings */
    .container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: space-between;
      max-width: 1000px;
      margin: 40px auto;
    }
    .column {
      flex: 1 1 48%;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .fixed-deposit h3,
    .monthly-savings h3 {
      color: #fff;
      margin-bottom: 20px;
      background: #007bff;
      padding: 10px;
      border-radius: 5px;
      text-transform: uppercase;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }
    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }
    table th { background-color: #f1f1f1; }
    .monthly-savings p {
      font-size: 16px;
      color: #333;
    }

    /* Borrow Rates Card */
    .card {
      width: 100%;
      max-width: 1000px;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(12,34,64,0.08);
      padding: 28px;
      margin: 40px auto;
    }
    .card header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    .card header h1 {
      font-size: 20px;
      color: #fff;
      margin-bottom: 20px;
      background: #007bff; 
      margin: 0;
    }
    .card header .sub {
      font-size: 13px;
      color: #475569;
    }
    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(3,43,86,0.06), transparent);
      margin: 18px 0;
    }
    .table-wrap { overflow-x: auto; }
    table.rates {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    table.rates thead th {
      text-align: left;
      padding: 12px 14px;
      background: linear-gradient(180deg,#f3f8ff,#eef7ff);
      color: #03305a;
      font-weight: 700;
      border-bottom: 1px solid #e6eefc;
    }
    table.rates tbody td {
      padding: 12px 14px;
      border-bottom: 1px dashed #eef4fb;
    }
    table.rates tbody tr:nth-child(even) { background: #fbfdff; }
    table.rates tbody tr:hover { background: #f1f8ff; }
    .note {
      margin-top: 18px;
      background: #fbfbff;
      border-left: 4px solid #004080;
      padding: 12px 14px;
      color: #213447;
      border-radius: 6px;
      font-size: 13px;
    }
    .contact {
      margin-top: 18px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }
    .contact .name { font-weight: 700; color: #032b56; }
    .contact .phone { color: #064274; font-weight: 600; }

    /* Contact Section */
    .contact-section {
      background-color: #003d6a;
      color: white;
      text-align: center;
      padding: 30px 20px;
      margin-top: 40px;
    }
    .contact-section h2 { margin-bottom: 20px; }
    .contact-section ul {
      list-style: none;
      padding: 0;
      font-size: 16px;
    }
    .contact-section a { color: #fff; text-decoration: underline; }
    footer { margin-top: 20px; font-size: 14px; }

    table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 15px;
    font-family: "Segoe UI", Arial, sans-serif;
  }

  th, td {
    border: 1px solid #e5e7eb;
    padding: 12px;
    text-align: center;
  }

  th {
    background: #0f172a;
   
    font-size: 15px;
  }

  /* Modern Button Styling */
  .calc-btn {
    background: linear-gradient(135deg, #007bff, #74b9ff);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }

  .calc-btn:hover {
    background: linear-gradient(135deg, #007bff, #74b9ff);
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(0,0,0,0.15);
  }

  .calc-btn:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

    
    
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="left">
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80" style="margin:-50px -50px -20px -50px;">
      </a>
    </div>
    <div class="right">
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact Us</a>
      <a href="login.php">Login</a>
      <a href="registration.php">Register</a>
      <a href="staff.php"> </a>
    </div>
  </div>

  <!-- Top Header -->
  <header class="top-header">
    <div class="badge">FCTA/ARDS/COOP/2019/3185</div>
    <h1>DCG MULTI-PURPOSE COOPERATIVE SOCIETY LIMITED</h1>
  </header>

  <!-- Services -->
  <section class="services-section">
    <h2>OUR SERVICES:</h2>
    <div class="services-grid">
      <div class="service-card">
        <img src="images/dcg_credit.png" alt="Credit Facilities">
        <h3>Credit Facilities</h3>
      </div>
      <div class="service-card">
        <img src="images/dcg_estate.png" alt="Real Estate">
        <h3>Real Estate</h3>
      </div>
      <div class="service-card">
        <img src="images/dcg_finance.png" alt="Financial Mentoring">
        <h3>Financial Mentoring</h3>
      </div>
      <div class="service-card">
        <img src="images/dcg_farming.png" alt="Fish Farming">
        <h3>Fish Farming</h3>
      </div>
    </div>
  </section>

 <!-- Fixed Deposit and Monthly Savings -->
<div class="container">
  <div class="column fixed-deposit">
    <h3>Fixed Deposit Dividends</h3>
    <table border="1" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Investment Range</th>
          <th>1 Year</th>
          <th>2 Years</th>
          <th>3 Years</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>₦1M and below Investment</td>
          <td data-rate="20">20%</td>
          <td data-rate="45">45%</td>
          <td data-rate="70">70%</td>
          <td><button class="calc-btn" onclick="showEarnings(this)">Check Earnings</button></td>
        </tr>
        <tr>
          <td>Above ₦1M to ₦5M Investment</td>
          <td data-rate="22">22%</td>
          <td data-rate="50">50%</td>
          <td data-rate="80">80%</td>
          <td><button class="calc-btn" onclick="showEarnings(this)">Check Earnings</button></td>
        </tr>
        <tr>
          <td>Above ₦5M to ₦10M Investment</td>
          <td data-rate="24">24%</td>
          <td data-rate="55">55%</td>
          <td data-rate="90">90%</td>
          <td><button class="calc-btn" onclick="showEarnings(this)">Check Earnings</button></td>
        </tr>
        <tr>
          <td>Above ₦10M Investment</td>
          <td data-rate="27">27%</td>
          <td data-rate="60">60%</td>
          <td data-rate="100">100%</td>
          <td><button class="calc-btn" onclick="showEarnings(this)">Check Earnings</button></td>
        </tr>
      </tbody>
    </table>
  </div>

 <script>
function showEarnings(button) {
  let row = button.closest("tr");
  let cells = row.querySelectorAll("td[data-rate]");
  
  let amount = parseFloat(prompt("Enter your investment amount in ₦:"));
  if (isNaN(amount) || amount <= 0) {
    alert("Invalid amount entered.");
    return;
  }

  let message = "💰 Estimated Earnings:\n\n";
  
  cells.forEach((cell) => {
    let rate = parseFloat(cell.getAttribute("data-rate"));
    let yearlyReturn = (rate / 100) * amount;
    let monthlyReturn = yearlyReturn / 12;

    message += `${cell.innerText}\n`;
    message += `   → ₦${monthlyReturn.toLocaleString(undefined, {maximumFractionDigits:2})} per month\n`;
    message += `   → ₦${yearlyReturn.toLocaleString(undefined, {maximumFractionDigits:2})} per year\n\n`;
  });

  alert(message);
}
</script>

</div>

    </div>
    
  </div>
  <div class="container">
    <div class="column monthly-savings">
      <h3>Monthly Savings</h3>
      <p>Join our monthly savings and earn <strong>100%</strong> of your monthly savings amount as dividends after 12 months.</p>
      <p><em>Example:</em> Save ₦50,000/month for 12 months and earn ₦50,000 dividends on your savings.</p>
    </div>
  </div>
  <div class="container">
    <div class="column monthly-savings">
      <h3>Borrow At The Following Rates Per Month</h3>
      <p>Rates apply relative to member's saved amount </p>
      <p> <small style="color:#6b7a8a">DCG Multipurpose Co-operative Society Ltd.</small></p>
    </div>
  </div>

  

  <!-- Borrow Rates -->

  <div class="card">
    <div class="divider"></div>
    <div class="table-wrap">
      <table class="rates">
        <thead>
          <tr><th>S/No</th><th>Amount (₦)</th><th>Rate</th></tr>
        </thead>
        <tbody>
          <tr><td>1</td><td>Amount less than your saved amount</td><td>2%</td></tr>
          <tr><td>2</td><td>Amount 2 times your saved amount</td><td>3%</td></tr>
          <tr><td>3</td><td>Amount 3 times your saved amount</td><td>4%</td></tr>
          <tr><td>4</td><td>Amount 4 times your saved amount</td><td>5%</td></tr>
          <tr><td>5</td><td>Amount 5  and above your saved amount</td><td>6%</td></tr>
        </tbody>
      </table>
    </div>
    <div class="note"><strong>Policy:</strong> Loans preserve capital; borrowers must service interest monthly while capital remains outstanding.</div>
    <div class="contact">
      <div>
        <div class="name">Anyam T. Daniel, Ph.D.</div>
        <div style="font-size:13px; color:#566676">Cooperative President</div><br><div class="phone">08162577877</div>
      </div>
      
    </div>
  </div>

  <!-- Contact -->
  <section class="contact-section">
    <h2>Contact Us</h2>
    <ul>
      <li>📞 +234 815 091 5060</li>
      <li>📞 +234 705 774 5805</li>
      <li>📧 <a href="mailto:dcgcoopltd@gmail.com">dcgcoopltd@gmail.com</a></li>
    </ul>
    <footer>
      <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
    </footer>
  </section>

</body>
</html>
