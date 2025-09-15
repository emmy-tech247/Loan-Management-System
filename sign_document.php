<?php
include 'db.php';
$document_id = $_GET['document_id'];

$result = $conn->query("SELECT * FROM documents WHERE id = $document_id");
$document = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Document</title>
    <link rel="stylesheet" href="css/style9.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</head>
<body>
    <header>
        <h1>Sign Document</h1>
    </header>

    <main>
        <h2>Document: <?php echo $document['file_name']; ?></h2>
        <canvas id="signature-pad" width="400" height="200"></canvas>
        <button id="clear">Clear</button>
        <button id="save">Save Signature</button>
    </main>

    <footer>
        <p>&copy; 2025 Loan Management System</p>
    </footer>

    <script src="js/script9.js"></script>
</body>
</html>
