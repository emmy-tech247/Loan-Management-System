<?php
include 'db.php';
$document_id = $_GET['document_id'];

$result = $conn->query("SELECT * FROM documents WHERE id = $document_id");
$document = $result->fetch_assoc();

if ($document) {
    $file_path = $document['file_path'];
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    readfile($file_path);
} else {
    echo "Document not found!";
}
?>
