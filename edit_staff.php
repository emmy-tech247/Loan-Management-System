<?php
session_start();
require_once "db.php";

// ✅ Restrict only Admin
// if ($_SESSION['role'] !== 'admin') { die("Access denied"); }

$message = "";

// ✅ Get staff ID from URL
if (!isset($_GET['staff_id']) || !is_numeric($_GET['staff_id'])) {
    die("Invalid staff ID.");
}
$staff_id = (int) $_GET['staff_id'];

// ✅ Fetch staff details
$stmt = $conn->prepare("SELECT staff_id, full_name, email, phone, role FROM admin_panel WHERE staff_id=?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

if (!$staff) {
    die("Staff not found.");
}

// ✅ Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $role      = $_POST['role'];
    $password  = $_POST['password'];

    if (!empty($password)) {
        // ✅ Update with password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin_panel SET full_name=?, email=?, phone=?, role=?, password_hash=? WHERE staff_id=?");
        $stmt->bind_param("sssssi", $full_name, $email, $phone, $role, $hashed_password, $staff_id);
    } else {
        // ✅ Update without password
        $stmt = $conn->prepare("UPDATE admin_panel SET full_name=?, email=?, phone=?, role=? WHERE staff_id=?");
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $role, $staff_id);
    }

    if ($stmt->execute()) {
        $message = "✅ Staff updated successfully!";
        // refresh staff details after update
        $stmt->close();
        $stmt = $conn->prepare("SELECT staff_id, full_name, email, phone, role FROM admin_panel WHERE staff_id=?");
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $staff = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 600px;
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
        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #6c757d;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
        }
        .back-link:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Edit Staff</h2>
        <p class="message <?php echo strpos($message,'✅')!==false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>

        <form method="POST">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($staff['full_name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($staff['phone']); ?>" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="relationship_officer" <?php if($staff['role']=='relationship_officer') echo 'selected'; ?>>Relationship Officer</option>
                <option value="auditor" <?php if($staff['role']=='auditor') echo 'selected'; ?>>Auditor</option>
                <option value="manager" <?php if($staff['role']=='manager') echo 'selected'; ?>>Manager</option>
                <option value="accountant" <?php if($staff['role']=='accountant') echo 'selected'; ?>>Accountant</option>
                <option value="admin" <?php if($staff['role']=='admin') echo 'selected'; ?>>Admin</option>
                <option value="md" <?php if($staff['role']=='md') echo 'selected'; ?>>Managing Director</option>
            </select>

            <label>New Password (leave blank if not changing):</label>
            <input type="password" name="password">

            <button type="submit">💾 Save Changes</button>
        </form>
        <a href="admin_add_staff.php" class="back-link">⬅️ Back to Staff List</a>
    </div>
</body>
</html>
