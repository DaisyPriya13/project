<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chosen_lamb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit;
}

$user_id = $input['user_id'];
$user_name = $input['user_name'];
$phone_number = $input['phone_number'];
$email = $input['email'];
$address = $input['address'];
$payment_mode = $input['payment_mode'];

// Fetch cart items for the user
$sql = "SELECT * FROM cart_items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch order product data from order.json
    $orderJsonPath = __DIR__ . '/order.json';
    if (!file_exists($orderJsonPath)) {
        http_response_code(500);
        echo json_encode(['message' => 'Order data file not found']);
        exit;
    }

    $orderJsonData = json_decode(file_get_contents($orderJsonPath), true);
    if (!$orderJsonData) {
        http_response_code(500);
        echo json_encode(['message' => 'Invalid order data file']);
        exit;
    }

    // Separate products with and without customText
    $customProducts = [];
    $normalProducts = [];
    if (isset($orderJsonData['products']) && is_array($orderJsonData['products'])) {
        foreach ($orderJsonData['products'] as $product) {
            if (!empty($product['customText']) || !empty($product['customText1']) || !empty($product['customText2'])) {
                $customProducts[] = $product;
            } else {
                $normalProducts[] = $product;
            }
        }
    }

    // Insert normal products into orders table (if any)
    if (count($normalProducts) > 0) {
        $orderJsonData['products'] = $normalProducts;
        $productDetails = json_encode($orderJsonData);

        $orderStmt = $conn->prepare("INSERT INTO orders (user_id, user_name, phone_number, email, address, payment_mode, products, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $orderStmt->bind_param("issssss", $user_id, $user_name, $phone_number, $email, $address, $payment_mode, $productDetails);
        $orderStmt->execute();
        $orderStmt->close();
    }

    // Insert custom products into customization table (if any)
    foreach ($customProducts as $product) {
        $customText = '';
        if (!empty($product['customText'])) {
            $customText = $product['customText'];
        } elseif (!empty($product['customText1']) || !empty($product['customText2'])) {
            $customText = trim(($product['customText1'] ?? '') . ' | ' . ($product['customText2'] ?? ''), ' |');
        }
        $prodName = isset($product['name']) ? $product['name'] : '';
        if ($customText !== '' && $prodName !== '') {
            $stmtCustomization = $conn->prepare("INSERT INTO customization (user_id, product_name, custom_text) VALUES (?, ?, ?)");
            $stmtCustomization->bind_param("iss", $user_id, $prodName, $customText);
            $stmtCustomization->execute();
            $stmtCustomization->close();
        }
    }

    // Clear the cart for the user
    $clearCartStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clearCartStmt->bind_param("i", $user_id);
    $clearCartStmt->execute();

    echo json_encode(['message' => 'Order placed successfully']);
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Cart is empty']);
}

$stmt->close();
$conn->close();
?>