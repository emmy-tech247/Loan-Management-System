<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['member_id'];

// Fetch member's active fixed deposits
$stmt = $conn->prepare("SELECT id, amount_deposited, tenure_months, interest_rate FROM fixed_deposits WHERE member_id = ? AND status = 'active'");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fixed Deposit Action</title>
    <style>
        form {
            max-width: 600px;
            margin: 40px auto;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #ccc;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        select, textarea, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        input[type="submit"] {
            background: #0275d8;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

<form method="POST" action="apply_fd_action_process.php">
    <label for="fd_id"><strong>Select Fixed Deposit <span style="color: red;">*</span></strong></label>
    <select name="fd_id" id="fd_id" required>
        <label>-- Choose Fixed Deposit --</label>
        <?php while ($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>">
                #<?= $row['id'] ?> | ₦<?= number_format($row['amount_deposited']) ?> | <?= $row['tenure_months'] ?> months @ <?= $row['interest_rate'] ?>%
            </option>
        <?php endwhile; ?>
    </select>

    <label for="action_type"><strong>Action Type <span style="color: red;">*</span></strong></label>
    <select name="action_type" id="action_type" >
        <option value="">-- Choose Action --</option>
        <option value="withdraw">Withdraw</option>
        <option value="rollover">Rollover</option>
    </select>

    <label for="note"><strong>Instruction / Note</strong></label>
    <textarea name="note" id="note" placeholder="Add message or instruction (optional)" rows="4"></textarea>

    <input type="submit" value="Submit Request">
</form>

</body>
</html>
