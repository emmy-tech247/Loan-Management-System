<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FAQs – Loan Management System</title>
  <link rel="stylesheet" href="css/style8.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
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

    .navbar a,
    .dropbtn {
      font-size: 16px;
      color: white;
      text-align: center;
      padding: 18px 25px;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
    }

 

    /* Regular hover for other links */
    .navbar a:not(.logo-link):hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .navbar img {
      display: block;
      margin: -50px -50px -20px -50px;
      padding: 0;
    }


    .faq-container {
      max-width: 900px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
    }

    .faq-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      font-size: 28px;
    }

    .faq {
      border-bottom: 1px solid #ddd;
      margin-bottom: 10px;
      transition: all 0.3s ease-in-out;
    }

    .question {
      padding: 15px;
      cursor: pointer;
      background: #f7f7f7;
      font-weight: bold;
      position: relative;
      font-size: 17px;
      transition: background-color 0.2s;
    }

    .question::after {
      content: '+';
      font-size: 18px;
      position: absolute;
      right: 20px;
      transition: transform 0.3s ease;
    }

    .faq.open .question::after {
      transform: rotate(45deg);
    }

    .answer {
      display: none;
      padding: 15px;
      background-color: #fff;
      color: #444;
      font-size: 16px;
      line-height: 1.6;
    }

    .faq.open .answer {
      display: block;
    }

    .question:hover {
      background-color: #eef1f5;
    }

    footer {
      text-align: center;
      padding: 25px;
      background-color: #004080;
      color: white;
      margin-top: 80px;
      font-size: 15px;
    }

    /* Responsive layout */
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 10px;
        font-size: 15px;
      }

      .faq-container {
        margin: 30px 15px;
        padding: 20px;
      }

      .faq-container h2 {
        font-size: 24px;
      }

      .question {
        font-size: 16px;
      }

      .answer {
        font-size: 15px;
      }
  .navbar .left a:hover,
.navbar .left a:hover img {
  background-color: transparent !important;
  padding: 0 !important;
  margin: -50px -50px -20px -50px !important;
  border-radius: 0 !important;
  cursor: pointer;
}


  </style>
</head>
<body>

  <div class="navbar">
    <a href="home.php" class="logo-link">
      <img src="images/logo2.png" alt="Logo" width="80" height="80">
    </a>

    <div class="right">
      <a href="member.php">Back</a>
      <a href="faq.php">FAQ</a>
      <a href="announcement.php">Announcements</a>
      <a href="member_chat_dashboard.php">Chat with Admin</a>
    </div>
  </div>

  <div class="faq-container">
    <h2>📘 Frequently Asked Questions</h2>

    <?php
    $faqs = [
      "What is a Loan Management System?" => "A platform that automates the process of applying, approving, disbursing, tracking, and repaying loans.",
      "How do I register on the platform?" => "Click the 'Register' button and complete your personal, financial, and contact details. ID verification may be required.",
      "What types of loans are available?" => "Personal loans, business loans, salary advances, and fixed deposit-backed loans.",
      "What documents are required to apply?" => "Valid ID, proof of income, bank statement, and passport photograph.",
      "How is loan eligibility determined?" => "Based on credit history, income, employment status, and existing obligations.",
      "Can I track loan application status?" => "Yes. Log in and go to “Loan Status” to view progress.",
      "How long does approval take?" => "24 to 72 hours after submission of required documents.",
      "What is the repayment period?" => "Ranges from 1 to 24 months depending on loan type.",
      "What happens if I miss a payment?" => "A late fee may be applied and it could affect your credit score or eligibility for future loans.",
      "Can I repay my loan before the due date?" => "Yes. Early repayment is allowed and may reduce the total interest payable, depending on the terms.",
      "How do I make repayments?" => "Repayments can be made via online payment gateways (e.g., Paystack), bank transfers, or direct debit from your account.",
      "Is there a penalty for early loan repayment?" => "Most of our loans do not carry an early repayment penalty, but always check your loan agreement.",
      "Can I have more than one active loan?" => "It depends on your repayment history and credit limit. Contact support for evaluation.",
      "How is the interest rate calculated?" => "Interest is calculated based on the loan amount, duration, and risk profile. Rates are displayed before application submission.",
      "How secure is my data?" => "We use SSL encryption, two-factor authentication (2FA), and role-based access control to protect your information.",
      "What is a fixed deposit-backed loan?" => "It’s a loan secured against your fixed deposit. You can borrow a percentage of your FD while it remains untouched.",
      "Can I pause or defer repayments?" => "In special cases (like medical emergencies), you may request a deferment, subject to admin approval.",
      "What is the maximum loan amount I can borrow?" => "The limit depends on your monthly income, deposit history, and credit score. It is shown on your dashboard.",
      "How can I contact support?" => "Use the 'Contact Us' form, live chat, or email at support@loanportal.com for assistance.",
      "How can I download my loan certificate or statement?" => "Log in to your account, navigate to the 'Documents' section, and download your loan certificate or repayment statement in PDF format.",
    ];

    foreach ($faqs as $question => $answer) {
      echo "<div class='faq'>
              <div class='question'>{$question}</div>
              <div class='answer'>{$answer}</div>
            </div>";
    }
    ?>
  </div>

  <script>
    document.querySelectorAll('.question').forEach(q => {
      q.addEventListener('click', () => {
        q.parentElement.classList.toggle('open');
      });
    });
  </script>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
