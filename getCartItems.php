<?php
header('Content-Type: application/json');

// Simulated cart items (replace this with database logic later)
$cartItems = [
    [
        "id" => 1,
        "name" => "Lavender Candle",
        "scent" => "Lavender",
        "price" => 15.99,
        "picture" => "../images/lavender-candle.jpg"
    ],
    [
        "id" => 2,
        "name" => "Vanilla Candle",
        "scent" => "Vanilla",
        "price" => 12.99,
        "picture" => "../images/vanilla-candle.jpg"
    ]
];

// Return the cart items as JSON
echo json_encode($cartItems);
?>
