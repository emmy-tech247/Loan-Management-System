<?php
$result = $conn->query("SELECT * FROM members");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['status']}</td>
        <td>
            <form action='php/approve_member.php' method='POST'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <button>Approve</button>
            </form>
            <button onclick='suspendMember({$row['id']})'>Suspend</button>
            <button onclick='sendMessage({$row['id']})'>Send SMS</button>
        </td>
    </tr>";
}
?>