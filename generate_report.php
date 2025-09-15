<?php
require 'db.php';
$type = $_GET['type'];
switch ($type) {
    case 'loans':
        $result = $conn->query("SELECT * FROM loans");
        break;
    case 'disbursed':
        $result = $conn->query("SELECT * FROM loans WHERE status = 'disbursed'");
        break;
    case 'savings':
        $result = $conn->query("SELECT * FROM savings_transactions");
        break;
    case 'collected':
        $result = $conn->query("SELECT * FROM loan_repayments");
        break;
    case 'fixed':
        $result = $conn->query("SELECT * FROM fixed_deposits");
        break;
    default:
        die("Invalid report type.");
}

echo "<table border='1'><tr>";
while ($field = $result->fetch_field()) {
    echo "<th>{$field->name}</th>";
}
echo "</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $col) {
        echo "<td>$col</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>
