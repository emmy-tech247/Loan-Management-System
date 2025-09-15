<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

// Connect to the database using MySQLi
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'loan_system';

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$id = intval($_POST['id']);
$action = $_POST['action'];
$status = ($action === 'approve') ? 1 : 0;

// Use prepared statement
$stmt = $conn->prepare("UPDATE savings SET approved = ? WHERE id = ?");
$stmt->bind_param("ii", $status, $id);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: approve_deposits.php");
exit();
?>
