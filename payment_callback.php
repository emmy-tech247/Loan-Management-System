<?php
include 'db.php';
$reference = $_GET['reference'];

$conn->query("UPDATE transactions SET status = 'admin1_confirmed' WHERE reference = '$reference'");
echo "Payment confirmed by Admin 1. Awaiting final approval.";
?>