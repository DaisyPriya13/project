CREATE TABLE `transaction` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `payment_mode` VARCHAR(50) NOT NULL,
    `timestamp` VARCHAR(50) NOT NULL
);
