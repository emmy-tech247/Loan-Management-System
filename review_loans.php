<?php
session_start();
include('db.php');

// Check if staff is logged in
if (!isset($_SESSION['staffId'])) {
    header("Location: staff_login.php");
    exit();
}

// Logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: staff_login.php");
    exit();
}

// Handle loan review action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $loanId = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'forward_to_admin') {
        // Update loan status
        $stmt = $conn->prepare("UPDATE loans SET status = 'forwarded_to_admin' WHERE id = ?");
        $stmt->bind_param("i", $loanId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "✅ Loan forwarded to admin.";
        } else {
            $_SESSION['error'] = "❌ Could not forward the loan.";
        }

        $stmt->close();
    }
}

// Fetch loans reviewed by RO
$loans = [];
$query = "SELECT loans.*, members.username FROM loans 
          JOIN members ON loans.member_id = members.id 
          WHERE loans.status = 'reviewed_by_ro'";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $loans[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Loans - Staff</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      padding: 40px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #007bff;
    }
    .loan-card {
      background: #fafafa;
      border: 1px solid #ccc;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .loan-card p {
      margin-bottom: 8px;
    }
    form button {
      padding: 10px 15px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    form button:hover {
      background: #218838;
    }
    .logout-btn {
      margin-top: 30px;
      padding: 10px 15px;
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .message {
      margin-bottom: 20px;
      color: green;
    }
    .error {
      margin-bottom: 20px;
      color: red;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Loans Reviewed by Relationship Officer</h2>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($loans)): ?>
      <p>No loans available for review at the moment.</p>
    <?php else: ?>
      <?php foreach ($loans as $loan): ?>
        <div class="loan-card">
          <p><strong>Member:</strong> <?= htmlspecialchars($loan['username']) ?></p>
          <p><strong>Amount:</strong> ₦<?= number_format($loan['amount'], 2) ?></p>
          <p><strong>Purpose:</strong> <?= htmlspecialchars($loan['purpose']) ?></p>
          <form method="POST">
            <input type="hidden" name="id" value="<?= $loan['id'] ?>">
            <button type="submit" name="action" value="forward_to_admin">➡️ Forward to Admin</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST">
      <button class="logout-btn" type="submit" name="logout">Logout</button>
    </form>
  </div>
</body>
</html>
