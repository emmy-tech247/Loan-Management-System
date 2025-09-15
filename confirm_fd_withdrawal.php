<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    die("❌ Unauthorized access");
}

$admin2_id = $_SESSION['adminId'];

if (!isset($_GET['confirm_id'])) {
    die("❌ No confirm_id received.");
}

$confirm_id = intval($_GET['confirm_id']);

// Ensure the record exists and is acknowledged
$stmt = $conn->prepare("SELECT status FROM fd_withdrawals WHERE id = ?");
$stmt->bind_param("i", $confirm_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Invalid request ID.");
}

$row = $result->fetch_assoc();

if ($row['status'] !== 'acknowledged') {
    die("⚠️ Request is not yet acknowledged by Admin1.");
}

// Confirm it
$update = $conn->prepare("UPDATE fd_withdrawals SET status = 'confirmed', admin2_id = ? WHERE id = ?");
$update->bind_param("ii", $admin2_id, $confirm_id);

if ($update->execute()) {
    header("Location: admin2_fd_confirm.php?success=1");
    exit();
} else {
    die("❌ Failed to confirm.");
}
?>
