<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Only buyers can save items
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'buyer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$buyer_id = $_SESSION['id'];
$crop_id = intval($_POST['crop_id'] ?? 0);

if ($crop_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid crop']);
    exit();
}

// Check if saved_items table exists
$res = $conn->query("SHOW TABLES LIKE 'saved_items'");
if (!$res || $res->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Saved items not set up on server']);
    exit();
}

// Check if already saved
$stmt = $conn->prepare("SELECT id FROM saved_items WHERE buyer_id = ? AND crop_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}
$stmt->bind_param('ii', $buyer_id, $crop_id);
$stmt->execute();
$result = $stmt->get_result();
$is_saved = $result->num_rows > 0;
$stmt->close();

// Toggle saved status
if ($is_saved) {
    // Remove from saved
    $stmt = $conn->prepare("DELETE FROM saved_items WHERE buyer_id = ? AND crop_id = ?");
    $stmt->bind_param('ii', $buyer_id, $crop_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'saved' => false, 'message' => 'Removed from saved items']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove']);
    }
    $stmt->close();
} else {
    // Add to saved
    $stmt = $conn->prepare("INSERT INTO saved_items (buyer_id, crop_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $buyer_id, $crop_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'saved' => true, 'message' => 'Added to saved items']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save']);
    }
    $stmt->close();
}

$conn->close();
?>
