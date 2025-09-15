<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    die("Unauthorized access. Please <a href='login.php'>log in</a>.");
}

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/receipts/';
        $filename = basename($_FILES['receipt']['name']);
        $safeFilename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $targetFile = $uploadDir . time() . '_' . $safeFilename;

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array(mime_content_type($_FILES['receipt']['tmp_name']), $allowedTypes)) {
            die("❌ Invalid file type. Only PDF, JPG, and PNG are allowed.");
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO receipts (member_id, file_path) VALUES (?, ?)");
            $stmt->bind_param("is", $member_id, $targetFile);
            if ($stmt->execute()) {
                echo "<p style='text-align:center; color:green;'>✅ Receipt uploaded successfully. Waiting for admin approval.</p>";
            } else {
                echo "<p style='text-align:center; color:red;'>❌ Database error: " . htmlspecialchars($stmt->error) . "</p>";
            }
        } else {
            echo "<p style='text-align:center; color:red;'>❌ Failed to upload file.</p>";
        }
    } else {
        echo "<p style='text-align:center; color:red;'>❌ No file uploaded or upload error.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Receipt</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }

    .top_receipt {
      text-align: center;
      color: #004080;
      font-weight: bold;
      margin: 100px 10px 20px;
      font-size: 24px;
    }

    .upload-section {
      background: #fff;
      padding: 30px;
      max-width: 500px;
      margin: 0 auto 60px;
      margin:top 20px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.08);
    }

    .receipt-form {
      display: flex;
      flex-direction: column;
    }

    .receipt-form label {
      font-weight: bold;
      margin-bottom: 10px;
      color: #004080;
    }

    .receipt-form input[type="file"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 20px;
      background: #fff;
      cursor: pointer;
    }

    .receipt-form button {
      background-color: #004080;
      color: white;
      padding: 14px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .receipt-form button:hover {
      background-color: #007bff;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 50px;
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

    @media (max-width: 600px) {
      .top_receipt {
        font-size: 20px;
        margin: 30px 10px 10px;
      }

      .upload-section {
        padding: 20px;
        margin: 0 10px 30px;
      }

      .receipt-form input[type="file"],
      .receipt-form button {
        font-size: 14px;
        padding: 10px;
      }

      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

  <div class="top_receipt">UPLOAD EVIDENCE OF PAYMENT</div>

  <section class="upload-section">
    <form action="upload_receipt.php" method="POST" enctype="multipart/form-data" class="receipt-form">
      <label for="receipt">Select Receipt (PDF, JPG, PNG):</label>
      <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png" required>
      <button type="submit">Upload Receipt</button>
    </form>
  </section>

  <div class="center-container">
    <a class="logout-btn" href="member.php">Back</a>
  </div>

</body>
</html>
