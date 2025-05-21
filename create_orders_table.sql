CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    products JSON NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_mode VARCHAR(50) NOT NULL,
    order_date DATETIME NOT NULL
);
