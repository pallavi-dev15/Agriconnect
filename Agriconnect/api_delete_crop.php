<?php
session_start();
header('Content-Type: application/xml');

// Check if user is logged in and is a farmer
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Unauthorized access');
    echo $response->asXML();
    exit();
}

require 'db.php';

$crop_id = $_POST['crop_id'] ?? 0;
$crop_id = intval($crop_id);

if (empty($crop_id)) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Crop ID is required');
    echo $response->asXML();
    exit();
}

// Check if crop belongs to the current farmer
$farmer_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id FROM crops WHERE id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $crop_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Crop not found or unauthorized');
    echo $response->asXML();
    $stmt->close();
    $conn->close();
    exit();
}

// Delete the crop
$deleteStmt = $conn->prepare("DELETE FROM crops WHERE id = ? AND farmer_id = ?");
$deleteStmt->bind_param("ii", $crop_id, $farmer_id);

if ($deleteStmt->execute()) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'true');
    $response->addChild('message', 'Crop deleted successfully');
    echo $response->asXML();
} else {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Error deleting crop: ' . $deleteStmt->error);
    echo $response->asXML();
}

$stmt->close();
$deleteStmt->close();
$conn->close();
?>
