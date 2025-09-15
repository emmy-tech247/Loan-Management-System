<?php
session_start();
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approval_id'], $_POST['fd_id'], $_POST['action'])) {
    $approval_id = (int)$_POST['approval_id'];
    $fd_id       = (int)$_POST['fd_id'];
    $action      = $_POST['action'];

    if ($action === 'confirm') {
        // Approve FD (step 2)
        $conn->begin_transaction();

        try {
            // Update approval status
            $stmt = $conn->prepare("UPDATE approvals SET status = 'confirmed', approved_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $approval_id);
            $stmt->execute();
            $stmt->close();

            // Update FD record
            $stmt = $conn->prepare("UPDATE fixed_deposits SET status = 'confirmed' WHERE id = ?");
            $stmt->bind_param("i", $fd_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $_SESSION['message'] = "✅ Fixed Deposit confirmed successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "❌ Error confirming Fixed Deposit.";
        }
    }
}
header("Location: confirm_fd_action.php"); // back to FD confirm page
exit;
