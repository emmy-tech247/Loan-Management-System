<?php
session_start();
include('db.php');

// Ensure only Admin2 can access

// Date filter logic
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "
    SELECT log_id, user_id, role, action, ip_address, created_at
    FROM admin1_activity_logs
    WHERE role = 'admin1'
";

// Apply date range if selected
if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND DATE(created_at) BETWEEN '" . $conn->real_escape_string($start_date) . "' 
                AND '" . $conn->real_escape_string($end_date) . "'";
}

$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin1 Activity Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile scaling -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            background-color: #f4f6f9;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        form label {
            font-weight: bold;
            color: #555;
            font-size: 0.9rem;
        }
        form input, form button, form a {
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        form a {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }
        form a:hover {
            background-color: #5a6268;
        }
        .table-container {
            overflow-x: auto; /* Horizontal scroll for mobile */
            background: white;
            border-radius: 8px;
            padding: 6px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        table.dataTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        table.dataTable thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
            white-space: nowrap;
        }
        table.dataTable tbody td {
            text-align: center;
            padding: 8px;
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 1.2rem;
            }
            form label, form input, form button, form a {
                font-size: 0.8rem;
            }
            table.dataTable thead th, table.dataTable tbody td {
                font-size: 0.8rem;
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <h1>Admin1 Activity Logs</h1>

    <!-- Date Range Filter Form -->
    <form method="GET">
        <label>Start Date:</label>
        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        <label>End Date:</label>
        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        <button type="submit">Filter</button>
        <a href="admin1_logs.php">Reset</a>
    </form>

    <div class="table-container">
        <table id="logsTable" class="display">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User ID</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['log_id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo $row['ip_address']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({
                "pageLength": 10,
                "responsive": true
            });
        });
    </script>
</body>
</html>
