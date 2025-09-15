<?php
// auditor_dashboard.php
session_start();
require_once "db.php";

// Ensure auditor is logged in
if (!isset($_SESSION['auditorId'])) {
    header("Location: auditor_login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auditor_login.php");
    exit();
}



// ✅ Fetch loans forwarded by Relationship Officer
$sql = "
    SELECT l.loan_id, l.loan_amount, l.tenure_month, l.loan_status, l.created_at,
           CONCAT(m.surname, ' ', m.first_name, ' ', m.other_names) AS full_name
    FROM loans l
    INNER JOIN members m ON l.member_id = m.member_id
    WHERE l.loan_status = 'forwarded_to_auditor'
    ORDER BY l.created_at DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("Database Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Auditor Dashboard</title>
  <style>
    body {font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px;}
    h2 {color: #003d6a;}
    table {width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,.1);}
    th, td {border: 1px solid #ddd; padding: 10px; text-align: center;}
    th {background: #003d6a; color: #fff;}
    tr:nth-child(even) {background: #f9f9f9;}
    a.review-link {color: #0066cc; text-decoration: none; font-weight: bold;}
    a.review-link:hover {text-decoration: underline;}

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
    background: linear-gradient(135deg, #6bb9eeff, #63b6eeff);
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
  <h2>📋 Auditor Dashboard – Loans Awaiting Approval</h2>
  <table>
    <tr>
      <th>Loan ID</th>
      <th>Borrower (Full Name)</th>
      <th>Amount (₦)</th>
      <th>Tenure (Months)</th>
      <th>Status</th>
      <th>Forwarded On</th>
      <th>Action</th>
    </tr>
    <tbody id="loanData">
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['loan_id']) ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= number_format($row['loan_amount'], 2) ?></td>
          <td><?= htmlspecialchars($row['tenure_month']) ?></td>
          <td><?= htmlspecialchars($row['loan_status']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><a class="review-btn" href="auditor_review_loan.php?id=<?= $row['loan_id'] ?>">Review</a></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7">✅ No loans awaiting auditor review.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <script>
    // ✅ Auto-refresh loans without page reload
    function fetchLoans() {
      fetch("auditor_fetch_loans.php")
        .then(response => response.text())
        .then(data => {
          document.getElementById("loanData").innerHTML = data;
        })
        .catch(error => console.error("Error fetching loans:", error));
    }

    // Refresh every 5 seconds
    setInterval(fetchLoans, 5000);
  </script><br><br>

   <a href="staff_change_password.php" class="modern-btn">Change Password</a>
   
<div class="center-container">
    <a class="logout-btn" href="staff.php">Logout</a>
</div>
</body>
</html>
