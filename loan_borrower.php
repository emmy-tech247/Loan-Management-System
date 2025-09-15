<?php
// loan_saver.php
session_start();
require_once "db.php";

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    die("Unauthorized: Please log in to apply for a loan.");
}

$member_id = (int) $_SESSION['member_id']; // ✅ Fix: set member_id from session

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $loan_amount    = isset($_POST['loan_amount']) ? (float)$_POST['loan_amount'] : 0;
    $tenure_month   = isset($_POST['tenure_month']) ? (int)$_POST['tenure_month'] : 0;
    $interest_rate  = isset($_POST['interest_rate']) ? (float)$_POST['interest_rate'] : 0;
    $facility_type  = isset($_POST['facility_type']) ? trim($_POST['facility_type']) : "";

    // BVN validation
    $bvn = $_POST['bvn'] ?? '';
    if (empty($bvn)) {
        die("BVN is required.");
    }
    if (!preg_match("/^[0-9]{11}$/", $bvn)) {
        die("Invalid BVN. It must be exactly 11 digits.");
    }

    // Guarantor 1
    $guarantor1_name    = trim($_POST['guarantor1_name'] ?? "");
    $guarantor1_phone   = trim($_POST['guarantor1_phone'] ?? "");
    $guarantor1_email   = trim($_POST['guarantor1_email'] ?? "");
    $guarantor1_address = trim($_POST['guarantor1_address'] ?? "");

    // Guarantor 2
    $guarantor2_name    = trim($_POST['guarantor2_name'] ?? "");
    $guarantor2_phone   = trim($_POST['guarantor2_phone'] ?? "");
    $guarantor2_email   = trim($_POST['guarantor2_email'] ?? "");
    $guarantor2_address = trim($_POST['guarantor2_address'] ?? "");

    // Compute repayment
    $total_repayment = 0;
    if ($loan_amount > 0 && $interest_rate > 0 && $tenure_month > 0) {
        $total_repayment = $loan_amount + (($loan_amount * $interest_rate * $tenure_month) / 100);
    }

    // File upload (receipt)
    $receipt_path = null;
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES['receipt']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $target_file)) {
            $receipt_path = "uploads/" . $file_name;
        }
    }

    // Insert loan record
    $sql = "INSERT INTO loans 
        (member_id, loan_amount, tenure_month, interest_rate, facility_type, total_loan, total_repayment, receipt_path,
         guarantor1_name, guarantor1_phone, guarantor1_email, guarantor1_address,
         guarantor2_name, guarantor2_phone, guarantor2_email, guarantor2_address,
         bvn, loan_status, assigned_to, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', 'relationship_officer', NOW())";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param(
        "iidssdsdsssssssss",   // 17 params
        $member_id,           // ✅ Now always set
        $loan_amount,
        $tenure_month,
        $interest_rate,
        $facility_type,
        $loan_amount,         // total_loan (could compute separately if needed)
        $total_repayment,
        $receipt_path,
        $guarantor1_name,
        $guarantor1_phone,
        $guarantor1_email,
        $guarantor1_address,
        $guarantor2_name,
        $guarantor2_phone,
        $guarantor2_email,
        $guarantor2_address,
        $bvn
    );

    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Loan application submitted successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Loan Application Form</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="robots" content="noindex, nofollow">

  <!-- Lightweight CSS (minified inline for fast loading) -->
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fa;margin:0;padding:20px;}
    .form-container{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.1);max-width:900px;margin:auto}
    h2{text-align:center;color:#003d6a;margin-bottom:10px}
    h3{color:#003d6a;margin-top:20px}
    label{font-weight:bold;display:block;margin-top:12px}
    input,select,textarea{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;font-size:14px}
    input[readonly]{background:#f1f1f1}
    .row{display:flex;flex-wrap:wrap;gap:15px}
    .row>div{flex:1;min-width:200px}
    button{background:#005b9a;color:#fff;padding:12px;border:none;border-radius:8px;font-size:16px;font-weight:bold;cursor:pointer;margin-top:20px;transition:.3s}
    button:disabled{background:#999;cursor:not-allowed}
    button:hover:enabled{background:#007bff}
    table{width:100%;border-collapse:collapse;margin-top:30px;font-size:14px}
    table,th,td{border:1px solid #ccc}
    th,td{padding:8px;text-align:center}
    th{background:#003d6a;color:#fff}
    p1{
      text-align:center;
    }
    @media(max-width:768px){.row{flex-direction:column}}

    /* Container */
/* Shared styles for borrower and guarantor */
.borrower-box,
.guarantor-box {
  display: flex;
  align-items: center;
  background: #f9f9f9;      /* Light background */
  border: 1px solid #ddd;   /* Subtle border */
  padding: 12px 16px;
  border-radius: 8px;       /* Rounded corners */
  margin: 10px 0;
  transition: 0.3s;
}

/* Hover effect for both */
.borrower-box:hover,
.guarantor-box:hover {
  background: #eef6ff;      /* Light blue on hover */
  border-color: #3b82f6;    /* Blue border */
}

/* Checkbox inside both */
.borrower-box input[type="checkbox"],
.guarantor-box input[type="checkbox"] {
  accent-color: #3b82f6;    /* Modern browsers: blue tick */
  width: 18px;
  height: 18px;
  cursor: pointer;
  margin-right: 10px;
}

/* Label inside both */
.borrower-box label,
.guarantor-box label {
  font-size: 15px;
  color: #333;
  cursor: pointer;
}


/* Hover effect */
.guarantor-box:hover {
  background: #eef6ff;      /* Light blue on hover */
  border-color: #3b82f6;    /* Blue border */
}

/* Checkbox */
.guarantor-box input[type="checkbox"] {
  accent-color: #3b82f6;    /* Modern browsers: blue tick */
  width: 18px;
  height: 18px;
  cursor: pointer;
  margin-right: 10px;
}

/* Label */
.guarantor-box label {
  font-size: 15px;
  color: #333;
  cursor: pointer;
}


    .navbar {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 10px 20px;
    }

    .navbar .left, .navbar .right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      text-align: center;
      padding: 16px 12px;
      text-decoration: none;
    }

    .navbar a:hover:not(:first-child) {
      background-color: #007bff;
      border-radius: 5px;
    }


  </style>
</head>
<body>


<div class="navbar">
  <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style="padding: 0; margin: -30px -30px -10px -30px;"></a>
  <div class="right">
    <a href="member.php">Back</a>
    <a href="loan_saver_borrower.php">Loan Application Form</a>
    <a href="loan_status.php">Status Tracking</a>
    <a href="upload_loan_repayment.php">Loan Repayment</a>
    <a href="view_loan_repayment.php">Loan Repayment Status</a>
    <a href="loan_calculator.html">Loan Calculator</a>
  </div>
</div>

    <h2>Loan Fee For A Borrower (₦2,000 Payment Required)</h2>
    <p style="text-align: center;">Repayment Account: DCG MULTIPURPOSE COOP. SOC. LTD: 0259738951, BANK: NIRSAL MFB  </p>
  <div class="form-container">
    <h2>Loan Application Form </h2>
    <form method="post" action="loan_saver.php" enctype="multipart/form-data" autocomplete="off">
      

      <!-- Borrower Information -->
       <h3>Borrower</h3>
      <label for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" required>

      <div class="row">
        <div>
          <label for="phone_number">Phone No</label>
          <input type="tel" id="phone_number" name="phone_number" pattern="[0-9+ ]{7,15}" required>
        </div>
        <div>
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>
        
      </div>

      <label for="permanent_address">Contact/Office Address</label>
      <input type="text" id="permanent_address" name="permanent_address" required>
      <div class="row">
          <div class="borrower-box">
            <input type="checkbox" id="borrower_confirm" name="borrower_confirm" required>
            <label for="borrower_confirm"> I, the borrower, confirm the details above</label>
          </div>
        </div><br><br>


      <!-- Guarantors -->
      <h3>First Guarantor</h3>
      <label for="guarantor1_name">Name</label>
      <input type="text" id="guarantor1_name" name="guarantor1_name" required>
      <div class="row">
        <div>
          <label for="guarantor1_phone">Phone No</label>
          <input type="tel" id="guarantor1_phone" name="guarantor1_phone" required>
        </div>
        <div>
          <label for="guarantor1_email">Email</label>
          <input type="email" id="guarantor1_email" name="guarantor1_email" >
        </div>
      </div>
      <label for="guarantor1_address">Address</label>
      <input type="text" id="guarantor1_address" name="guarantor1_address" required>
      <div class="row">
        <div class="guarantor-box">
            <input type="checkbox" id="guarantor1_confirm" name="guarantor1_confirm" required>
            <label for="guarantor1_confirm">First Guarantor agrees to guarantee</label>
        </div>

      </div><br><br>

      <h3>Second Guarantor</h3>
      <label for="guarantor2_name">Name</label>
      <input type="text" id="guarantor2_name" name="guarantor2_name" required>
      <div class="row">
        <div>
          <label for="guarantor2_phone">Phone No</label>
          <input type="tel" id="guarantor2_phone" name="guarantor2_phone" required>
        </div>
        <div>
          <label for="guarantor2_email">Email</label>
          <input type="email" id="guarantor2_email" name="guarantor2_email">
        </div>
      </div>
      <label for="guarantor2_address">Address</label>
      <input type="text" id="guarantor2_address" name="guarantor2_address" required>
      <div class="row">
        <div class="guarantor-box">
            <input type="checkbox" id="guarantor2_confirm" name="guarantor2_confirm" required>
            <label for="guarantor2_confirm">Second Guarantor agrees to guarantee</label>
        </div>
      </div><br><br>

      <!-- Loan Details -->
      <h3>Loan Details</h3>
      
      <label>Type of Facility</label>
      <select name="loan_type" required>
        <option value="personal">Personal</option>
        <option value="business">Business</option>
        <option value="education">Education</option>
      </select>

      <label for="purpose">Purpose</label>
      <textarea id="purpose" name="purpose" required></textarea>

      <div class="row">
        <div>
          <label for="loan_amount">Amount (₦)</label>
          <input type="number" id="loan_amount" name="loan_amount" required min="1000">
        </div>
        <div>
          <label for="interest_rate">Interest Rate (%)</label>
          <input type="number" id="interest_rate" name="interest_rate" step="0.01" min="0" required>
        </div>
      </div>

      <label for="tenure_month">Loan Tenure (Months)</label>
      <select id="tenure_month" name="tenure_month" required>
        <option value="">-- Select Tenure --</option>
        <option value="1">1 Month</option>
        <option value="2">2 Months</option>
        <option value="3">3 Months</option>
        <option value="4">4 Months</option>
        <option value="5">5 Months</option>
        <option value="6">6 Months</option>
      </select>

      <div class="row">
        <div>
          <label for="start_date">Loan Start Date</label>
          <input type="date" id="start_date" name="start_date" required>
        </div>
        <div>
          <label for="end_date">Loan End Date</label>
          <input type="text" id="end_date" name="end_date" readonly>
        </div>
      </div><br><br>

      <!-- Receipt Upload -->
      <h3>Proof of Payment (₦2,000 Payment)</h3>
      <label for="receipt">Upload Receipt of Payment (PDF/JPG/PNG, max 2MB)</label>
      <input type="file" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png" required><br><br>

      <!-- Loan Summary -->
      <h3>Loan Summary</h3>
      <div class="row">
        <div>
          <label>Total Loan (₦)</label>
          <input type="text" id="total_loan" name="total_loan" readonly>
        </div>
        <div>
          <label>Total Repayment (₦)</label>
          <input type="text" id="total_repayment" name="total_repayment" readonly>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Monthly Repayment (₦)</label>
          <input type="text" id="monthly_repayment" name="monthly_repayment" readonly>
        </div>
        <div>
          <label>Monthly Repayment Date</label>
          <input type="text" name="repayment_date">
        </div>
      </div><br><br>

      <!-- Repayment Details -->
      <h3>Repayment Details</h3>
      <label for="repayment_source">Repayment Source:</label>
      <select id="repayment_source" name="repayment_source" required>
          <option value="" disabled selected>-- Select Source --</option>
          <option value="Salary">Salary</option>
          <option value="Business">Business</option>
          <option value="Others">Others</option>
      </select>


      <div class="row">
        <div>
          <label for="bank_name">Bank</label>
          <input type="text" id="bank_name" name="bank_name" required>
        </div>
        <div>
          <label for="account_number">Account Number</label>
          <input type="text" id="account_number" name="account_number" pattern="[0-9]{10}" maxlength="10" required>
        </div>
      </div>

      <label for="account_name">Account Name</label>
      <input type="text" id="account_name" name="account_name" required><br><br>

      <div class="form-group">
        <label for="bvn">Bank Verification Number (BVN)</label>
        <input type="text" id="bvn" name="bvn" 
           placeholder="Enter 11-digit BVN" 
           maxlength="11" pattern="\d{11}" required>
      </div>



      <button type="submit" id="submitBtn" disabled>Submit Loan Application</button>
    </form>

    <!-- Amortization Schedule -->
    <h3>Amortization Schedule</h3>
    <table id="scheduleTable" style="display:none;">
      <thead>
        <tr>
          <th>Month</th>
          <th>EMI (₦)</th>
          <th>Principal (₦)</th>
          <th>Interest (₦)</th>
          <th>Balance (₦)</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <form id="exportForm" method="post" action="export_pdf.php" target="_blank" style="display:none; margin-top:20px;">
      <input type="hidden" name="scheduleData" id="scheduleData">
      <button type="submit">Export to PDF</button>
    </form>
  </div>

  <!-- Minified JS for speed -->
 <script>
    const loanAmount=document.getElementById("loan_amount"),
          interestRate=document.getElementById("interest_rate"),
          tenure=document.getElementById("tenure_month"),
          startDate=document.getElementById("start_date"),
          endDate=document.getElementById("end_date"),
          totalLoan=document.getElementById("total_loan"),
          totalRepayment=document.getElementById("total_repayment"),
          monthlyRepayment=document.getElementById("monthly_repayment"),
          scheduleTable=document.getElementById("scheduleTable").getElementsByTagName("tbody")[0],
          scheduleContainer=document.getElementById("scheduleTable"),
          exportForm=document.getElementById("exportForm"),
          scheduleDataInput=document.getElementById("scheduleData"),
          receiptInput=document.getElementById("receipt"),
          submitBtn=document.getElementById("submitBtn");

    receiptInput.addEventListener("change",()=>{submitBtn.disabled=!(receiptInput.files.length>0)});

    function calculateLoanDetails(){
      const amount=parseFloat(loanAmount.value),
            monthlyRate=parseFloat(interestRate.value)/100, // 🔹 per month directly
            months=parseInt(tenure.value),
            start=new Date(startDate.value);

      scheduleTable.innerHTML="";
      scheduleContainer.style.display="none";
      exportForm.style.display="none";

      if(!isNaN(amount)&&!isNaN(monthlyRate)&&!isNaN(months)&&months>0){
        // EMI formula using monthly rate directly
        const r=monthlyRate,n=months;
        const emi=(amount*r*Math.pow(1+r,n))/(Math.pow(1+r,n)-1);
        const total=emi*n;

        totalLoan.value=amount.toFixed(2);
        totalRepayment.value=total.toFixed(2);
        monthlyRepayment.value=emi.toFixed(2);

        let balance=amount,scheduleData=[];
        scheduleContainer.style.display="table";
        exportForm.style.display="block";

        for(let i=1;i<=n;i++){
          let interestPayment=balance*r;
          let principalPayment=emi-interestPayment;
          balance-=principalPayment;
          if(balance<0) balance=0;

          let row=scheduleTable.insertRow();
          row.insertCell(0).innerText=i;
          row.insertCell(1).innerText=emi.toFixed(2);
          row.insertCell(2).innerText=principalPayment.toFixed(2);
          row.insertCell(3).innerText=interestPayment.toFixed(2);
          row.insertCell(4).innerText=balance.toFixed(2);

          scheduleData.push({
            month:i,
            emi:emi.toFixed(2),
            principal:principalPayment.toFixed(2),
            interest:interestPayment.toFixed(2),
            balance:balance.toFixed(2)
          });
        }
        scheduleDataInput.value=JSON.stringify(scheduleData);
      }

      if(!isNaN(start.getTime())&&!isNaN(months)){
        const end=new Date(start);
        end.setMonth(end.getMonth()+months);
        endDate.value=end.toISOString().split("T")[0];
      }
    }

    loanAmount.addEventListener("input",calculateLoanDetails);
    interestRate.addEventListener("input",calculateLoanDetails);
    tenure.addEventListener("change",calculateLoanDetails);
    startDate.addEventListener("change",calculateLoanDetails);
</script>

</body>
</html>
