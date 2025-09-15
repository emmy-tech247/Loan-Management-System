<?php
$host = "localhost";
$dbname = "loan_system";
$username = "root";
$password = "";

// Create connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Alias $conn as $mysqli (so both work)
$mysqli = $conn;

if ($conn->connect_errno) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn; // you can also return $mysqli if needed
?>
