<?php
session_start();
require_once "db.php";

// Only allow relationship officer role

// Check if logged in
if (!isset($_SESSION['relationship_officerId'])) {
    header("Location: relationship_officer_login.php");
    exit();
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: relationship_officer_login.php");
    exit();
}

// ✅ Join loans with members and build full name from separate fields
$sql = "SELECT l.*, 
               CONCAT(m.surname, ' ', m.first_name, ' ', m.other_names) AS full_name
        FROM loans l
        INNER JOIN members m ON l.member_id = m.member_id
        WHERE l.assigned_to = 'relationship_officer' 
        AND l.loan_status = 'submitted'
        ORDER BY l.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Relationship Officer Dashboard</title>
  <style>
    body{font-family:Arial;background: #f4f6f9;padding:20px}
    table{width:100%;border-collapse:collapse;background: #fff;box-shadow:0 2px 5px rgba(0,0,0,.1)}
    th,td{border:1px solid #ddd;padding:10px;text-align:center}
    th{background: #003d6a;color: #fff}

    .review-btn {
      display: inline-block;
      padding: 8px 16px;
      background: linear-gradient(135deg, #4CAF50, #45a049);
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
    .review-btn:hover {
      background: linear-gradient(135deg, #45a049, #3e8e41);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .review-btn:active {
      transform: scale(0.97);
    }

    .modern-btn {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #2a91d6ff, #2a91d6ff);
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.modern-btn:hover {
    background: linear-gradient(135deg, #45a049, #1b5e20);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.modern-btn:active {
    transform: scale(0.98);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.center-container {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }

    .logout-btn {
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
  </style>
</head>
<body>
  <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
  <h2>Relationship Officer Dashboard</h2>
  <table>
    <tr>
      <th>Loan ID</th>
      <th>Borrower (Full Name)</th>
      <th>Amount</th>
      <th>Tenure (Months)</th>
      <th>Status</th>
      <th>Submitted On</th>
      <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['loan_id']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td>₦<?= number_format($row['loan_amount'],2) ?></td>
        <td><?= htmlspecialchars($row['tenure_month']) ?></td>
        <td><?= htmlspecialchars($row['loan_status']) ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><a class="review-btn" href="relationship_review_loan.php?id=<?= $row['loan_id'] ?>">Review</a></td>
      </tr>
    <?php endwhile; ?>
  </table><br><br>
  
  <a href="staff_change_password.php" class="modern-btn">Change Password</a> 
  <div class="center-container">
    <a class="logout-btn" href="staff.php">Logout</a>
  </div>
</body>
</html>
