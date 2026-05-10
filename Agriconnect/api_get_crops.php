<?php
session_start();
header('Content-Type: application/xml');

require 'db.php';

// Get crops for current farmer (if farmer) or all crops (if buyer)
if ($_SESSION['usertype'] === 'farmer') {
    $farmer_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT id, crop_name, price, quantity, grade, location, description, image_url, created_at 
                            FROM crops WHERE farmer_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $farmer_id);
} else {
    // For buyers - get all crops from all farmers
    $farmer_filter = $_GET['farmer_id'] ?? '';
    
    if (!empty($farmer_filter)) {
        $farmer_filter = intval($farmer_filter);
        $stmt = $conn->prepare("SELECT id, farmer_id, crop_name, price, quantity, grade, location, description, image_url, created_at 
                                FROM crops WHERE farmer_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $farmer_filter);
    } else {
        $stmt = $conn->prepare("SELECT id, farmer_id, crop_name, price, quantity, grade, location, description, image_url, created_at 
                                FROM crops ORDER BY created_at DESC");
    }
}

if (!$stmt->execute()) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database error: ' . $conn->error);
    echo $response->asXML();
    exit();
}

$result = $stmt->get_result();

$response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
$response->addChild('success', 'true');
$cropsElement = $response->addChild('crops');

while ($row = $result->fetch_assoc()) {
    $cropElement = $cropsElement->addChild('crop');
    $cropElement->addChild('id', htmlspecialchars($row['id']));
    $cropElement->addChild('crop_name', htmlspecialchars($row['crop_name']));
    $cropElement->addChild('price', htmlspecialchars($row['price']));
    $cropElement->addChild('quantity', htmlspecialchars($row['quantity']));
    $cropElement->addChild('grade', htmlspecialchars($row['grade']));
    $cropElement->addChild('location', htmlspecialchars($row['location']));
    $cropElement->addChild('description', htmlspecialchars($row['description']));
    $cropElement->addChild('image_url', htmlspecialchars($row['image_url']));
    $cropElement->addChild('created_at', htmlspecialchars($row['created_at']));
    
    // Add farmer_id for buyer view
    if ($_SESSION['usertype'] === 'buyer' || !empty($_GET['farmer_id'])) {
        $cropElement->addChild('farmer_id', htmlspecialchars($row['farmer_id'] ?? $_SESSION['id']));
    }
}

echo $response->asXML();

$stmt->close();
$conn->close();
?>
