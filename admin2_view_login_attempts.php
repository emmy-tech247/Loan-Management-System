<?php
// --- Production hardening ---
declare(strict_types=1);
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
]);

// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; img-src data: 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer");
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Error handling
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

$mysqli = require __DIR__ . "/db.php";
if (!$mysqli || !($mysqli instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection error.');
}
$mysqli->set_charset('utf8mb4');

// --- Fetch Failed Logins (only after 5 failed attempts per user) ---
$sql1 = "
    SELECT la.id, la.email, la.ip_address, la.attempt_time, la.success
    FROM login_attempts la
    INNER JOIN (
        SELECT email
        FROM login_attempts
        WHERE success = 0
        GROUP BY email
        HAVING COUNT(*) >= 5
    ) AS filtered ON la.email = filtered.email
    ORDER BY la.attempt_time DESC
    LIMIT 50
";
$result1 = $mysqli->query($sql1);

// --- Fetch Forgot Password Requests ---
$sql2 = "SELECT id, email, request_time 
         FROM password_resets 
         ORDER BY request_time DESC LIMIT 50";
$result2 = $mysqli->query($sql2);

$rows1 = ($result1 && $result1 instanceof mysqli_result) ? $result1 : null;
$rows2 = ($result2 && $result2 instanceof mysqli_result) ? $result2 : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin2 - Security Alerts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --pad: 12px;
            --br: 12px;
            --shadow: 0 4px 14px rgba(0,0,0,.08);
            --bg: #f9fafb;
            --card-bg: #fff;
            --primary: #2563eb;
            --failed: #dc2626;
            --success: #16a34a;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 20px; color:#1f2937; background: var(--bg); line-height: 1.5; }
        h1 { margin-bottom: 10px; font-size: 1.8rem; color: var(--primary); }
        h2 { margin-top: 40px; font-size: 1.3rem; color: #374151; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; background: var(--card-bg); border-radius: var(--br); overflow: hidden; box-shadow: var(--shadow); }
        th, td { padding: var(--pad); text-align: left; }
        th { background: #f3f4f6; font-weight: 600; font-size: 0.9rem; color: #374151; border-bottom: 2px solid #e5e7eb; }
        td { border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        tr:last-child td { border-bottom: 0; }
        tr:hover td { background: #f9fafb; transition: background 0.2s; }
        .failed { color: var(--failed); font-weight: 600; }
        .success { color: var(--success); font-weight: 600; }
        .empty { padding: 16px; color:#6b7280; font-style: italic; text-align: center; }
        @media (min-width: 721px) {
            tbody tr:nth-child(odd) td { background: #ffffff; }
            tbody tr:nth-child(even) td { background: #f3f4f6; }
            tbody tr:hover td { background: #e5e7eb; }
        }
        .wrap { word-break: break-word; }
        @media (max-width: 720px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead { display:none; }
            tr { margin: 0 0 15px 0; border:1px solid #e5e7eb; border-radius: var(--br); overflow:hidden; box-shadow: var(--shadow); background: var(--card-bg); }
            td { display: grid; grid-template-columns: 40% 60%; align-items: center; gap: 8px; border:0; border-bottom:1px solid #f3f4f6; }
            td::before { content: attr(data-label); font-weight: 600; color:#4b5563; }
            td:last-child { border-bottom:0; }
        }
        .center-container { display: flex; justify-content: center; align-items: center; margin: 80px; }
        .logout-btn { display: inline-block; background-color: #007bff; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 16px; font-weight: 600; transition: background-color 0.3s ease, transform 0.2s ease; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .logout-btn:hover { background-color: #0056b3; transform: translateY(-2px); }
        .logout-btn:active { background-color: #0056b3; transform: translateY(0); }
    </style>
</head>
<body>
    <h1>🔐 Security Monitoring (Managing Director)</h1>

    <h2>🚨 Failed Login Attempts (Only after 5 fails)</h2>
    <table aria-label="Failed Login Attempts">
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>IP Address</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($rows1 && $rows1->num_rows > 0): ?>
            <?php while ($row = $rows1->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= htmlspecialchars((string)$row['id']); ?></td>
                    <td data-label="Email" class="wrap"><?= htmlspecialchars((string)$row['email']); ?></td>
                    <td data-label="IP Address"><?= htmlspecialchars((string)$row['ip_address']); ?></td>
                    <td data-label="Time"><?= htmlspecialchars((string)$row['attempt_time']); ?></td>
                    <td data-label="Status" class="<?= (int)$row['success'] ? 'success' : 'failed'; ?>">
                        <?= (int)$row['success'] ? 'Successful' : 'Failed'; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td class="empty" colspan="5">No login attempts exceeding 5 fails found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h2>📩 Forgot Password Requests</h2>
    <table aria-label="Forgot Password Requests">
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Request Time</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($rows2 && $rows2->num_rows > 0): ?>
            <?php while ($row = $rows2->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= htmlspecialchars((string)$row['id']); ?></td>
                    <td data-label="Email" class="wrap"><?= htmlspecialchars((string)$row['email']); ?></td>
                    <td data-label="Request Time"><?= htmlspecialchars((string)$row['request_time']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td class="empty" colspan="3">No password reset requests found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="center-container">
      <a class="logout-btn" href="admin2.php">Back to Admin Dashboard</a>
    </div>
</body>
</html>
