<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    die("Unauthorized");
}

$result = $conn->query("SELECT s.*, m.fullname FROM savings_transactions s JOIN members m ON s.member_id = m.id ORDER BY s.created_at DESC");
?>

<h2>💰 Manual Topups Transactions</h2>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
}
form {
    padding: 10px;
    border: 1px solid #ccc;
    width: 300px;
}
input, button {
    display: block;
    margin-top: 10px;
    width: 100%;
    padding: 8px;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
}
th, td {
    padding: 10px;
    border: 1px solid #ccc;
}
th {
    background-color: #f4f4f4;
}
a {
    color: blue;
    text-decoration: none;
}
</style>


<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Member</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Receipt</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td>₦<?= number_format($row['amount_saved'], 2) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><a href="<?= $row['receipt_file'] ?>" target="_blank">View</a></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                    <a href="admin1_acknowledge_savings.php?id=<?= $row['id'] ?>">✅ Acknowledge</a>
                <?php elseif ($row['status'] == 'acknowledged'): ?>
                    <a href="admin2_confirm_savings.php?id=<?= $row['id'] ?>">✔️ Confirm</a>
                <?php else: ?>
                    ✅ Completed
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
