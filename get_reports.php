<?php
$res = $conn->query("SELECT 
    COUNT(*) AS total_loans,
    SUM(loan_amount) AS total_disbursed,
    (SELECT SUM(amount_saved) FROM savings) AS total_savings,
    (SELECT SUM(amount_deposited ) FROM fixed_deposits) AS total_fd
FROM loans");

echo json_encode($res->fetch_assoc());
?>
