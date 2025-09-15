<?php
require('db.php');
require('fpdf/fpdf.php'); // Download FPDF from http://www.fpdf.org/

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$type = isset($_GET['type']) ? $_GET['type'] : 'pdf';

$sql = "
    SELECT 
        l.loan_id,
        m.member_id,
        CONCAT(m.surname, ' ', m.first_name) AS member_name,
        l.loan_amount,
        l.interest_rate,
        l.start_date,
        l.end_date,
        l.loan_status,
        l.purpose,
        l.guarantor_id,
        l.monthly_repayment,
        l.amount_paid,
        (l.loan_amount + (l.loan_amount * l.interest_rate / 100) - l.amount_paid) AS loan_balance
    FROM loans l
    INNER JOIN members m ON l.member_id = m.member_id
";

if ($filter === 'day') {
    $sql .= " WHERE DATE(l.start_date) = CURDATE()";
} elseif ($filter === 'month') {
    $sql .= " WHERE YEAR(l.start_date) = YEAR(CURDATE()) AND MONTH(l.start_date) = MONTH(CURDATE())";
} elseif ($filter === '3months') {
    $sql .= " WHERE l.start_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
}

$sql .= " ORDER BY l.start_date DESC";

$result = $conn->query($sql);

if ($type === 'pdf') {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Loan Report',0,1,'C');

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(15,10,'ID',1);
    $pdf->Cell(35,10,'Member Name',1);
    $pdf->Cell(25,10,'Amount',1);
    $pdf->Cell(25,10,'Start Date',1);
    $pdf->Cell(25,10,'End Date',1);
    $pdf->Cell(25,10,'Balance',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(15,8,$row['loan_id'],1);
        $pdf->Cell(35,8,$row['member_name'],1);
        $pdf->Cell(25,8,number_format($row['loan_amount'],2),1);
        $pdf->Cell(25,8,$row['start_date'],1);
        $pdf->Cell(25,8,$row['end_date'],1);
        $pdf->Cell(25,8,number_format($row['loan_balance'],2),1);
        $pdf->Ln();
    }

    $pdf->Output();
} else {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="loan_report.xls"');
    header('Cache-Control: max-age=0');

    echo "Loan ID\tMember Name\tAmount\tStart Date\tEnd Date\tBalance\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['loan_id']."\t".
             $row['member_name']."\t".
             $row['loan_amount']."\t".
             $row['start_date']."\t".
             $row['end_date']."\t".
             $row['loan_balance']."\n";
    }
}
