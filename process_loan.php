<?php
session_start();
include('db.php');

// Check if logged in
if (!isset($_SESSION['relationship_officerId'])) {
    header("Location: relationship_officer_login.php");
    exit();
}

// If loan form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrower_name'])) {
    // Secure inputs
    $borrower_name   = mysqli_real_escape_string($conn, $_POST['borrower_name']);
    $borrower_phone  = mysqli_real_escape_string($conn, $_POST['borrower_phone']);
    $borrower_email  = mysqli_real_escape_string($conn, $_POST['borrower_email']);
    $borrower_address= mysqli_real_escape_string($conn, $_POST['borrower_address']);
    $guarantor1_name = mysqli_real_escape_string($conn, $_POST['guarantor1_name']);
    $guarantor1_phone= mysqli_real_escape_string($conn, $_POST['guarantor1_phone']);
    $guarantor1_email= mysqli_real_escape_string($conn, $_POST['guarantor1_email']);
    $guarantor1_address= mysqli_real_escape_string($conn, $_POST['guarantor1_address']);
    $guarantor2_name = mysqli_real_escape_string($conn, $_POST['guarantor2_name']);
    $guarantor2_phone= mysqli_real_escape_string($conn, $_POST['guarantor2_phone']);
    $guarantor2_email= mysqli_real_escape_string($conn, $_POST['guarantor2_email']);
    $guarantor2_address= mysqli_real_escape_string($conn, $_POST['guarantor2_address']);
    $facility_type   = mysqli_real_escape_string($conn, $_POST['facility_type']);
    $purpose         = mysqli_real_escape_string($conn, $_POST['purpose']);
    $loan_amount     = (float)$_POST['loan_amount'];
    $interest_rate   = (float)$_POST['interest_rate'];
    $tenure          = (int)$_POST['tenure'];
    $start_date      = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date        = mysqli_real_escape_string($conn, $_POST['end_date']);
    $total_loan      = (float)$_POST['total_loan'];
    $total_repayment = (float)$_POST['total_repayment'];
    $monthly_repayment = (float)$_POST['monthly_repayment'];

    // Handle receipt upload
    $receipt_path = "";
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === 0) {
        $target_dir = "uploads/receipts/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES["receipt"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file);
        $receipt_path = $target_file;
    }

    // Insert loan into database
    $stmt = $conn->prepare("INSERT INTO loans 
        (borrower_name, borrower_phone, borrower_email, borrower_address,
         guarantor1_name, guarantor1_phone, guarantor1_email, guarantor1_address,
         guarantor2_name, guarantor2_phone, guarantor2_email, guarantor2_address,
         facility_type, purpose, amount, interest_rate, tenure, start_date, end_date,
         total_loan, total_repayment, monthly_repayment, receipt_path, status)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'submitted')");
    
    $stmt->bind_param("sssssssssssssdidssddds", 
        $borrower_name, $borrower_phone, $borrower_email, $borrower_address,
        $guarantor1_name, $guarantor1_phone, $guarantor1_email, $guarantor1_address,
        $guarantor2_name, $guarantor2_phone, $guarantor2_email, $guarantor2_address,
        $facility_type, $purpose, $loan_amount, $interest_rate, $tenure, $start_date, $end_date,
        $total_loan, $total_repayment, $monthly_repayment, $receipt_path
    );
    $stmt->execute();
}

// Logout if triggered
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: relationship_officer_login.php");
    exit();
}

// Fetch submitted loans
$loans = [];
$query = "SELECT * FROM loans WHERE status = 'submitted'";
$result = mysqli_query($conn, $query);
if ($result) {
    $loans = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
