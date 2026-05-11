<?php
session_start();
header('Content-Type: application/xml; charset=utf-8');

// Check if user is logged in and is a farmer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Unauthorized access');
    echo $response->asXML();
    exit();
}

require 'db.php';

$order_id = $_POST['order_id'] ?? 0;
$order_status = $_POST['status'] ?? '';

$order_id = intval($order_id);

if (empty($order_id) || empty($order_status)) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Missing required fields');
    echo $response->asXML();
    exit();
}

// Verify order belongs to this farmer
$farmer_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $order_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Order not found or unauthorized');
    echo $response->asXML();
    $stmt->close();
    $conn->close();
    exit();
}

// Update order status
$updateStmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ? AND farmer_id = ?");
$updateStmt->bind_param("sii", $order_status, $order_id, $farmer_id);

if ($updateStmt->execute()) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'true');
    $response->addChild('message', 'Order status updated successfully');
    echo $response->asXML();
} else {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Error updating order: ' . $updateStmt->error);
    echo $response->asXML();
}

$stmt->close();
$updateStmt->close();
$conn->close();
?>
