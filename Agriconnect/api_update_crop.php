<?php
session_start();
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

// Only farmers can update crops
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$farmer_id = $_SESSION['id'];
$crop_id = intval($_POST['crop_id'] ?? 0);
$crop_name = trim($_POST['crop_name'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);
$grade = trim($_POST['grade'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validate inputs
if ($crop_id <= 0 || empty($crop_name) || $price <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Verify ownership
$check = $conn->prepare("SELECT farmer_id FROM crops WHERE id = ?");
if (!$check) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}
$check->bind_param('i', $crop_id);
$check->execute();
$result = $check->get_result();
$row = $result->fetch_assoc();
$check->close();

if (!$row || $row['farmer_id'] != $farmer_id) {
    echo json_encode(['success' => false, 'message' => 'Crop not found or unauthorized']);
    exit();
}

// Update crop
$stmt = $conn->prepare("UPDATE crops SET crop_name = ?, price = ?, quantity = ?, grade = ?, location = ?, description = ? WHERE id = ? AND farmer_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}
$stmt->bind_param('sdissii', $crop_name, $price, $quantity, $grade, $location, $description, $crop_id, $farmer_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Crop updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update crop']);
}
$stmt->close();
$conn->close();
?>
