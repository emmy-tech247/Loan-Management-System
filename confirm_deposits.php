<?php
session_start();
include('db.php');

// Ensure only admin2 has access


// Fetch acknowledged but unconfirmed payments
$query = "SELECT * FROM payment_transactions 
          WHERE acknowledged_by_admin1 = 1 
          AND confirmed_by_admin2 = 0 
          ORDER BY acknowledged_at DESC";

$result = $conn->query($query);
$payments = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin 2 - Confirm Deposits</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      background: #f4f6f9;
    }

    .login-box {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    .payment-card {
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      transition: transform 0.2s ease;
    }

    .payment-card:hover {
      transform: scale(1.01);
    }

    .payment-card p {
      margin: 6px 0;
      font-size: 15px;
      color: #444;
    }

    .payment-card strong {
      color: #111;
    }

    form {
      margin-top: 15px;
    }

    button {
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background: #0056b3;
      transform: translateY(-1px);
    }

    button:active {
      transform: translateY(0);
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 40px;
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
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    @media (max-width: 600px) {
      .payment-card {
        padding: 15px;
      }
      button, .logout-btn {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>✅ Payments Awaiting Final Confirmation</h2>

    <?php if (empty($payments)): ?>
      <p style="text-align:center; color:#888;">No deposits waiting for confirmation.</p>
    <?php else: ?>
      <?php foreach ($payments as $payment): ?>
        <div class="payment-card">
          <p><strong>Member ID:</strong> <?= htmlspecialchars($payment['member_id']) ?></p>
          <p><strong>Amount:</strong> ₦<?= number_format($payment['amount_paid'], 2) ?></p>
          <p><strong>Reference:</strong> <?= htmlspecialchars($payment['reference']) ?></p>
          <p><strong>Acknowledged At:</strong> <?= htmlspecialchars($payment['acknowledged_at']) ?></p>
          <form action="verify_payment.php" method="POST">
            <input type="hidden" name="payment_id" value="<?= (int)$payment['id'] ?>">
            <input type="hidden" name="action" value="confirm">
            <button type="submit">Confirm</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="center-container">
      <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
