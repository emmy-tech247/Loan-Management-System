<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized. Please log in.");
}

$member_id = $_SESSION['member_id'];

if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/receipts/';
    $filename = basename($_FILES['receipt']['name']);
    $targetFile = $uploadDir . time() . '_' . $filename;

    // Validate file type (optional)
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($_FILES['receipt']['type'], $allowedTypes)) {
        die("Invalid file type. Only PDF, JPG, PNG allowed.");
    }

    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO receipts (member_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $member_id, $targetFile);
        if ($stmt->execute()) {
            echo "✅ Receipt uploaded successfully. Waiting for admin approval.";
        } else {
            echo "❌ Database error: " . $stmt->error;
        }
    } else {
        echo "❌ Failed to upload file.";
    }
} else {
    echo "❌ No file uploaded or upload error.";
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Loan Management</title>
  <script src="js/script3.js"></script>
  <style>
    /* Basic navbar styling */
    body { font-family: Arial; padding: 20px; background: #f8f8f8; margin: 0;
      padding: 0; }
    form, table { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 10px; }
    input, select, textarea { display: block; width: 100%; margin: 10px 0; padding: 10px; }

   
.upload-section {
  background: #f9f9f9;
  padding: 40px 20px;
  max-width: 500px;
  margin: 100px auto;
  margin-top: 10px;
  border-radius: 12px;
  box-shadow: 0 0 12px rgba(0,0,0,0.08);
  font-family: 'Segoe UI', sans-serif;
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
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-bottom: 20px;
  background: #fff;
  cursor: pointer;
}

.receipt-form button {
  background-color: #004080;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.receipt-form button:hover {
  background-color: #0066cc;
}

.top_receipt{
  text-align:center ;
  color: #004080;
  font-weight: bold;
  max-width:1400px;
  margin: 50px ;
  font-size: 24px;
  margin-top: 70px;

}
    
   
  .center-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0px;
  
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

.logout-btn:active {
  background-color: #0056b3;
  transform: translateY(0);
}: flex;
  justify-content: center;
  align-items: center;
  
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

.logout-btn:active {
  background-color: #0056b3;
  transform: translateY(0);
}



  </style>
</head>
<body>
  
  
 <div class="top_receipt">UPLOAD EVIDENCE OF PAYMENT </div>
 <section class="upload-section">
  <form action="upload_receipt.php" method="POST" enctype="multipart/form-data" class="receipt-form">
    <label for="receipt">Select Receipt (PDF/JPG/PNG):</label>
    <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png" required>
    <button type="submit">Upload Receipt</button>
  </form>
</section>


   <div class="center-container">
      <a class="logout-btn" href="member.php">Back </a>
   </div>


</body>
</html>
