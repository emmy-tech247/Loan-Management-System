<?php
// loan_review_template.php
session_start();
require_once "db.php";

// ✅ Ensure reviewer is logged in with a role


// Define workflow order
$workflow_order = [
    "relationship_officer" => "auditor",
    "auditor" => "manager",
    "manager" => "accountant",
    "accountant" => "approved" // final
];

// Handle form submission (approve / reject / forward)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loan_id = (int) $_POST['loan_id'];
    $action = $_POST['action'] ?? "";

    if ($action === "approve" && isset($workflow_order[$role])) {
        $next_stage = $workflow_order[$role];
        $stmt = $conn->prepare("UPDATE loans SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE loan_id=?");
        $stmt->bind_param("ssi", $next_stage, $user_id, $loan_id);
        $stmt->execute();
        $message = "Loan forwarded to next stage ($next_stage).";
    } elseif ($action === "reject") {
        $stmt = $conn->prepare("UPDATE loans SET status='rejected', reviewed_by=?, reviewed_at=NOW() WHERE loan_id=?");
        $stmt->bind_param("si", $user_id, $loan_id);
        $stmt->execute();
        $message = "Loan rejected.";
    }
}

// Fetch pending loans for this role
$stmt = $conn->prepare("
    SELECT l.loan_id, l.loan_amount, l.tenure, l.loan_status, 
           m.full_name, m.phone_number, m.email
    FROM loans l
    JOIN members m ON l.member_id = m.member_id
    WHERE l.loan_status = ?
");
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Loan Review - <?= ucfirst($role) ?></title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 20px; }
    h2 { text-align: center; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #0077b6; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
    .approve { background: #28a745; color: white; }
    .reject { background: #dc3545; color: white; }
  </style>
</head>
<body>
  <h2>Loan Review - <?= ucfirst($role) ?></h2>

  <?php if (!empty($message)) : ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <table>
    <tr>
      <th>Loan ID</th>
      <th>Borrower</th>
      <th>Phone</th>
      <th>Email</th>
      <th>Amount (₦)</th>
      <th>Tenure (months)</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['loan_id'] ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= number_format($row['loan_amount'], 2) ?></td>
        <td><?= $row['tenure'] ?></td>
        <td>
          <form method="POST">
            <input type="hidden" name="loan_id" value="<?= $row['loan_id'] ?>">
            <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
            <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
