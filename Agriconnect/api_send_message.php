<?php
session_start();
require 'db.php';

header('Content-Type: application/xml; charset=utf-8');

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'buyer') {
    http_response_code(403);
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Not authorized');
    echo $response->asXML();
    exit();
}

$sender = $_SESSION['id'];
$farmer_id = intval($_POST['farmer_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($farmer_id <= 0 || $message === '') {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Farmer and message required');
    echo $response->asXML();
    exit();
}

// Ensure messages table exists
$res = $conn->query("SHOW TABLES LIKE 'messages'");
if (!$res || $res->num_rows == 0) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Messaging not set up on server');
    echo $response->asXML();
    exit();
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, farmer_id, message) VALUES (?, ?, ?)");
if (!$stmt) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database prepare failed');
    echo $response->asXML();
    exit();
}
$stmt->bind_param('iis', $sender, $farmer_id, $message);
if ($stmt->execute()) {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'true');
    $response->addChild('message', 'Message sent');
    echo $response->asXML();
} else {
    $response = new SimpleXMLElement('<?xml version="1.0"?><response/>');
    $response->addChild('success', 'false');
    $response->addChild('message', 'Failed to send message');
    echo $response->asXML();
}
$stmt->close();
$conn->close();
?>
