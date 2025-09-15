<?php
// option_page.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = $_POST['account_type'] ?? '';

    if ($choice === 'loan_saver.php') {
        // Example: Redirect or display content for Loan Savers
        header("Location: loan_saver_form.html");
        exit;
    } elseif ($choice === 'loan_borrower.php') {
        // Example: Redirect or display content for Loan Borrowers
        header("Location: loan_borrower_form.html");
        exit;
    } else {
        $error = "❌ Please select an option.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Select Account Type</title>
  <style>
    /* ===== General Reset ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* ===== Body Styling ===== */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, , #007bff);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: #333;
}

/* ===== Container ===== */
.container {
  background: #fff;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  width: 350px;
  text-align: center;
  transition: all 0.3s ease-in-out;
}
.container:hover {
  transform: translateY(-5px);
}

/* ===== Heading ===== */
h2 {
  margin-bottom: 25px;
  font-size: 22px;
  color: #004080;
  font-weight: 600;
}

/* ===== Radio Buttons ===== */
label {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f9f9f9;
  border: 1px solid #ddd;
  padding: 12px 15px;
  border-radius: 8px;
  margin: 10px 0;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.2s ease;
}
label:hover {
  background: #eef4ff;
  border-color: #004080;
}
input[type="radio"] {
  accent-color: #004080; /* Modern browsers */
  transform: scale(1.2);
}

/* ===== Button ===== */
button {
  margin-top: 20px;
  padding: 12px 20px;
  background: #004080;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  width: 100%;
  font-weight: bold;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
}
button:hover {
  background: #007bff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* ===== Error Message ===== */
.error {
  color: #d9534f;
  margin-top: 15px;
  font-size: 14px;
  font-weight: bold;
}

  </style>
</head>
<body>

<div class="container">
  <h2>Select Loan Category</h2>
  <form method="POST" action="">
    <label>
      <input type="radio" name="account_type" value="loan_saver.php"> Monthly Saver</input>
    </label>
    <label>
      <input type="radio" name="account_type" value="loan_borrower.php"> Loan Borrower </input>
    </label>

    <button type="submit">Continue</button>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
  </form>
</div>

</body>
</html>
