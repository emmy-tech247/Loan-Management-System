<?php
$conn = new mysqli("localhost", "root", "", "loan_system");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$filter = $_GET['filter'] ?? 'all';
$where = "";

switch ($filter) {
    case 'daily':
        $where = "WHERE DATE(transaction_date) = CURDATE()";
        break;
    case 'monthly':
        $where = "WHERE YEAR(transaction_date) = YEAR(CURDATE()) AND MONTH(transaction_date) = MONTH(CURDATE())";
        break;
    case '3months':
        $where = "WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        break;
}

$sql = "
    SELECT transaction_id, member_id, transaction_type, amount, transaction_date, reference
    FROM transactions
    $where
    ORDER BY transaction_date DESC
";
$result = $conn->query($sql);

// Output CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="financial_report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['Transaction ID', 'Member ID', 'Type', 'Amount', 'Date', 'Reference']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
