<?php
require 'db.php';
$recipient = $_POST['recipient'];
$message = $_POST['message'];

// Save alert
$stmt = $conn->prepare("INSERT INTO alerts (recipient, message) VALUES (?, ?)");
$stmt->bind_param("ss", $recipient, $message);
$stmt->execute();

echo "Alert sent to $recipient.";
?>
