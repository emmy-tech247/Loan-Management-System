<?php
session_start();
require 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $loan_id = intval($_POST['loan_id']);
    $amount = floatval($_POST['amount']);

    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $file_type = mime_content_type($_FILES['receipt']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            $file_ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
            $safe_filename = uniqid('receipt_', true) . '.' . $file_ext;
            $upload_dir = 'uploads/';
            $file_path = $upload_dir . $safe_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare("INSERT INTO loan_repayments (member_id, loan_id, amount_paid, receipt_file) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iids", $member_id, $loan_id, $amount, $file_path);
                $stmt->execute();
                $success = "✅ Repayment submitted!";
            } else {
                $error = "❌ Failed to upload file.";
            }
        } else {
            $error = "❌ Invalid file type.";
        }
    } else {
        $error = "❌ No file uploaded or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Loan Repayment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f6fc;
      margin: 0;
      padding: 0 20px;
      color: #333;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004080;
      padding: 10px 25px;
      margin: 1px -15px;
      height: 40px;
    }

    .navbar .left,
    .navbar .right {
      display: flex;
      align-items: center;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 26px 25px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .navbar a:not(:has(img)):hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    h2 {
      text-align: center;
      margin: 30px 0 10px;
      font-size: 1.6rem;
      color: #2c3e50;
    }

    form {
      background-color: #ffffff;
      padding: 25px 30px;
      border-radius: 10px;
      max-width: 500px;
      margin: 30px auto;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    input[type="number"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccd6dd;
      border-radius: 5px;
      font-size: 15px;
    }

    button[type="submit"] {
      width: 100%;
      background-color: #004080;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #2980b9;
    }

    .success, .error {
      text-align: center;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .success {
      background-color: #dff0d8;
      color: #3c763d;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
    }

    footer {
      text-align: center;
      padding: 20px;
      background-color: #004080;
      color: white;
      margin: 110px -15px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        height: auto;
        padding: 10px;
      }

      .navbar a {
        padding: 10px;
        font-size: 14px;
      }

      h2 {
        font-size: 1.2rem;
      }
    }
  </style>
</head>

<body>
  <div class="navbar">
    <div class="left">
      <a href="home.php">
        <img src="images/logo2.png" alt="Logo" width="80" height="80" style="display:block; margin: -50px -50px -20px -50px;">
      </a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="loan_saver_borrower.php">Loan Application Form</a>
      <a href="loan_status.php">Status Tracking</a>
      <a href="upload_loan_repayment.php">Loan Repayment</a>
      <a href="view_loan_repayment.php">Loan Repayment Status</a>
      <a href="loan_report.php">Loan Report </a>
      <a href="loan_calculator.html">Loan Calculator</a>
    </div>
  </div>

  <?php if (isset($success)): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php elseif (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <h2>Loan Repayment</h2>
    <input type="hidden" name="loan_id" value="1">

    <label for="amount">Amount:</label>
    <input type="number" name="amount" id="amount" required min="0.01" step="0.01">

    <label for="receipt">Upload Receipt:</label>
    <input type="file" name="receipt" id="receipt" required accept=".jpg,.jpeg,.png,.pdf">

    <button type="submit">Submit Repayment</button>
  </form>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>
</body>
</html>
