<?php
session_start();
require_once 'db.php';

// Secure session check
if (!isset($_SESSION['member_id']) || !is_numeric($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = intval($_SESSION['member_id']);
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fd_id = intval($_POST['fd_id']);
    $action_type = $_POST['action_type'];
    $note = trim($_POST['note']);

    if (!in_array($action_type, ['withdraw', 'rollover'])) {
        $message = "❌ Invalid action selected.";
    } else {
        $status = 'pending';
        $stmt = $conn->prepare("INSERT INTO fd_withdrawals (fd_id, member_id, action_type, note, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $fd_id, $member_id, $action_type, $note, $status);

        if ($stmt->execute()) {
            $pending_status = $action_type === 'withdraw' ? 'pending_withdrawal' : 'pending_rollover';
            $update = $conn->prepare("UPDATE fixed_deposits SET status = ? WHERE id = ? AND member_id = ?");
            $update->bind_param("sii", $pending_status, $fd_id, $member_id);
            $update->execute();

            $message = "✅ Your request to *$action_type* has been submitted successfully.";
        } else {
            $message = "❌ Failed to submit your request. Please try again.";
        }
    }
}

// Fetch active fixed deposits
$stmt = $conn->prepare("SELECT id, amount_deposited, tenure_months, interest_rate, start_date FROM fixed_deposits WHERE member_id = ? AND status = 'active'");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fixed Deposit Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #004080;
            padding: 0 20px;
        }

        .navbar .left, .navbar .right {
            display: flex;
            align-items: center;
        }

        .navbar a {
            font-size: 16px;
            color: white;
            padding: 16px 25px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }

      

        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 20px;
            background-color: #fdfdfd;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"] {
            background: #004080;
            color: white;
            border: none;
            padding: 12px 15px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background: #0056b3;
        }

        

        .msg {
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .msg.success {
            background-color: #d4edda;
            color: #155724;
        }

        .msg.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        footer {
            text-align: center;
            padding: 30px;
            background-color: #004080;
            color: white;
            margin-top: 50px;
        }
        /* Disable all hover effects and pointer interactions on logo link */
.navbar .left .logo-link {          /* Disables hover and clicking */
  cursor: pointer !important;    /* Keeps arrow cursor */
}

.navbar .left .logo-link:hover {
  background: none !important;
  color: inherit !important;
  text-decoration: none !important;
  padding: 0 !important;
  border-radius: 0 !important;
}


  .navbar .left a:hover,
.navbar .left a:hover img {
  background-color: transparent !important;
  padding: 0 !important;
  margin: -50px -50px -20px -50px !important;
  border-radius: 0 !important;
  cursor: default;
}



        @media (max-width: 600px) {
            .navbar .right a {
                padding: 12px 15px;
                font-size: 14px;
                text-align: center;
            }

            .navbar .left a img {
                width: 60px;
                height: 60px;
                margin: -30px -30px -10px -30px;
            }

            .container {
                padding: 20px;
                margin: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="home.php" class="logo-link">
              <img src="images/logo2.png" alt="Logo" width="80" height="80" style="display: block; padding: 0; margin: -50px -50px -20px -50px;">
            </a>
 
        </div>
        <div class="right">
            <a href="member.php">Back</a>
            <a href="apply_fd.php">Apply For<br>Fixed Deposit</a>
            <a href="view_fd.php">View Fixed <br>Deposit Record</a>
            <a href="view_interest_fd.php">View Interest<br>Earned Overtime</a>
            <a href="apply_fd_action.php">Withdrawal or<br>Rollover Options</a>
            <a href="view_fd_actions.php">Fixed Deposit <br>Actions Log</a>
        </div>
    </div>

    <div class="container">
        <h2>Fixed Deposit Action</h2>

        <?php if ($message): ?>
            <div class="msg <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="fd_id">Select Fixed Deposit <span style="color: red;">*</span></label>
            <select name="fd_id" id="fd_id" required>
                <option value="">-- Choose Fixed Deposit --</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['id']) ?>">
                        #<?= htmlspecialchars($row['id']) ?> | ₦<?= number_format($row['amount_deposited']) ?> | <?= htmlspecialchars($row['tenure_months']) ?> months @ <?= htmlspecialchars($row['interest_rate']) ?>%
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="action_type">Action Type <span style="color: red;">*</span></label>
            <select name="action_type" id="action_type" required>
                <option value="">-- Choose Action --</option>
                <option value="withdraw">Withdraw</option>
                <option value="rollover">Rollover</option>
            </select>

            <label for="note">Instruction / Note</label>
            <textarea name="note" id="note" placeholder="Add message or instruction (optional)" rows="4"></textarea>

            <input type="submit" value="Submit Request">
        </form>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> DCG Cooperative Society Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
