<?php
session_start();
require_once "db.php";

// ✅ Restrict only Admin
// if ($_SESSION['role'] !== 'admin') { die("Access denied"); }

$message = "";

// ✅ Handle Add Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $role      = $_POST['role'];
    $password  = $_POST['password'];

    // ✅ Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin_panel (full_name, email, phone, role, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $phone, $role, $hashed_password);

    if ($stmt->execute()) {
        $message = "✅ Staff added successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Handle Delete
if (isset($_GET['delete'])) {
    $staff_id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM admin_panel WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    if ($stmt->execute()) {
        $message = "🗑️ Staff deleted successfully!";
    } else {
        $message = "❌ Error deleting staff: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Fetch All Staff
$result = $conn->query("SELECT staff_id, full_name, email, phone, role FROM admin_panel ORDER BY staff_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #003d6a;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }
        .success { color: green; }
        .error { color: red; }
        
        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
            margin-bottom: 30px;
        }
        form label {
            font-weight: bold;
            display: block;
            margin: 8px 0 4px;
        }
        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        form button {
            background: #003d6a;
            color: #fff;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        form button:hover {
            background: #005999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background: #003d6a;
            color: #fff;
        }
        tr:nth-child(even) { background: #f9f9f9; }
        td a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        td a.edit {
            background: #ffc107;
            color: #000;
        }
        td a.delete {
            background: #dc3545;
            color: #fff;
        }
        td a:hover {
            opacity: 0.85;
        }

        /* ✅ Responsive */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; }
            tr { margin-bottom: 15px; }
            th {
                display: none;
            }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>👨‍💼 Manage Staff</h2>
        <p class="message <?php echo strpos($message,'✅')!==false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>

        <!-- ✅ Add Staff Form -->
        <form method="POST">
            <input type="hidden" name="add_staff" value="1">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="relationship_officer">Relationship Officer</option>
                <option value="auditor">Auditor</option>
                <option value="manager">Manager</option>
                <option value="accountant">Accountant</option>
                <option value="admin">Admin</option>
                <option value="md">Managing Director</option>
            </select>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">➕ Add Staff</button>
        </form>

        <!-- ✅ Staff List -->
        <h3>All Staff</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?php echo $row['staff_id']; ?></td>
                    <td data-label="Full Name"><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td data-label="Role"><?php echo ucfirst($row['role']); ?></td>
                    <td data-label="Actions">
                        <a class="edit" href="edit_staff.php?staff_id=<?php echo $row['staff_id']; ?>">✏️ Edit</a> 
                        <a class="delete" href="?delete=<?php echo $row['staff_id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
