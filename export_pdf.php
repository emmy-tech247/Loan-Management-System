<?php
require("fpdf/fpdf.php");

if (!isset($_POST['scheduleData'])) {
    die("No data received");
}

$data = json_decode($_POST['scheduleData'], true);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 10, "Loan Amortization Schedule", 0, 1, "C");
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(20, 10, "Month", 1);
$pdf->Cell(35, 10, "EMI (N)", 1);
$pdf->Cell(35, 10, "Principal (N)", 1);
$pdf->Cell(35, 10, "Interest (N)", 1);
$pdf->Cell(35, 10, "Balance (N)", 1);
$pdf->Ln();

$pdf->SetFont("Arial", "", 10);
foreach ($data as $row) {
    $pdf->Cell(20, 10, $row['month'], 1);
    $pdf->Cell(35, 10, $row['emi'], 1);
    $pdf->Cell(35, 10, $row['principal'], 1);
    $pdf->Cell(35, 10, $row['interest'], 1);
    $pdf->Cell(35, 10, $row['balance'], 1);
    $pdf->Ln();
}

$pdf->Output("D", "Amortization_Schedule.pdf");
