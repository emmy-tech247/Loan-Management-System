<?php
session_start();
if ($_SESSION['role'] !== 'staff') {
    header("Location: ../index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Assist Members</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-box">
    <h2>Assist Members</h2>
    <p>Use this page to search members, send messages, or call them.</p>
    <!-- Implement member search and messaging -->
    <form action="assist_members.php" method="POST">
      <input type="text" name="search" placeholder="Search member by name or email" required>
      <button type="submit">Search</button>
    </form>
    <a href="staff.php">← Back to Dashboard</a>
  </div>
</body>
</html>
