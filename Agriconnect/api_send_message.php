<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'buyer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$sender = $_SESSION['id'];
$farmer_id = intval($_POST['farmer_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($farmer_id <= 0 || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Farmer and message required']);
    exit();
}

// Ensure messages table exists
$res = $conn->query("SHOW TABLES LIKE 'messages'");
if (!$res || $res->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Messaging not set up on server']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, farmer_id, message) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
    exit();
}
$stmt->bind_param('iis', $sender, $farmer_id, $message);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
$stmt->close();
$conn->close();
?>
