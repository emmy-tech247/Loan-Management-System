<?php
session_start();
include 'db.php';

if (!isset($_SESSION['member_id'])) {
    die("Unauthorized");
}

$member_id = $_SESSION['member_id'];
$purpose = trim($_POST['purpose']);
$signature_data = $_POST['signature_data'];
$upload_dir = 'uploads/signatures/';

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_path = "";

// 1️⃣ If signature is drawn on canvas
if (!empty($signature_data) && strpos($signature_data, 'data:image/png;base64,') === 0) {
    $signature_data = str_replace('data:image/png;base64,', '', $signature_data);
    $signature_data = str_replace(' ', '+', $signature_data);
    $decoded = base64_decode($signature_data);

    $file_name = 'signature_' . time() . '.png';
    $file_path = $upload_dir . $file_name;
    file_put_contents($file_path, $decoded);

}
// 2️⃣ If signature is uploaded as a file
elseif (!empty($_FILES['signature_file']['name'])) {
    $allowed_ext = ['png', 'jpg', 'jpeg'];
    $file_ext = strtolower(pathinfo($_FILES['signature_file']['name'], PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
        $file_name = 'signature_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        move_uploaded_file($_FILES['signature_file']['tmp_name'], $file_path);
    } else {
        die("❌ Invalid file type. Only PNG, JPG, or JPEG allowed.");
    }
} else {
    die("❌ No signature provided. Please draw or upload a signature.");
}

// 3️⃣ Save to DB if a file path was created
if ($file_path) {
    $stmt = $conn->prepare("INSERT INTO signatures (member_id, purpose, signature_path) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $member_id, $purpose, $file_path);
    $stmt->execute();

    echo "✅ Signature saved successfully.<br><a href='$file_path' target='_blank'>View Signature</a>";
} else {
    echo "❌ Failed to save signature.";
}
?>
