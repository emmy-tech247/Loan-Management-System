<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'loan_system');
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Collect and sanitize inputs
    $full_name = trim($_POST['full_name']);
    $password_hash = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $state_of_origin = trim($_POST['state_of_origin']);
    $lga_of_origin = trim($_POST['lga_of_origin']);
    $permanent_address = trim($_POST['permanent_address']);
    $residential_address = trim($_POST['residential_address']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']) ; // ✅ prevents warning
    $bank_name = trim($_POST['bank_name']);
    $account_number = trim($_POST['account_number']);
    $account_name = trim($_POST['account_name']);
    $account_type = trim($_POST['account_type']);
    $place_of_work = trim($_POST['place_of_work']);
    $type_of_business_work = trim($_POST['type_of_business_work']);
    $monthly_earning = trim($_POST['monthly_earning']);
    $annual_income = trim($_POST['annual_income']);
    $expected_monthly_contribution_amount = (float)$_POST['expected_monthly_contribution_amount'];
    $fixed_deposit_amount = isset($_POST['fixed_deposit_amount']) ? (float)$_POST['fixed_deposit_amount'] : 0.00;
    $fixed_deposit_years = isset($_POST['fixed_deposit_years']) ? (int)$_POST['fixed_deposit_years'] : 0;
    $contribution_start_date = $_POST['contribution_start_date'];
    $full_name_of_next_of_kin = trim($_POST['full_name_of_next_of_kin']);
    $address_of_next_of_kin = trim($_POST['address_of_next_of_kin']);
    $phone_number_of_next_of_kin = trim($_POST['phone_number_of_next_of_kin']);
    $relationship_with_next_of_kin = trim($_POST['relationship_with_next_of_kin']);
    
    // System fields
    $payment_verified = 0;
    $otp_code = rand(100000, 999999);
    $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $payment_reference = '';
    $payment_receipt = '';

    // Admin flags
    $admin1_acknowledged = 0;
    $admin2_approved = 0;

    // File uploads
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    function uploadFile($inputName, $dir) {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        $filename = time() . "_" . basename($_FILES[$inputName]['name']);
        $target = $dir . $filename;
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $target)) {
            return $filename;
        }
        return '';
    }

    $id_document = uploadFile('id_document', $upload_dir);
    $passport = uploadFile('passport', $upload_dir);
    $payment_receipt = uploadFile('payment_receipt', $upload_dir);

    if (empty($payment_receipt)) {
        die("❌ Please upload payment receipt as proof of payment.");
    }

    // ✅ Correct SQL (matching 35 placeholders with bind_param types)
    $stmt = $conn->prepare("
    INSERT INTO members (
        full_name, state_of_origin, lga_of_origin,
        permanent_address, residential_address, phone_number, email,
        bank_name, account_number, account_name, account_type, place_of_work,
        type_of_business_work, monthly_earning, annual_income,
        expected_monthly_contribution_amount, fixed_deposit_amount,
        fixed_deposit_years, contribution_start_date, full_name_of_next_of_kin,
        address_of_next_of_kin, phone_number_of_next_of_kin,
        relationship_with_next_of_kin, password_hash, id_document,
        passport, payment_verified, otp_code, otp_expires, payment_reference, 
        payment_receipt, admin1_acknowledged, admin2_approved
    ) VALUES (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
    )
");


    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // ✅ Fixed bind_param types (35 total params)
    $stmt->bind_param(
        'ssssssssssssssddiisssssssssisissi',
        $full_name, $state_of_origin, $lga_of_origin,
        $permanent_address, $residential_address, $phone_number, $email,
        $bank_name, $account_number, $account_name, $account_type, $place_of_work,
        $type_of_business_work, $monthly_earning, $annual_income,
        $expected_monthly_contribution_amount, $fixed_deposit_amount,
        $fixed_deposit_years, $contribution_start_date, $full_name_of_next_of_kin,
        $address_of_next_of_kin, $phone_number_of_next_of_kin,
        $relationship_with_next_of_kin, $password_hash, $id_document,
        $passport, $payment_verified, $otp_code, $otp_expires, $payment_reference,
        $payment_receipt, $admin1_acknowledged, $admin2_approved
    );


    if ($stmt->execute()) {
        echo "✅ Registration submitted successfully! Awaiting Admin approvals before login is allowed.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- FORM SECTION OMITTED FOR BREVITY -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register with Payment</title>
  <script src="https://js.paystack.co/v1/inline.js"></script>
  
</head>
<body>
    <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      padding: 20px;
    }
    button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    .form-container {
      max-width: 800px;
      margin: auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #2c3e50;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
    }

    input, select, textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    .radio-group, .radio-options {
      display: flex;
      flex-direction: row;
      gap: 15px;
      margin-top: 5px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .form-row .form-group {
      flex: 1;
      min-width: 250px;
    }

    button {
      padding: 12px 25px;
      font-size: 16px;
      border: none;
      background-color: #3498db;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 20px;
    }

    button:hover {
      background-color: #2980b9;
    }

    .full-name-box {
  margin-bottom: 20px;
}

.full-name-box label {
  font-weight: bold;
  margin-bottom: 8px;
  display: block;
}

.full-name-box input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
}

  </style>

<h2>Register (₦1,000 Payment Required)</h2>



<div class="form-container">
  <h2>Member Registration Form</h2>
  <form action="registration.php" method="POST" enctype="multipart/form-data">

    <div class="form-row full-name-box">
      <label for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" placeholder="      Surname                                  First Name                                  Other Names             " required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input type="text" name="password">
    </div>
    

    <div class="form-row">
      <div class="form-group">
        <label>State of Origin</label>
        <input type="text" name="state_of_origin" required>
      </div>
      <div class="form-group">
        <label>LGA of Origin</label>
        <input type="text" name="lga_of_origin" required>
      </div>
    </div>

    <div class="form-group">
      <label>Permanent Address</label>
      <textarea name="permanent_address" rows="2" required></textarea>
    </div>

    <div class="form-group">
      <label>Residential Address</label>
      <textarea name="residential_address" rows="2" required></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone_number" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Bank Name</label>
        <input type="text" name="bank_name" required>
      </div>
      <div class="form-group">
        <label>Account Number</label>
        <input type="text" name="account_number" required>
      </div>
      <div class="form-group">
        <label>Account Name</label>
        <input type="text" name="account_name" required>
      </div>
    </div>

    <div class="form-group">
      <label>Account Type</label>
      <div class="radio-options">
        <label><input type="radio" name="account_type" value="Current" required> Current</label>
        <label><input type="radio" name="account_type" value="Savings"> Savings</label>
        <label><input type="radio" name="account_type" value="Other"> Other</label>
      </div><br>
      <div class="form-group">
        <label>Upload ID</label>
        <input type="file" name="id_document" required>
      </div>
      <div class="form-group">
        <label>Passport</label>
        <input type="file" name="passport" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Place of Work</label>
        <input type="text" name="place_of_work" required>
      </div>
      <div class="form-group">
        <label>Type of Business/Work</label>
        <input type="text" name="type_of_business_work" required>
      </div>
    </div>

    <div class="form-group">
      <label>Monthly Earnings</label>
      <select name="monthly_earning" required>
        <option value="">-- Select --</option>
        <option value="100k_below">N100k and Below</option>
        <option value="100k_200k">Above N100k - N150k</option>
        <option value="200k_250k">Above N150k - N200k</option>
        <option value="200k_250k">Above N200k - N250k</option>
        <option value="200k_250k">Above N250k - N300k</option>
        <option value="200k_250k">Above N300k - N350k</option>
        <option value="200k_250k">N400k and Above</option>
      </select>
    </div>

    <div class="form-group">
      <label>Annual Income</label>
      <div class="radio-options">
        <label><input type="radio" name="annual_income" value="below_500k" required> N500k and Below</label>
        <label><input type="radio" name="annual_income" value="500k_1m"> Above N500k - N1M</label>
        <label><input type="radio" name="annual_income" value="1m_2m"> Above N1M - N2M</label>
        <label><input type="radio" name="annual_income" value="2m_3m"> Above N2M - N3M</label>
        <label><input type="radio" name="annual_income" value="3m_4m"> Above N3M - N4M</label>
        <label><input type="radio" name="annual_income" value="4m_5m"> Above N1M - N2M</label>
        <label><input type="radio" name="annual_income" value="above_5m"> Above N5M</label>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Expected Monthly Contribution Amount</label>
        <input type="number" name="expected_monthly_contribution_amount" required>
      </div>
      <div class="form-group">
        <label>Fixed Deposit Amount (If Any)</label>
        <input type="number" name="fixed_deposit_amount">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Number of Years of Fixed Deposit (If Any)</label>
        <input type="number" name="fixed_deposit_years">
      </div>
      <div class="form-group">
        <label>Start Date for Monthly Contribution</label>
        <input type="date" name="contribution_start_date" required>
      </div>
    </div>

    <h3>Next of Kin Details</h3>

    <div class="form-group">
      <label>Full Name of Next of Kin</label>
      <input type="text" name="full_name_of_next_of_kin" required>
    </div>

    <div class="form-group">
      <label>Address of Next of Kin</label>
      <textarea name="address_of_next_of_kin" rows="2" required></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Phone Number of Next of Kin</label>
        <input type="tel" name="phone_number_of_next_of_kin" required>
      </div>
      <div class="form-group">
        <label>Relationship with Next of Kin</label>
        <input type="text" name="relationship_with_next_of_kin" required>
      </div>
    </div>

  
 <div class="form-group">
  <label>Upload Payment Receipt (Proof of ₦1,000 Payment)</label>
  <input type="file" name="payment_receipt" required>
</div>


  <button type="submit" id="submitBtn" disabled  style="margin: 10px  50px 10px 320px;">Submit Registration</button>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const receiptInput = document.querySelector('input[name="payment_receipt"]');
    const submitBtn = document.getElementById('submitBtn');

    receiptInput.addEventListener('change', function () {
      if (receiptInput.files.length > 0) {
        submitBtn.disabled = false;
      } else {
        submitBtn.disabled = true;
      }
    });
  });
</script>


</body>
</html>
