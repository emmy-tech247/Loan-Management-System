<?php
session_start();
require_once 'db.php';

$result = $conn->query("SELECT * FROM documents ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Uploaded Documents</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 20px;
    }

    table {
      width: 95%;
      margin: 0 auto 30px;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 8px #003366;
      overflow-x: auto;
    }

    th, td {
      padding: 14px 16px;
      border: 1px solid #ccc;
      text-align: center;
      word-break: break-word;
    }

    th {
      background-color: #007bff;
      color: white;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #eef6ff;
    }

    a {
      color: #0047ab;
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }

    .status-ack {
      color: green;
      font-weight: bold;
    }

    .status-pending {
      color: #d9534f;
      font-weight: bold;
    }

    .center-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 40px;
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
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 14px;
      }

      .logout-btn {
        font-size: 14px;
        padding: 10px 20px;
      }

      h2 {
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      table {
        width: 100%;
      }

      th, td {
        padding: 10px;
      }

      h2 {
        font-size: 18px;
      }
    }
  </style>
</head>
<body>

<h2>📄 Uploaded Documents</h2>

<table>
  <tr>
    <th>ID</th>
    <th>File Name</th>
    <th>Type</th>
    <th>Uploaded At</th>
    <th>Action</th>
  </tr>

  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td>
        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">
          <?= htmlspecialchars($row['file_name']) ?>
        </a>
      </td>
      <td><?= htmlspecialchars($row['type']) ?></td>
      <td><?= htmlspecialchars($row['uploaded_at']) ?></td>
      <td>
        <?php if ($row['admin1_acknowledged']): ?>
          <span class="status-ack">✅ Acknowledged</span>
        <?php else: ?>
          <a class="status-pending" href="acknowledge_document.php?document_id=<?= intval($row['id']) ?>">Acknowledge</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<div class="center-container">
  <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
