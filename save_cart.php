<?php
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
$cartItem = $_POST;

// Validate POST data
if (!isset($cartItem['user_id'], $cartItem['user_name'], $cartItem['image'], $cartItem['name'], $cartItem['price'], $cartItem['quantity'])) {
    echo "Invalid cart data received. Missing required fields.";
    exit;
}

if ($cartItem) {
    // Insert cart item into the cart_items table
    $stmt = $conn->prepare("INSERT INTO cart_items (user_id, user_name, image, name, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    $stmt->bind_param(
        "isssdi",
        $cartItem['user_id'],
        $cartItem['user_name'],
        $cartItem['image'],
        $cartItem['name'],
        $cartItem['price'],
        $cartItem['quantity']
    );

    if ($stmt->execute()) {
        echo "Cart item saved successfully!";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid cart data received.";
}

$conn->close();
?>
