<?php
require 'db.php';
session_start();
// Check admin2 session here if needed

// Fetch top-up approvals pending at step 2
$sql = "SELECT a.id AS approval_id, s.* FROM approvals a
        JOIN savings_transactions s ON a.item_id = s.id
        WHERE a.item_type = 'top_up' AND a.step = 2 AND a.status = 'pending'";
$result = $conn->query($sql);

echo "<h3>Top-up Final Confirmation (Admin 2)</h3>";
while ($row = $result->fetch_assoc()) {
    echo "<p>
        Member ID: {$row['member_id']} <br>
        Amount: ₦" . number_format($row['amount_saved'], 2) . "<br>
        Reference: {$row['reference']}<br>
        <form method='POST'>
            <input type='hidden' name='approval_id' value='{$row['approval_id']}'>
            <input type='hidden' name='transaction_id' value='{$row['id']}'>
            <input type='hidden' name='amount_saved' value='{$row['amount_saved']}'>
            <input type='hidden' name='member_id' value='{$row['member_id']}'>
            <button name='confirm'>Confirm Top-up</button>
        </form>
    </p>";
}

if (isset($_POST['confirm'])) {
    $approval_id = intval($_POST['approval_id']);
    $transaction_id = intval($_POST['transaction_id']);

    // Step 1: Approve the approval request
    $updateApproval = $conn->prepare("UPDATE approvals SET status = 'approved' WHERE id = ?");
    $updateApproval->bind_param("i", $approval_id);
    $updateApproval->execute();

    // Step 2: Update transaction status
    $updateTxn = $conn->prepare("UPDATE savings_transactions SET status = 'approved' WHERE id = ?");
    $updateTxn->bind_param("i", $transaction_id);
    $updateTxn->execute();

    // Step 3: Optionally update member’s total savings balance
    // You must have a `savings_balance` column in `members` table
    $updateBal = $conn->prepare("UPDATE members SET savings_balance = savings_balance + ? WHERE id = ?");
    $updateBal->bind_param("di", $_POST['amount_saved'], $_POST['member_id']);
    $updateBal->execute();

    echo "<p>✅ Top-up confirmed and balance updated.</p>";
    echo "<meta http-equiv='refresh' content='1'>";
}
?>
