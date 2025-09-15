<?php
session_start();
require_once 'db.php';

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle confirm/reject actions
if ($id > 0 && in_array($action, ['confirm', 'reject'])) {
    $stmt = $conn->prepare("SELECT member_id, action_type, fd_id, amount FROM fd_withdrawals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if ($data) {
        $member_id = (int) $data['member_id'];
        $fd_id = (int) $data['fd_id'];
        $fd_amount = (float) $data['amount'];
        $action_type = $data['action_type'];

        if ($action === 'confirm') {
            $update = $conn->prepare("UPDATE fd_withdrawals SET status = 'confirmed', admin2_id = ?, confirmed_at = NOW() WHERE id = ?");
            $update->bind_param("ii", $admin2_id, $id);
            $update->execute();

            if ($action_type === 'withdraw') {
                $conn->prepare("UPDATE members SET savings_balance = savings_balance + ? WHERE id = ?")
                    ->bind_param("di", $fd_amount, $member_id)
                    ->execute();

                $conn->prepare("DELETE FROM fixed_deposits WHERE id = ? AND member_id = ?")
                    ->bind_param("ii", $fd_id, $member_id)
                    ->execute();

            } elseif ($action_type === 'rollover') {
                $conn->prepare("UPDATE fixed_deposits SET status = 'confirmed' WHERE id = ? AND member_id = ?")
                    ->bind_param("ii", $fd_id, $member_id)
                    ->execute();
            }
        } elseif ($action === 'reject') {
            $update = $conn->prepare("UPDATE fd_withdrawals SET status = 'rejected', admin2_id = ?, confirmed_at = NOW() WHERE id = ?");
            $update->bind_param("ii", $admin2_id, $id);
            $update->execute();
        }
    }
}

// Fetch pending requests
$query = "SELECT fw.id, fw.member_id, fw.action_type, fw.status, fw.created_at, m.full_name
          FROM fd_withdrawals fw
          JOIN members m ON fw.member_id = m.member_id
          WHERE fw.status = 'acknowledged'
          ORDER BY fw.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FD Withdrawals | Admin2</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 30px;
    }

    table {
      width: 95%;
      margin: auto;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    th, td {
      padding: 14px 16px;
      border-bottom: 1px solid #e0e0e0;
      text-align: center;
      font-size: 15px;
    }

    th {
      background: #007bff;
      color: #fff;
      text-transform: uppercase;
      font-size: 13px;
    }

    tr:hover {
      background: #f1f1f1;
    }

    a.btn {
      padding: 8px 12px;
      margin: 0 4px;
      text-decoration: none;
      border-radius: 4px;
      color: #fff;
      font-size: 13px;
      display: inline-block;
      transition: background 0.2s ease-in-out;
    }

    .confirm {
      background-color: #28a745;
    }

    .confirm:hover {
      background-color: #218838;
    }

    .reject {
      background-color: #dc3545;
    }

    .reject:hover {
      background-color: #c82333;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 40px;
    }

    .logout-btn {
      background-color: #007bff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    .logout-btn:active {
      background-color: #004080;
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 13px;
      }

      .logout-btn {
        width: 100%;
        text-align: center;
        padding: 12px;
      }

      .center-container {
        margin: 30px auto;
      }
    }
  </style>
</head>
<body>

<h2>FD Withdrawals / Rollover – Admin2 Confirmation</h2>

<table>
  <tr>
    <th>ID</th>
    <th>Member</th>
    <th>Action</th>
    <th>Status</th>
    <th>Date</th>
    <th>Decision</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$row['id'] ?></td>
      <td><?= htmlspecialchars($row['first_name']) ?></td>
      <td><?= ucfirst($row['action_type']) ?></td>
      <td><?= ucfirst($row['status']) ?></td>
      <td><?= htmlspecialchars($row['created_at']) ?></td>
      <td>
        <a class="btn confirm" href="?action=confirm&id=<?= (int)$row['id'] ?>" onclick="return confirm('Confirm this request?')">✅ Confirm</a>
        <a class="btn reject" href="?action=reject&id=<?= (int)$row['id'] ?>" onclick="return confirm('Reject this request?')">❌ Reject</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<div class="center-container">
  <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
