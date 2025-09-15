<?php
require 'db.php';
$res = $conn->query("SELECT a.action, a.timestamp, ad.username FROM audit_trail a
                     JOIN admins ad ON a.admin_id = ad.id ORDER BY a.timestamp DESC");

$audit = [];
while ($row = $res->fetch_assoc()) {
    $audit[] = $row;
}

echo json_encode($audit);
?>
