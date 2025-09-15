<?php
session_start();
require 'db.php'; // Ensure correct path to your database connection file

// Check if the form was submitted properly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $member_id = (int)$_POST['member_id']; // Sanitize to prevent injection
    $action = $_POST['action'];

    // Fetch user to check if they exist and are pending approval
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ? AND is_verified = 0");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        if ($action === 'approve') {
            // Update is_verified = 1
            $update = $conn->prepare("UPDATE members SET is_verified = 1 WHERE id = ?");
            $update->bind_param("i", $member_id);
            if ($update->execute()) {
                $_SESSION['message'] = "User approved successfully.";
            } else {
                $_SESSION['error'] = "Failed to approve user.";
            }
        } elseif ($action === 'decline') {
            // Optionally delete or mark declined
            $delete = $conn->prepare("DELETE FROM members WHERE id = ?");
            $delete->bind_param("i", $user_id);
            if ($delete->execute()) {
                $_SESSION['message'] = "User declined and removed.";
            } else {
                $_SESSION['error'] = "Failed to decline user.";
            }
        } else {
            $_SESSION['error'] = "Invalid action.";
        }
    } else {
        $_SESSION['error'] = "User not found or already verified.";
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to admin panel
header("Location: ../admin_panel.php");
exit;
