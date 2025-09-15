<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized");
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $amount_saved = floatval($_POST['amount_saved']);
    $reference = uniqid('TXN_', true);
    $receipt = $_FILES['receipt'];

    $uploadDir = 'uploads/receipts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($receipt['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($fileExt, $allowed)) {
        $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        $filePath = $uploadDir . $safeFileName;

        if (move_uploaded_file($receipt['tmp_name'], $filePath)) {
            $stmt = $conn->prepare("INSERT INTO savings_transactions (member_id, amount_saved, reference, receipt_file, type, status) VALUES (?, ?, ?, ?, 'deposit', 'pending')");
            $stmt->bind_param("idss", $member_id, $amount_saved, $reference, $filePath);
            $stmt->execute();
            $message = "✅ Uploaded. Awaiting admin acknowledgment.";
            $success = true;
        } else {
            $message = "❌ Upload failed.";
        }
    } else {
        $message = "❌ Invalid file type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upload Savings Receipt</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
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

    .navbar .left a {
      padding: 0;
      margin: 0;
    }

    .navbar .left a:hover {
      background: none !important;
      padding: 0 !important;
      border-radius: 0 !important;
    }

    .navbar a {
      font-size: 16px;
      color: white;
      padding: 18px 25px;
      text-decoration: none;
      transition: background-color 0.3s ease, padding 0.3s ease;
    }

    .right a:hover {
      background-color: #007bff;
      padding: 10px 15px;
      border-radius: 5px;
    }

    h2 {
      text-align: center;
      margin-top: 40px;
      color: #333;
    }

    form {
      background: white;
      max-width: 400px;
      margin: 30px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      font-weight: bold;
      margin-bottom: 8px;
    }

    input[type="number"],
    input[type="file"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #004080;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #007bff;
    }

    .message {
      text-align: center;
      margin-top: 20px;
      font-weight: bold;
    }

    .message.success {
      color: green;
    }

    .message.error {
      color: red;
    }

    footer {
      text-align: center;
      padding: 30px;
      background-color: #004080;
      color: white;
      margin-top: 120px;
    }

    @media (max-width: 768px) {
      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar a {
        padding: 12px 20px;
      }

      form {
        margin: 20px;
        padding: 20px;
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
      <a href="home.php"><img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; padding: 0; margin: -50px -50px -20px -50px;"></a>
    </div>
    <div class="right">
      <a href="member.php">Back</a>
      <a href="set_saving.html">Monthly Savings<br> Amount</a>
      <a href="savings_transactions.php">Transaction Alerts<br>and Summaries</a>
      <a href="upload_topups_receipt.php">Savings</a>
      <a href="withdraw_funds.php">Withdrawal</a>
    </div>
  </div>

  <h2>Upload Top-up Savings Receipt</h2>

  <?php if ($message): ?>
    <div class="message <?= $success ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form action="upload_topups_receipt.php" method="POST" enctype="multipart/form-data" autocomplete="off">
    <label for="amount_saved">Amount Saved:</label>
    <input type="number" name="amount_saved" id="amount_saved" required min="0" step="100">

    <label for="receipt">Upload Receipt:</label>
    <input type="file" name="receipt" id="receipt" accept="image/*,application/pdf" required>

    <button type="submit">Submit</button>
  </form>

  <footer>
    <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
  </footer>

</body>
</html>
