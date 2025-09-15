<?php
session_start();
include 'db.php';

if (isset($_GET['acknowledge_id'])) {
    $id = intval($_GET['acknowledge_id']);
    $stmt = $conn->prepare("UPDATE savings_transactions SET status = 'acknowledged', acknowledged_by = ? WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $admin1_id, $id);
    $stmt->execute();
    echo "✅ Acknowledged by Admin1.";
}
?>

<!-- Display Pending Receipts -->
<h3>Pending Manual Savings (for Admin1)</h3>
<?php
$result = $conn->query("SELECT * FROM savings_transactions WHERE status = 'pending'");
while ($row = $result->fetch_assoc()) {
    echo "<p>
        Member ID: {$row['member_id']} - ₦{$row['amount']}<br>
        <a href='{$row['receipt_file']}' target='_blank'>View Receipt</a><br>
        <a href='admin1_acknowledge_savings.php?acknowledge_id={$row['id']}'>✅ Acknowledge</a>
    </p><hr>";
}
?>
