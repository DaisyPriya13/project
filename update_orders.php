<?php
header('Content-Type: application/json');

// Read order data from request
$orderData = json_decode(file_get_contents('php://input'), true);
if (!$orderData) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order data']);
    exit;
}

// Save to orders.json as before
$jsonFile = '../json/orders.json';
$orders = [];
if (file_exists($jsonFile)) {
    $ordersJson = file_get_contents($jsonFile);
    if ($ordersJson) {
        $orders = json_decode($ordersJson, true) ?: [];
    }
}
$orders[] = $orderData;
file_put_contents($jsonFile, json_encode($orders, JSON_PRETTY_PRINT));

// --- Save to MySQL database ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chosen_lamb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Prepare and insert order
$stmt = $conn->prepare("INSERT INTO orders (user_name, phone_number, email, address, products, total_price, payment_mode, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$productsJson = json_encode($orderData['products']);
$stmt->bind_param(
    "sssssdss",
    $orderData['user_name'],
    $orderData['phone_number'],
    $orderData['email'],
    $orderData['address'],
    $productsJson,
    $orderData['total_price'],
    $orderData['payment_mode'],
    $orderData['order_date']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order' => $orderData]);
} else {
    echo json_encode(['error' => 'Failed to save order in database']);
}

$stmt->close();
$conn->close();
?>
