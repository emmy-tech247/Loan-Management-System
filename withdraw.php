<?php
require 'db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];
$message = "";

// Handle withdrawal or rollover
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['fd_id'])) {
    $fd_id = intval($_POST['fd_id']);
    $action = $_POST['action'];
    $today = date('Y-m-d');

    $stmt = $conn->prepare("SELECT * FROM fixed_deposits WHERE id = ? AND member_id = ? AND status = 'active'");
    $stmt->bind_param("ii", $fd_id, $member_id);
    $stmt->execute();
    $fd = $stmt->get_result()->fetch_assoc();

    if ($fd && $fd['maturity_date'] <= $today) {
        if ($action === 'withdraw') {
            $update = $conn->prepare("UPDATE fixed_deposits SET status = 'withdrawn', action_date = ? WHERE id = ?");
            $update->bind_param("si", $today, $fd_id);
            $update->execute();
            $message = "✅ Withdrawal successful.";
        } elseif ($action === 'rollover') {
            $conn->begin_transaction();
            try {
                $update = $conn->prepare("UPDATE fixed_deposits SET status = 'rolled_over', action_date = ? WHERE id = ?");
                $update->bind_param("si", $today, $fd_id);
                $update->execute();

                $new_start = $today;
                $new_maturity = date('Y-m-d', strtotime("+{$fd['tenure_months']} months"));
                $insert = $conn->prepare("INSERT INTO fixed_deposits (member_id, amount_deposited, tenure_months, interest_rate, start_date, maturity_date, status)
                                          VALUES (?, ?, ?, ?, ?, ?, 'active')");
                $insert->bind_param("ididss", $member_id, $fd['amount_deposited'], $fd['tenure_months'], $fd['interest_rate'], $new_start, $new_maturity);
                $insert->execute();

                $conn->commit();
                $message = "🔁 Rollover successful. New fixed deposit created.";
            } catch (Exception $e) {
                $conn->rollback();
                $message = "❌ Rollover failed. Please try again.";
            }
        }
    } else {
        $message = "⚠️ Action not allowed. Maturity not reached or already processed.";
    }

    header("Location: withdraw.php?msg=" . urlencode($message));
    exit();
}

// Fetch matured FDs
$stmt = $conn->prepare("SELECT * FROM fixed_deposits WHERE member_id = ? AND status = 'active' AND maturity_date <= CURDATE()");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$message = $_GET['msg'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>FD Withdrawal / Rollover Options</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/main.css" /> <!-- Use an external CSS file -->
  <style>
    body {
  font-family: 'Segoe UI', sans-serif;
  background: #f0f2f5;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 900px;
  margin: auto;
  background: #fff;
  padding: 30px;
  margin-top: 40px;
  border-radius: 10px;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  margin-bottom: 20px;
}

.alert {
  background-color: #d1ecf1;
  color: #0c5460;
  padding: 12px;
  margin-bottom: 20px;
  border-radius: 6px;
}

.styled-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.styled-table th, .styled-table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: center;
}

.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-danger:hover, .btn-primary:hover {
  opacity: 0.9;
}

  </style>
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container">
  <h2>Withdrawal / Rollover Options</h2>

  <?php if ($message): ?>
    <div class="alert"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (empty($records)): ?>
    <p style="text-align:center;">No matured fixed deposits available.</p>
  <?php else: ?>
    <table class="styled-table">
      <thead>
        <tr>
          <th>Amount</th>
          <th>Tenure</th>
          <th>Rate</th>
          <th>Start Date</th>
          <th>Maturity</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($records as $fd): ?>
        <tr>
          <td>₦<?= number_format($fd['amount_deposited'], 2) ?></td>
          <td><?= htmlspecialchars($fd['tenure_months']) ?> months</td>
          <td><?= htmlspecialchars($fd['interest_rate']) ?>%</td>
          <td><?= htmlspecialchars($fd['start_date']) ?></td>
          <td><?= htmlspecialchars($fd['maturity_date']) ?></td>
          <td>
            <form method="POST" style="display:flex;gap:6px;">
              <input type="hidden" name="fd_id" value="<?= $fd['id'] ?>">
              <button type="submit" name="action" value="withdraw" class="btn btn-danger">Withdraw</button>
              <button type="submit" name="action" value="rollover" class="btn btn-primary">Rollover</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>
</body>
</html>
