<?php
require_once "db.php";

$sql = "
    SELECT l.loan_id, l.loan_amount, l.tenure_month, l.loan_status, l.created_at,
           CONCAT(m.surname, ' ', m.first_name, ' ', m.other_names) AS full_name
    FROM loans l
    INNER JOIN members m ON l.member_id = m.member_id
    WHERE l.loan_status = 'forwarded_to_auditor'
    ORDER BY l.created_at DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".htmlspecialchars($row['loan_id'])."</td>
                <td>".htmlspecialchars($row['full_name'])."</td>
                <td>".number_format($row['loan_amount'], 2)."</td>
                <td>".htmlspecialchars($row['tenure_month'])."</td>
                <td>".htmlspecialchars($row['loan_status'])."</td>
                <td>".htmlspecialchars($row['created_at'])."</td>
                <td><a class='review-btn' href='auditor_review_loan.php?id=".$row['loan_id']."'>Review</a></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>✅ No loans awaiting auditor review.</td></tr>";
}
?>
