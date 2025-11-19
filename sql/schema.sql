CREATE DATABASE IF NOT EXISTS phichai_run_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE phichai_run_2026;

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    bib_name VARCHAR(50),
    gender ENUM('Male', 'Female') NOT NULL,
    birth_date DATE NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    category ENUM('Fun Run 5KM', 'Mini Marathon 10.5KM', 'Half Marathon 21KM') NOT NULL,
    shirt_size ENUM('XS', 'S', 'M', 'L', 'XL', '2XL', '3XL') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);