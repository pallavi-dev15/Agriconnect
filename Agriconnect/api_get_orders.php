<?php
session_start();
header('Content-Type: application/xml; charset=utf-8');

require 'db.php';

// Get orders based on user type
$response = new SimpleXMLElement('<?xml version="1.0"?><response/>');

if ($_SESSION['usertype'] === 'farmer') {
    // Farmer sees orders from buyers
        $farmer_id = $_SESSION['id'];
        // Include buyer information so farmers can see customer name
        $stmt = $conn->prepare("SELECT o.id, o.crop_id, c.crop_name, o.quantity, o.total_price, 
                        o.order_status, o.order_date, o.delivery_address, o.notes,
                        o.buyer_id, u.username AS buyer_name
                    FROM orders o 
                    JOIN crops c ON o.crop_id = c.id
                    JOIN users u ON o.buyer_id = u.id
                    WHERE o.farmer_id = ? 
                    ORDER BY o.order_date DESC");
        $stmt->bind_param("i", $farmer_id);
} else if ($_SESSION['usertype'] === 'buyer') {
    // Buyer sees their own orders
    $buyer_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT o.id, c.crop_name, u.username as farmer_name, o.quantity, 
                                   o.total_price, o.order_status, o.order_date, o.delivery_address
                            FROM orders o 
                            JOIN crops c ON o.crop_id = c.id
                            JOIN users u ON o.farmer_id = u.id
                            WHERE o.buyer_id = ? 
                            ORDER BY o.order_date DESC");
    $stmt->bind_param("i", $buyer_id);
} else {
    $response->addChild('success', 'false');
    $response->addChild('message', 'Unauthorized access');
    echo $response->asXML();
    exit();
}

if (!$stmt->execute()) {
    $response->addChild('success', 'false');
    $response->addChild('message', 'Database error: ' . $conn->error);
    echo $response->asXML();
    exit();
}

$result = $stmt->get_result();
$response->addChild('success', 'true');
$ordersElement = $response->addChild('orders');

while ($row = $result->fetch_assoc()) {
    $orderElement = $ordersElement->addChild('order');
    foreach ($row as $key => $value) {
        $orderElement->addChild($key, htmlspecialchars($value));
    }
}

echo $response->asXML();

$stmt->close();
$conn->close();
?>
