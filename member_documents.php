<?php
include 'db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    die("⛔ Unauthorized.");
}

$member_id = $_SESSION['member_id'];
$stmt = $conn->prepare("SELECT * FROM documents WHERE member_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$res = $stmt->get_result();
?>

<style>
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px auto;
    font-family: Arial, sans-serif;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }

  th, td {
    padding: 12px 15px;
    border: 1px solid #ccc;
    text-align: center;
  }

  th {
    background-color: #004080;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  tr:hover {
    background-color: #f1f1f1;
  }

  a {
    color: #004080;
    text-decoration: none;
    font-weight: bold;
  }

  a:hover {
    text-decoration: underline;
  }
</style>

<table border="1" cellpadding="8">
  <tr>
    <th>Doc ID</th><th>File</th><th>Type</th><th>Uploaded</th><th>Admin1</th><th>Admin2</th>
  </tr>
<?php while ($r = $res->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($r['document_id']) ?></td>
    <td><a href="<?= $r['file_path'] ?>" target="_blank">View</a></td>
    <td><?= htmlspecialchars($r['type']) ?></td>
    <td><?= $r['uploaded_at'] ?></td>
    <td><?= $r['admin1_acknowledged'] ? "✅" : "Pending" ?></td>
    <td><?= $r['admin2_confirmed'] ? "✅" : "Pending" ?></td>
  </tr>
<?php endwhile; ?>
</table>
