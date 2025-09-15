<?php
require_once 'db.php';

// In production, you should fetch $member_id from session
session_start();
if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    // Redirect unauthorized access
    header("Location: login.php");
    exit();
}

$member_id = intval($_SESSION['member_id']);

// Prepare and execute query
$stmt = $conn->prepare("
    SELECT id, amount, due_date 
    FROM loans 
    WHERE member_id = ? 
      AND due_date <= CURDATE() 
      AND status = 'active'
");

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

// Output
if ($result->num_rows > 0) {
    echo "<h3>📅 Loans Due or Overdue</h3>";
    while ($row = $result->fetch_assoc()) {
        $loanId = htmlspecialchars($row['id']);
        $amount = "₦" . number_format($row['amount'], 2);
        $dueDate = htmlspecialchars($row['due_date']);
        echo "Loan ID: {$loanId}, Amount: {$amount}, Due Date: {$dueDate}<br>";
    }
} else {
    echo "<p>No due or overdue loans found.</p>";
}

$stmt->close();
$conn->close();
?>
