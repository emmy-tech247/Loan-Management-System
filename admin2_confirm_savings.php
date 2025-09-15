<?php
session_start();
include 'db.php';


if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);

    // Confirm approval
    $stmt = $conn->prepare("UPDATE savings_transactions SET status = 'approved', approved_by = ? WHERE id = ? AND status = 'acknowledged'");
    $stmt->bind_param("ii", $admin2_id, $id);
    $stmt->execute();
    echo "✅ Approved and savings updated.";
}
?>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f8;
    margin: 0;
    padding: 20px;
    color: #333;
  }

  h3 {
    text-align: center;
    margin-bottom: 30px;
    color: #0056b3;
  }

  .transaction {
    background: #fff;
    padding: 20px;
    margin: 0 auto 20px;
    max-width: 700px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    line-height: 1.6;
  }

  .transaction p {
    margin: 0 0 10px;
  }

  .transaction a {
    display: inline-block;
    margin-right: 10px;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    transition: background 0.3s ease;
  }

  .transaction a[href*='receipt_file'] {
    background-color: #17a2b8;
    color: white;
  }

  .transaction a[href*='approve_id'] {
    background-color: #28a745;
    color: white;
  }

  .transaction a:hover {
    opacity: 0.85;
  }

  hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 15px auto;
    max-width: 700px;
  }
</style>

<!-- Display Acknowledged Receipts -->
<h3>Admin2: Acknowledged Transactions to Approve</h3>
<?php
$result = $conn->query("SELECT * FROM savings_transactions WHERE status = 'acknowledged'");
while ($row = $result->fetch_assoc()) {
    echo "<div class='transaction'>
    <p><strong>Member ID:</strong> {$row['member_id']}<br>
    <strong>Amount:</strong> ₦" . number_format($row['amount_saved'], 2) . "</p>
    <a href='{$row['receipt_file']}' target='_blank'>📄 View Receipt</a>
    <a href='admin2_confirm_savings.php?approve_id={$row['id']}'>✅ Approve</a>
</div>";

}
?>
