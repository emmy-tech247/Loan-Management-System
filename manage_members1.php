<?php
session_start();
include('db.php');


// Secure handling of POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'], $_POST['action'])) {
    $memberId = intval($_POST['member_id']);
    $action = $_POST['action'];

    $stmt = null;
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE members SET status = 'approved' WHERE member_id = ?");
    } elseif ($action === 'disapprove') {
        $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    } elseif ($action === 'suspend') {
        $stmt = $conn->prepare("UPDATE members SET status = 'suspended' WHERE id = ?");
    } elseif ($action === 'unsuspend') {
        $stmt = $conn->prepare("UPDATE members SET status = 'approved' WHERE id = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $memberId);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch members
$result = $conn->query("SELECT member_id, full_name, email, phone_number, status, created_at, payment_receipt 
FROM members ORDER BY created_at DESC");
$members = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Members</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 20px;
    }

    .login-box {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    .member-card {
      background: #fafafa;
      padding: 20px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      transition: transform 0.2s ease;
    }

    .member-card:hover {
      transform: scale(1.01);
    }

    .member-card p {
      margin: 6px 0;
      font-size: 15px;
    }

    .member-card strong {
      color: #333;
    }

    form {
      margin-top: 15px;
    }

    button {
      margin-right: 10px;
      padding: 8px 14px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .approve { background-color: #28a745; color: white; }
    .approve:hover { background-color: #218838; }

    .disapprove { background-color: #dc3545; color: white; }
    .disapprove:hover { background-color: #c82333; }

    .suspend { background-color: #ffc107; color: black; }
    .suspend:hover { background-color: #e0a800; }

    .unsuspend { background-color: #007bff; color: white; }
    .unsuspend:hover { background-color: #0056b3; }

    .center-container {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }

    .logout-btn {
      background-color: #007bff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease, transform 0.2s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .logout-btn:hover {
      background-color: #0056b3;
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .member-card {
        padding: 15px;
      }

      button {
        margin-top: 10px;
        width: 100%;
      }
    }

    .receipt-link {
  display: inline-block;
  margin-top: 6px;
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
}

.receipt-link:hover {
  text-decoration: underline;
}

  </style>
</head>
<body>
  <div class="login-box">
    <h2>👥 Manage Members</h2>

    <?php if (empty($members)): ?>
      <p>No members found.</p>
    <?php else: ?>
      <?php foreach ($members as $member): ?>
  <div class="member-card">
    <p><strong>Name:</strong> <?= htmlspecialchars($member['full_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($member['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($member['phone_number']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($member['status'])) ?></p>
    <p><strong>Joined:</strong> <?= date('d M Y', strtotime($member['created_at'])) ?></p>

    <!-- Show Payment Receipt -->
    <?php 
  $receiptPath = "uploads/" . $member['payment_receipt'];
?>
<a href="<?= htmlspecialchars($receiptPath) ?>" target="_blank" class="receipt-link">
  📄 View Payment Receipt
</a>

    <!-- Admin Actions -->
    <form method="POST" action="">
      <input type="hidden" name="member_id" value="<?= htmlspecialchars($member['member_id']) ?>">

      <?php if ($member['status'] === 'pending'): ?>
        <?php if (!empty($member['payment_receipt'])): ?>
          <button class="approve" name="action" value="approve">Approve</button>
        <?php else: ?>
          <button class="approve" disabled title="Upload receipt required">Approve</button>
        <?php endif; ?>
        <button class="disapprove" name="action" value="disapprove">Disapprove</button>
      <?php elseif ($member['status'] === 'approved'): ?>
        <button class="suspend" name="action" value="suspend">Suspend</button>
      <?php elseif ($member['status'] === 'suspended'): ?>
        <button class="unsuspend" name="action" value="unsuspend">Unsuspend</button>
      <?php endif; ?>
    </form>
  </div>
<?php endforeach; ?>

    <?php endif; ?>

    <div class="center-container">
      <a class="logout-btn" href="admin1.php">Back to Admin Dashboard</a>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
