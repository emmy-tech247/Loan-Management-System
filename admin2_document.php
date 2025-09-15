<?php
session_start();
require_once 'db.php';


$sql = "SELECT * FROM documents WHERE admin1_acknowledged = 1 AND admin2_confirmed = 0 ORDER BY acknowledged_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin 2 – Document Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px 16px;
            border: 1px solid #ccc;
            text-align: center;
            font-size: 16px;
        }

        th {
            background-color: #003366;
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

        .ack-btn {
            padding: 8px 14px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .ack-btn:hover {
            background-color: #218838;
        }

        .center-container {
            display: flex;
            justify-content: center;
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
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }

            .ack-btn, .logout-btn {
                font-size: 14px;
                padding: 10px 20px;
            }

            .center-container {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

<h2>📄 Documents Awaiting Admin 2 Confirmation</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Type</th>
            <th>Acknowledged At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank"><?= htmlspecialchars($row['file_name']) ?></a></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['acknowledged_at']) ?></td>
            <td>
                <form method="GET" action="confirm_document.php" onsubmit="return confirm('Are you sure you want to confirm this document?');">
                    <input type="hidden" name="document_id" value="<?= intval($row['id']) ?>">
                    <button type="submit" class="ack-btn">✅ Confirm</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center; font-size: 18px; color: green;">✅ No documents pending Admin 2 confirmation.</p>
<?php endif; ?>

<div class="center-container">
    <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
</div>

</body>
</html>
