CREATE DATABASE IF NOT EXISTS phichai_run_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE phichai_run_2026;

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    birth_date DATE NOT NULL,
    age INT,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    citizen_id VARCHAR(13),
    prefix VARCHAR(50),
    emergency_contact_name VARCHAR(255) NOT NULL,
    emergency_contact_phone VARCHAR(20) NOT NULL,
    address TEXT,
    category ENUM(
        'Walk & Run 3.5km - ประถมศึกษา',
        'Walk & Run 3.5km - ม.ต้น',
        'Walk & Run 3.5km - ม.ปลาย/ปวช.',
        'Walk & Run 3.5km - VIP',
        'Fun Run 5.5km นักเรียน - ประถมศึกษา',
        'Fun Run 5.5km นักเรียน - ม.ต้น',
        'Fun Run 5.5km นักเรียน - ม.ปลาย/ปวช.',
        'Fun Run 5.5km บุคคลทั่วไป - 19-29 ปี',
        'Fun Run 5.5km บุคคลทั่วไป - 30-39 ปี',
        'Fun Run 5.5km บุคคลทั่วไป - 40-49 ปี',
        'Fun Run 5.5km บุคคลทั่วไป - 50-59 ปี',
        'Fun Run 5.5km บุคคลทั่วไป - 60 ปีขึ้นไป',
        'Fun Run 5.5km บุคคลทั่วไป - VIP',
        'Shirt Only'
    ) NOT NULL,
    shirt_size ENUM('XS', 'S', 'M', 'L', 'XL', '2XL', '3XL') NOT NULL,
    shipping_method ENUM('SELF', 'POST') DEFAULT 'SELF',
    payment_slip VARCHAR(255),
    payment_amount DECIMAL(10, 2),
    payment_date DATE,
    payment_time TIME,
    bank_ref VARCHAR(50),
    sender_name VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reject_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);