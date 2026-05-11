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

// Get POST data
$crop_name = $_POST['crop_name'] ?? '';
$price = $_POST['price'] ?? 0;
$quantity = $_POST['quantity'] ?? 0;
$grade = $_POST['grade'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$image_url = '';

if (!empty($_FILES['image']['name'])) {
    $uploadDir = __DIR__ . '/uploads/crops/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $imageInfo = pathinfo($_FILES['image']['name']);
    $extension = strtolower($imageInfo['extension'] ?? '');
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($extension, $allowed, true)) {
        $targetName = uniqid('crop_', true) . '.' . $extension;
        $targetFile = $uploadDir . $targetName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image_url = 'uploads/crops/' . $targetName;
        }
    }
} else {
    $image_url = $_POST['image_url'] ?? '';
}

// Validate input
if (empty($crop_name) || empty($price) || empty($quantity)) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Missing required fields');
    echo $response->asXML();
    exit();
}

// Sanitize inputs
$crop_name = htmlspecialchars($crop_name);
$price = floatval($price);
$quantity = intval($quantity);
$grade = htmlspecialchars($grade);
$location = htmlspecialchars($location);
$description = htmlspecialchars($description);
$image_url = htmlspecialchars($image_url);
$farmer_id = $_SESSION['id'];

// Insert into database
$stmt = $conn->prepare("INSERT INTO crops (farmer_id, crop_name, price, quantity, grade, location, description, image_url) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database error: ' . $conn->error);
    echo $response->asXML();
    exit();
}

$stmt->bind_param("isdiisss", $farmer_id, $crop_name, $price, $quantity, $grade, $location, $description, $image_url);

if ($stmt->execute()) {
    $crop_id = $conn->insert_id;
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'true');
    $response->addChild('message', 'Crop added successfully');
    $response->addChild('crop_id', $crop_id);
    echo $response->asXML();
} else {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Error adding crop: ' . $stmt->error);
    echo $response->asXML();
}

$stmt->close();
$conn->close();
?>
