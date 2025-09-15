<?php
include 'db.php';
$result = $conn->query("SELECT d.*, m.fullname FROM documents d JOIN members m ON d.member_id = m.id ORDER BY d.id DESC");
?>

<table>
  <tr>
    <th>Member</th>
    <th>Type</th>
    <th>File</th>
    <th>Admin1</th>
    <th>Admin2</th>
    <th>Action</th>
  </tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['fullname']) ?></td>
  <td><?= htmlspecialchars($row['type']) ?></td>
  <td><a href="<?= $row['file_path'] ?>" target="_blank">View</a></td>
  <td><?= $row['admin1_acknowledged'] ? '✅' : '❌' ?></td>
  <td><?= $row['admin2_confirmed'] ? '✅' : '❌' ?></td>
  <td>
    <?php if (!$row['admin1_acknowledged']): ?>
        <a href="acknowledge_document.php?document_id=<?= $row['id'] ?>">Acknowledge</a>
    <?php elseif (!$row['admin2_confirmed']): ?>
        <a href="confirm_document.php?document_id=<?= $row['id'] ?>">Confirm</a>
    <?php else: ?>
        ✔️ Completed
    <?php endif; ?>
  </td>
</tr>
<?php endwhile; ?>
</table>
