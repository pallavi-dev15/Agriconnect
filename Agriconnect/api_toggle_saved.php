<?php
session_start();
require 'db.php';

header('Content-Type: application/xml; charset=utf-8');

// Only buyers can save items
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'buyer') {
    http_response_code(403);
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Not authorized');
    echo $response->asXML();
    exit();
}

$buyer_id = $_SESSION['id'];
$crop_id = intval($_POST['crop_id'] ?? 0);

if ($crop_id <= 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Invalid crop');
    echo $response->asXML();
    exit();
}

// Check if saved_items table exists
$res = $conn->query("SHOW TABLES LIKE 'saved_items'");
if (!$res || $res->num_rows == 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Saved items not set up on server');
    echo $response->asXML();
    exit();
}

// Check if already saved
$stmt = $conn->prepare("SELECT id FROM saved_items WHERE buyer_id = ? AND crop_id = ?");
if (!$stmt) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database error');
    echo $response->asXML();
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
        $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $response->addChild('success', 'true');
        $response->addChild('saved', 'false');
        $response->addChild('message', 'Removed from saved items');
        echo $response->asXML();
    } else {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $response->addChild('success', 'false');
        $response->addChild('message', 'Failed to remove');
        echo $response->asXML();
    }
    $stmt->close();
} else {
    // Add to saved
    $stmt = $conn->prepare("INSERT INTO saved_items (buyer_id, crop_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $buyer_id, $crop_id);
    if ($stmt->execute()) {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $response->addChild('success', 'true');
        $response->addChild('saved', 'true');
        $response->addChild('message', 'Added to saved items');
        echo $response->asXML();
    } else {
        $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
        $response->addChild('success', 'false');
        $response->addChild('message', 'Failed to save');
        echo $response->asXML();
    }
    $stmt->close();
}

$conn->close();
?>
