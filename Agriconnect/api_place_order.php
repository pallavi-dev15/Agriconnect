<?php
session_start();
header('Content-Type: application/xml; charset=utf-8');

// Check if user is logged in and is a buyer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'buyer') {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Unauthorized access');
    echo $response->asXML();
    exit();
}

require 'db.php';

$crop_id = $_POST['crop_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 0;
$delivery_address = $_POST['delivery_address'] ?? '';
$notes = $_POST['notes'] ?? '';

$crop_id = intval($crop_id);
$quantity = intval($quantity);

if (empty($crop_id) || empty($quantity) || empty($delivery_address)) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Missing required fields');
    echo $response->asXML();
    exit();
}

// Get crop details
$stmt = $conn->prepare("SELECT id, farmer_id, price, quantity FROM crops WHERE id = ?");
$stmt->bind_param("i", $crop_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Crop not found');
    echo $response->asXML();
    $stmt->close();
    $conn->close();
    exit();
}

$crop = $result->fetch_assoc();
$farmer_id = $crop['farmer_id'];
$available_quantity = $crop['quantity'];
$price = $crop['price'];

// Check if enough quantity available
if ($quantity > $available_quantity) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Insufficient quantity available. Available: ' . $available_quantity);
    echo $response->asXML();
    $stmt->close();
    $conn->close();
    exit();
}

$buyer_id = $_SESSION['id'];
$total_price = $price * $quantity;
$delivery_address = htmlspecialchars($delivery_address);
$notes = htmlspecialchars($notes);

// Insert order
$orderStmt = $conn->prepare("INSERT INTO orders (crop_id, buyer_id, farmer_id, quantity, total_price, delivery_address, notes, order_status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");

if (!$orderStmt) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database error: ' . $conn->error);
    echo $response->asXML();
    $stmt->close();
    $conn->close();
    exit();
}

$orderStmt->bind_param("iiidiss", $crop_id, $buyer_id, $farmer_id, $quantity, $total_price, $delivery_address, $notes);

if ($orderStmt->execute()) {
    $order_id = $conn->insert_id;
    
    // Update crop quantity
    $new_quantity = $available_quantity - $quantity;
    $updateStmt = $conn->prepare("UPDATE crops SET quantity = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $new_quantity, $crop_id);
    $updateStmt->execute();
    
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'true');
    $response->addChild('message', 'Order placed successfully');
    $response->addChild('order_id', $order_id);
    $response->addChild('total_price', $total_price);
    echo $response->asXML();
    
    $updateStmt->close();
} else {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Error placing order: ' . $orderStmt->error);
    echo $response->asXML();
}

$stmt->close();
$orderStmt->close();
$conn->close();
?>
