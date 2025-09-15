
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    die("⛔ Unauthorized. Please login as Admin.");
}

$admin2_id = $_SESSION['adminId'];

if (isset($_GET['document_id']) && is_numeric($_GET['document_id'])) {
    $document_id = intval($_GET['document_id']);

    // Check if document exists and admin1 has already acknowledged
    $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ? AND admin1_acknowledged = 1");
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "⛔ Document not found or not yet confirmed by Admin 1.";
    } else {
        // Acknowledge by Admin 2
        $update = $conn->prepare("UPDATE documents 
            SET admin2_confirmed = 1, admin2_id = ?, admin2_confirmed_at = NOW() 
            WHERE id = ?");
        $update->bind_param("ii", $admin2_id, $document_id);

        if ($update->execute()) {
            $message = "✅ Admin 2 has confirmed the document successfully.";
        } else {
            $message = "❌ Failed to confirm document.";
        }
    }
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin 2 – Confirm Document</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        p.message {
            padding: 10px;
            font-weight: bold;
            color: #444;
            background-color: #e2e6ea;
            border-left: 5px solid #17a2b8;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 16px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .ack-btn {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .ack-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>📄 Documents Awaiting Admin 2 Confirmation</h2>

<?php if (!empty($message)): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php
$sql = "SELECT * FROM documents WHERE admin1_acknowledged = 1 AND admin2_confirmed = 0";
$result = $conn->query($sql);

if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Admin 1 Confirmation Time</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['title'] ?? 'Untitled') ?></td>
            <td><?= htmlspecialchars($row['confirmed_at'] ?? '-') ?></td>
            <td>
                <form method="GET" action="confirm_document.php" onsubmit="return confirm('Confirm this document?');">
                    <input type="hidden" name="document_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="ack-btn">Confirm Document</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>✅ No documents pending Admin 2 confirmation.</p>
<?php endif; ?>

</body>
</html>
