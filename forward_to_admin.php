<?php
session_start();
require 'db.php';

// Ensure only staff can access
if (!isset($_SESSION['staffId'])) {
    header("Location: staff_login.php");
    exit();
}

$error = '';
$success = '';

// Check if loan ID is provided
if (isset($_GET['id'])) {
    $loan_id = intval($_GET['id']);
    
    // Verify loan exists
    $check = $conn->prepare("SELECT id FROM loans WHERE id = ?");
    $check->bind_param("i", $loan_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        // Update status to 'reviewed'
        $stmt = $conn->prepare("UPDATE loans SET status = 'reviewed' WHERE id = ?");
        $stmt->bind_param("i", $loan_id);

        if ($stmt->execute()) {
            $stmt->close();
            $success = "Loan application #$loan_id has been forwarded to the Admin for review.";
        } else {
            $error = "Failed to forward application. Please try again.";
            $stmt->close();
        }
    } else {
        $error = "Loan application not found.";
        $check->close();
    }
} else {
    $error = "Invalid request. No loan ID provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forward to Admin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      padding: 40px;
      display: flex;
      justify-content: center;
    }
    .message-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      width: 500px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    .message-box h2 {
      color: #007bff;
      margin-bottom: 20px;
    }
    .message-box p {
      font-size: 1.1rem;
      color: #333;
    }
    .error {
      color: red;
    }
    .success {
      color: green;
      font-weight: bold;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="message-box">
    <h2>➡ Forward to Admin</h2>

    <?php if ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php else: ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <a href="review_loans.php">← Back to Loan Reviews</a>
  </div>
</body>
</html>
