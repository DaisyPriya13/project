<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'chosen_lamb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product details from POST request
$name = $conn->real_escape_string($_POST['name']);
$scent = $conn->real_escape_string($_POST['scent']);
$price = (float)$_POST['price'];
$old_price = (float)$_POST['old_price'];
$discount_percentage = (float)$_POST['discount_percentage'];
$description = $conn->real_escape_string($_POST['description']);
$image_url = $conn->real_escape_string($_POST['image_url']);

// Insert product into the database
$sql = "INSERT INTO products (name, scent, price, old_price, discount_percentage, description, image_url)
        VALUES ('$name', '$scent', $price, $old_price, $discount_percentage, '$description', '$image_url')";

if ($conn->query($sql) === TRUE) {
    echo "Product stored successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
