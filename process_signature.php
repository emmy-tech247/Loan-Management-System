<?php
include 'db.php';
$document_id = $_POST['document_id'];
$signature = $_POST['signature'];
$member_id = 1; // Logged-in user

$conn->query("INSERT INTO signatures (document_id, member_id, signature) 
              VALUES ($document_id, $member_id, '$signature')");

$conn->query("UPDATE documents SET status = 'signed' WHERE id = $document_id");

echo json_encode(['status' => 'success']);
?>
