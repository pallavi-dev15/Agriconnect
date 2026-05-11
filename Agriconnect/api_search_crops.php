<?php
session_start();
header('Content-Type: application/xml; charset=utf-8');

require 'db.php';

$search_query = $_GET['query'] ?? '';
$search_query = htmlspecialchars(trim($search_query));

if (empty($search_query)) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Search query is required');
    echo $response->asXML();
    exit();
}

// Farmer searches only their own crops
if ($_SESSION['usertype'] === 'farmer') {
    $farmer_id = $_SESSION['id'];
    $search_term = '%' . $search_query . '%';
    
    $stmt = $conn->prepare("SELECT id, crop_name, price, quantity, grade, location, description, image_url, created_at 
                            FROM crops 
                            WHERE farmer_id = ? AND (crop_name LIKE ? OR description LIKE ? OR location LIKE ?)
                            ORDER BY created_at DESC");
    $stmt->bind_param("isss", $farmer_id, $search_term, $search_term, $search_term);
} else {
    // Buyers search all crops
    $search_term = '%' . $search_query . '%';
    
    $stmt = $conn->prepare("SELECT id, farmer_id, crop_name, price, quantity, grade, location, description, image_url, created_at 
                            FROM crops 
                            WHERE crop_name LIKE ? OR description LIKE ? OR location LIKE ?
                            ORDER BY created_at DESC");
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
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
    
    if ($_SESSION['usertype'] === 'buyer') {
        $cropElement->addChild('farmer_id', htmlspecialchars($row['farmer_id']));
    }
}

echo $response->asXML();

$stmt->close();
$conn->close();
?>
