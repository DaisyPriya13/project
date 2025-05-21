<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chosen_lamb";

$data = json_decode(file_get_contents('php://input'), true);

if (
    isset($data['user_name']) &&
    isset($data['email']) &&
    isset($data['payment_mode'])
) {
    $user_name = $data['user_name'];
    $email = $data['email'];
    $payment_mode = $data['payment_mode'];
    $timestamp = date('c');

    // 1. Save to database
    $db_success = false;
    $db_error = '';
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_errno) {
        $db_error = 'DB connection failed';
    } else {
        $stmt = $mysqli->prepare("INSERT INTO transaction (user_name, email, payment_mode, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user_name, $email, $payment_mode, $timestamp);
        if ($stmt->execute()) {
            $db_success = true;
        } else {
            $db_error = 'Insert failed';
        }
        $stmt->close();
        $mysqli->close();
    }

    // 2. Save to JSON file
    $json_success = false;
    $json_error = '';
    $transactionFile = realpath(__DIR__ . '/../json/transaction.json');
    if ($transactionFile === false) {
        $transactionFile = __DIR__ . '/../json/transaction.json';
    }
    $transactions = [];
    if (file_exists($transactionFile)) {
        $json = file_get_contents($transactionFile);
        $transactions = json_decode($json, true) ?: [];
    }
    $transactions[] = [
        'user_name' => $user_name,
        'email' => $email,
        'payment_mode' => $payment_mode,
        'timestamp' => $timestamp
    ];
    if (file_put_contents($transactionFile, json_encode($transactions, JSON_PRETTY_PRINT))) {
        $json_success = true;
    } else {
        $json_error = 'Could not save transaction to JSON';
    }

    // 3. Response
    if ($db_success && $json_success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'db_error' => $db_error,
            'json_error' => $json_error
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
}
?>
