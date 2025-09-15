<?php
require 'db.php';
session_start();
// Check admin1 session here if needed

// Fetch top-up approvals pending at step 1
$sql = "SELECT a.id AS approval_id, s.* FROM approvals a
        JOIN savings_transactions s ON a.item_id = s.id
        WHERE a.item_type = 'top_up' AND a.step = 1 AND a.status = 'pending'";
$result = $conn->query($sql);

echo "<h3>Pending Top-up Approvals (Admin 1)</h3>";
while ($row = $result->fetch_assoc()) {
    echo "<p>
        Member ID: {$row['member_id']} <br>
        Amount: ₦" . number_format($row['amount_saved'], 2) . "<br>
        Reference: {$row['reference']}<br>
        <form method='POST'>
            <input type='hidden' name='approval_id' value='{$row['approval_id']}'>
            <button name='approve'>Acknowledge</button>
        </form>
    </p>";
}

if (isset($_POST['approve'])) {
    $approval_id = intval($_POST['approval_id']);

    $update = $conn->prepare("UPDATE approvals SET step = 2 WHERE id = ?");
    $update->bind_param("i", $approval_id);
    $update->execute();

    echo "<p>✅ Top-up acknowledged by Admin 1.</p>";
    echo "<meta http-equiv='refresh' content='1'>";
}
?>
