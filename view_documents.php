<?php
include 'db.php';
session_start();

if (!isset($_SESSION['adminId'])) {
    die("⛔ Unauthorized. Please login.");
}

$res = $conn->query("SELECT d.*, m.full_name FROM documents d JOIN members m ON d.member_id = m.id ORDER BY d.uploaded_at DESC");
?>
<table border="1" cellpadding="8">
  <tr>
    <th>ID</th><th>Doc. ID</th><th>Member</th><th>File</th><th>Type</th><th>Uploaded</th><th>Admin1</th><th>Admin2</th><th>Actions</th>
  </tr>
<?php while ($r = $res->fetch_assoc()): ?>
  <tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['document_id']) ?></td>
    <td><?= htmlspecialchars($r['full_name']) ?></td>
    <td><a href="<?= $r['file_path'] ?>" target="_blank">View</a></td>
    <td><?= htmlspecialchars($r['type']) ?></td>
    <td><?= $r['uploaded_at'] ?></td>
    <td><?= $r['admin1_acknowledged'] ? "✅" : "❌" ?></td>
    <td><?= $r['admin2_confirmed'] ? "✅" : "❌" ?></td>
    <td>
      <?php if (!$r['admin1_acknowledged']): ?>
        <a href="acknowledge_document.php?document_id=<?= $r['id'] ?>">Acknowledge Document</a>
      <?php elseif (!$r['admin2_confirmed']): ?>
        <a href="confirm_document.php?document_id=<?= $r['id'] ?>">Confirm Document </a>
      <?php else: ?>
        ✅ Both acknowledged
      <?php endif; ?>
    </td>
  </tr>
<?php endwhile; ?>
</table>
