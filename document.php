<?php
session_start();
include 'db.php';

$adminRole = $_SESSION['adminRole']; // e.g., 'admin1' or 'admin2'

$result = $conn->query("SELECT * FROM documents ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Document Approvals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
            margin:0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px 14px;
            text-align: center;
        }
        th {
            background-color: #0077cc;
            color: #fff;
        }
        tr:hover {
            background-color: #f0f8ff;
        }
        a.button {
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background-color: #218838;
        }
        .acknowledged {
            color: green;
            font-weight: bold;
        }
        .pending {
            color: red;
            font-weight: bold;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #0077cc;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2 >📄 Uploaded Documents</h2>

<table>
    <tr>
        <th>ID</th>
        <th>File Name</th>
        <th>Type</th>
        <th>Uploaded At</th>
        <th>Admin1</th>
        <th>Admin2</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank"><?= htmlspecialchars($row['file_name']) ?></a></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= $row['uploaded_at'] ?></td>
            <td class="<?= $row['admin1_acknowledged'] ? 'acknowledged' : 'pending' ?>">
                <?= $row['admin1_acknowledged'] ? '✅ Yes' : '⏳ No' ?>
            </td>
            <td class="<?= $row['admin2_confirmed'] ? 'confirmed' : 'pending' ?>">
                <?= $row['admin2_confirmed'] ? '✅ Yes' : '⏳ No' ?>
            </td>
            <td>
                <?php if ($adminRole == 'admin1' && !$row['admin1_acknowledged']): ?>
                    <a class="button" href="acknowledge_document.php?admin=1&document_id=<?= $row['id'] ?>">Acknowledge</a>
                <?php elseif ($adminRole == 'admin2' && !$row['admin2_confirmed'] && $row['admin1_acknowledged']): ?>
                    <a class="button" href="confirm_document.php?admin=2&document_id=<?= $row['id'] ?>">Confirm</a>
                <?php else: ?>
                    ✅ Confirmed
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a class="back-link" href="<?= $adminRole ?>.php">🔙 Back to Admin Dashboard</a>

</body>
</html>
