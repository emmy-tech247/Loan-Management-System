<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

// Connect to the database using MySQLi
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch manual deposits pending approval
$sql = "SELECT * FROM savings WHERE type = 'manual' AND approved IS NULL";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Approve Deposits</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-box">
    <h2>Approve Deposits</h2>
    <table>
      <tr><th>ID</th><th>Member ID</th><th>Amount</th><th>Actions</th></tr>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($deposit = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $deposit['id'] ?></td>
            <td><?= $deposit['member_id'] ?></td>
            <td><?= $deposit['amount_deposited'] ?></td>
            <td>
              <form method="POST" action="approve_deposit_action.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $deposit['id'] ?>">
                <button type="submit" name="action" value="approve">Approve</button>
                <button type="submit" name="action" value="reject">Reject</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No pending deposits found.</td></tr>
      <?php endif; ?>
    </table>
    <a href="admin.php">← Back to Dashboard</a>
  </div>
</body>
</html>

<?php $conn->close(); ?>
