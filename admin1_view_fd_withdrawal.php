<?php
session_start();
require_once 'db.php';

// ✅ Secure access for only logged-in admins

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// ✅ Sanitize ID and handle acknowledgment
if ($action === 'acknowledge' && filter_var($id, FILTER_VALIDATE_INT)) {
    $stmt = $conn->prepare("SELECT status FROM fd_withdrawals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultCheck = $stmt->get_result()->fetch_assoc();

    if ($resultCheck && $resultCheck['status'] === 'pending') {
        $update = $conn->prepare("UPDATE fd_withdrawals SET status = 'acknowledged', admin1_id = ?, acknowledged_at = NOW() WHERE id = ?");
        $update->bind_param("ii", $admin1_id, $id);
        $update->execute();
        header("Location: admin1_view_fd_withdrawal.php?success=1");
        exit();
    }
}

// ✅ Use prepared statement to fetch data
$query = "
    SELECT fw.id, fw.member_id, fw.action_type, fw.status, fw.created_at, m.full_name
    FROM fd_withdrawals fw
    JOIN members m ON fw.member_id = m.member_id
    WHERE fw.status IN ('pending', 'acknowledged')
    ORDER BY fw.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin1 - FD Acknowledgment</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      max-width: 1100px;
      margin: auto;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }

    th, td {
      padding: 12px 14px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background: #007bff;
      color: #fff;
    }

    a.btn {
      padding: 6px 10px;
      text-decoration: none;
      border-radius: 4px;
      color: #fff;
      font-weight: bold;
    }

    .acknowledge {
      background: #28a745;
    }

    .acknowledged-text {
      color: green;
      font-weight: bold;
    }

    .center-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }

    .logout-btn {
      display: inline-block;
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

    @media screen and (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead {
        display: none;
      }

      td {
        position: relative;
        padding-left: 50%;
        text-align: left;
      }

      td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
        color: #007bff;
      }

      .logout-btn {
        width: 90%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

<h2>FD Withdrawals / Rollover - Admin1 Acknowledgment</h2>

<?php if (isset($_GET['success'])): ?>
  <p style="text-align:center; color: green;">✅ Request acknowledged successfully.</p>
<?php endif; ?>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Member</th>
      <th>Action</th>
      <th>Status</th>
      <th>Date</th>
      <th>Decision</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td data-label="ID"><?= $row['id'] ?></td>
      <td data-label="Member"><?= htmlspecialchars($row['first_name']) ?></td>
      <td data-label="Action"><?= ucfirst(htmlspecialchars($row['action_type'])) ?></td>
      <td data-label="Status"><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
      <td data-label="Date"><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
      <td data-label="Decision">
        <?php if ($row['status'] === 'pending'): ?>
          <a class="btn acknowledge" href="?action=acknowledge&id=<?= $row['id'] ?>">✅ Acknowledge</a>
        <?php else: ?>
          <span class="acknowledged-text">Acknowledged</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<div class="center-container">
  <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
