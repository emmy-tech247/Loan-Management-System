<?php
session_start();
require_once 'db.php';


// Fetch signatures securely
$stmt = $conn->prepare("SELECT s.signature_path, s.purpose, s.signed_at, m.full_name 
                        FROM signatures s 
                        JOIN members m ON s.member_id = m.member_id 
                        ORDER BY s.signed_at DESC");
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
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #333;
    }

    .mandate-box {
      background: #fff;
      border: 1px solid #ccc;
      padding: 15px;
      margin: 20px auto;
      max-width: 600px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    .mandate-box strong {
      display: inline-block;
      width: 80px;
      color: #555;
    }

    .mandate-box img {
      margin-top: 10px;
      max-width: 100%;
      height: auto;
      border: 1px solid #000;
      border-radius: 4px;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 40px 0;
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
      transform: translateY(0);
    }

    @media (max-width: 600px) {
      .mandate-box {
        margin: 10px;
        padding: 10px;
      }

      .logout-btn {
        width: 90%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <h2>Signed Mandates</h2>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="mandate-box">
      <div><strong>Member:</strong> <?= htmlspecialchars($row['full_name']) ?></div>
      <div><strong>Purpose:</strong> <?= htmlspecialchars($row['purpose']) ?></div>
      <div><strong>Date:</strong> <?= htmlspecialchars($row['signed_at']) ?></div>
      <img src="<?= htmlspecialchars($row['signature_path']) ?>" alt="Signature Image">
    </div>
  <?php endwhile; ?>

  <div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
  </div>

</body>
</html>
