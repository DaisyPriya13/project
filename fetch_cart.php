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

// Get user_id from GET request
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    // Verify if user_id exists in the users table
    $verifyStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
    $verifyStmt->bind_param("i", $user_id);
    $verifyStmt->execute();
    $verifyStmt->bind_result($userExists);
    $verifyStmt->fetch();
    $verifyStmt->close();

    if ($userExists > 0) {
        // Fetch cart items for the user
        $stmt = $conn->prepare("SELECT id, user_id, user_name, image, name, price, quantity FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $cartItems = $result->fetch_all(MYSQLI_ASSOC);

            // Return cart items as JSON
            echo json_encode($cartItems);
        } else {
            echo json_encode(["error" => "Error fetching cart items: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "User ID does not exist."]);
    }
} else {
    echo json_encode(["error" => "Invalid user ID."]);
}

$conn->close();
?>
