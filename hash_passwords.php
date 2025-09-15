<?php
// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch all users with plain text passwords
$result = $mysqli->query("SELECT id, password FROM admin_panel");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $plainPassword = $row['password'];

        // Check if the password is already hashed
        if (password_get_info($plainPassword)['algo'] === 0) {
            // Not hashed yet, so hash it
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Update the user's password in the database
            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();

            echo "Updated user ID $userId with hashed password.<br>";
        } else {
            echo "User ID $userId already has a hashed password. Skipped.<br>";
        }
    }
} else {
    echo "No users found.";
}

$mysqli->close();
?>
