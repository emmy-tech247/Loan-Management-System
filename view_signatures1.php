<?php
session_start();
require_once 'db.php';

// Use prepared statements for security (even if no user input is used here, it's best practice)
$query = "SELECT s.*, m.full_name FROM signatures s JOIN members m ON s.member_id = m.member_id ORDER BY s.signed_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signed Mandates</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f9f9f9;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .mandate {
      border: 1px solid #ccc;
      padding: 15px;
      margin: 0 auto 20px auto;
      background-color: #fff;
      max-width: 600px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      transition: box-shadow 0.3s ease-in-out;
    }

    .mandate:hover {
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .mandate img {
      width: 100%;
      max-width: 300px;
      height: auto;
      border: 1px solid #000;
      display: block;
      margin-top: 10px;
    }

    .center-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
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

    .logout-btn:active {
      background-color: #0056b3;
      transform: translateY(0);
    }
  </style>
</head>
<body>

  <h2>Signed Mandates</h2>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="mandate">
      <strong>Member:</strong> <?= htmlspecialchars($row['full_name']) ?><br>
      <strong>Purpose:</strong> <?= htmlspecialchars($row['purpose']) ?><br>
      <strong>Date:</strong> <?= htmlspecialchars($row['signed_at']) ?><br>
      <img src="<?= htmlspecialchars($row['signature_path']) ?>" alt="Signature">
    </div>
  <?php endwhile; ?>

  <div class="center-container">
    <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
  </div>

</body>
</html>
