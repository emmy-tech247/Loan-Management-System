<?php
session_start();
require_once 'db.php';

// Fetch unacknowledged payments
$query = "SELECT p.id, p.member_id, p.amount_paid, p.reference, p.created_at, m.first_name, m.surname 
          FROM payment_transactions p
          JOIN members m ON p.member_id = m.member_id
          WHERE p.acknowledged_by_admin1 = 0
          ORDER BY p.created_at DESC";

$result = $conn->query($query);
$payments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin 1 - Acknowledge Deposits</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.5em;
      color: #333;
    }

    .payment-card {
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      background-color: #fafafa;
    }

    .payment-card p {
      margin: 6px 0;
      font-size: 0.95em;
    }

    .payment-card form {
      margin-top: 10px;
    }

    .payment-card button {
      background-color: #28a745;
      color: #fff;
      padding: 10px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .payment-card button:hover {
      background-color: #218838;
    }

    .message {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .center-container {
      text-align: center;
      margin-top: 30px;
    }

    .logout-btn {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    @media (max-width: 600px) {
      .payment-card {
        font-size: 14px;
        padding: 12px;
      }

      .payment-card button {
        width: 100%;
      }

      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🧾 Payments Awaiting Acknowledgment</h2>

    <?php if (isset($_GET['success'])): ?>
      <p class="message">✅ Deposit acknowledged successfully.</p>
    <?php endif; ?>

    <?php if (empty($payments)): ?>
      <p>No new deposits to acknowledge.</p>
    <?php else: ?>
      <?php foreach ($payments as $payment): ?>
        <div class="payment-card">
          <p><strong>Member:</strong> <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['surname']) ?> (ID: <?= (int)$payment['member_id'] ?>)</p>
          <p><strong>Amount:</strong> ₦<?= number_format((float)$payment['amount_paid'], 2) ?></p>
          <p><strong>Reference:</strong> <?= htmlspecialchars($payment['reference']) ?></p>
          <p><strong>Date:</strong> <?= date('d M Y H:i', strtotime($payment['created_at'])) ?></p>
          <form action="verify_payment.php" method="POST">
            <input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>">
            <input type="hidden" name="action" value="acknowledge">
            <button type="submit">✅ Acknowledge</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="center-container">
      <a class="logout-btn" href="admin1.php">⬅ Back to Admin Dashboard</a>
    </div>
  </div>
   
</body>
</html>
<?php $conn->close(); ?>
