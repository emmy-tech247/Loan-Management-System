<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin1 - FD Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px 16px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background: #007bff;
      color: #fff;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .ack-btn {
      background: #28a745;
      color: #fff;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      transition: opacity 0.3s ease;
    }
    .ack-btn:hover {
      opacity: 0.85;
    }
    .acknowledged {
      color: green;
      font-weight: bold;
    }
    .confirmed {
      color: blue;
      font-weight: bold;
    }
    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
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
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 14px;
      }
      .logout-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
    }

    /* Neutralize logo hover if present */
    .logo:hover {
      transform: none !important;
      filter: none !important;
    }
  </style>
</head>
<body>

<h2>FD Withdrawal/Rollover Requests - Admin1</h2>

<table>
  <tr>
    <th>ID</th>
    <th>Member</th>
    <th>Action</th>
    <th>Status</th>
    <th>Date</th>
    <th>Acknowledge</th>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['first_name']) ?></td>
    <td><?= ucfirst($row['action_type']) ?></td>
    <td>
      <?php
        if ($row['status'] === 'acknowledged') {
          echo '<span class="acknowledged">Acknowledged</span>';
        } elseif ($row['status'] === 'confirmed') {
          echo '<span class="confirmed">Confirmed</span>';
        } else {
          echo ucfirst($row['status']);
        }
      ?>
    </td>
    <td><?= htmlspecialchars($row['created_at']) ?></td>
    <td>
      <?php if ($row['status'] === 'pending'): ?>
        <a class="ack-btn" href="acknowledge_fd_withdrawal.php?request_id=<?= $row['id'] ?>">Acknowledge</a>
      <?php else: ?>
        ✔
      <?php endif; ?>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<div class="center-container">
  <a class="logout-btn" href="admin1.php">🔙 Back to Admin Dashboard</a>
</div>

</body>
</html>
