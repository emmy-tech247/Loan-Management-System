<?php
include 'db.php';
session_start();

if (!isset($_SESSION['member_id'])) {
    die("⛔ Unauthorized. Please login.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $member_id = $_SESSION['member_id'];
    $file_name = basename($_FILES['document']['name']);
    $file_tmp = $_FILES['document']['tmp_name'];
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    $file_path = $upload_dir . $file_name;
    $type = $_POST['type'] ?? '';

    $document_id = 'DOC' . strtoupper(uniqid());

    if (move_uploaded_file($file_tmp, $file_path)) {
        $stmt = $conn->prepare("INSERT INTO documents (document_id, member_id, file_name, file_path, type, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sisss", $document_id, $member_id, $file_name, $file_path, $type);

        if ($stmt->execute()) {
            header('Location: member_documents.php');
            exit();
        } else {
            echo "❌ Upload failed: " . $stmt->error;
        }
    } else {
        echo "❌ File upload failed.";
    }
}
?>
