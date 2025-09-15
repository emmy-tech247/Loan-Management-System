<?php
session_start();
include 'db.php';

// Security headers for fast, secure loading
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer");


$document_id = intval($_GET['document_id']);
$admin1_id = intval($_SESSION['adminId']);

// Check if document exists
$stmt = $conn->prepare("SELECT id FROM documents WHERE id = ?");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div style='color:red;text-align:center;'>❌ Document with ID $document_id not found in the database.</div>");
}

// Acknowledge document
$ack = $conn->prepare("UPDATE documents 
    SET admin1_acknowledged = 1, admin1_id = ?, acknowledged_at = NOW() 
    WHERE id = ?");
$ack->bind_param("ii", $admin1_id, $document_id);
$ack->execute();

// Output Result (Responsive, Clean)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document Acknowledgment</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      text-align: center;
    }

    .container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      max-width: 450px;
      width: 90%;
    }

    .message {
      font-size: 1.1rem;
      margin-bottom: 20px;
      color: #333;
    }

    .success {
      color: green;
    }

    .warning {
      color: #e69500;
    }

    a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease-in-out;
    }

    a:hover {
      background-color: #0056b3;
    }

    @media screen and (max-width: 480px) {
      .container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="message <?= $ack->affected_rows > 0 ? 'success' : 'warning' ?>">
      <?= $ack->affected_rows > 0 
          ? "✅ Document ID <strong>$document_id</strong> successfully acknowledged by Admin 1." 
          : "⚠️ Document already acknowledged or update failed." ?>
    </div>
    <a href="document.php">🔙 Back to Document List</a>
  </div>
</body>
</html>
