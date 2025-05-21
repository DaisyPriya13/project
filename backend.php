<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chosen_lamb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['login_password'])) {
        $email = $_POST['email'];
        $password = $_POST['login_password'];

        // Validate inputs
        if (empty($email) || empty($password)) {
            echo "All fields are required.";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.";
            exit;
        }

        // Check user in the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Verify the entered password matches the hashed password
            if (password_verify($password, $hashedPassword)) {
                echo "success";
                exit;
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No account found with this email.";
        }

        $stmt->close();
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            echo "All fields are required.";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.";
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Registration successful.";
            header("Location: ../html/login.html");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

