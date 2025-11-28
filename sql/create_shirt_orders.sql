-- Create shirt_orders table for merchandise-only orders
CREATE TABLE IF NOT EXISTS shirt_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    citizen_id VARCHAR(13),
    address TEXT NOT NULL,
    
    -- Shirt details (can store multiple: "M: 2, L: 1, XL: 3")
    shirt_sizes VARCHAR(500) NOT NULL,
    shirt_quantity INT NOT NULL DEFAULT 1,
    
    -- Shipping
    shipping_method ENUM('SELF', 'POST') DEFAULT 'SELF',
    
    -- Payment
    payment_amount DECIMAL(10,2),
    payment_slip VARCHAR(255),
    payment_date DATE,
    payment_time TIME,
    bank_ref VARCHAR(100),
    sender_name VARCHAR(255),
    
    -- Status
    status ENUM('pending', 'paid', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    tracking_number VARCHAR(100),
    notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for faster lookups
CREATE INDEX idx_phone ON shirt_orders(phone);
CREATE INDEX idx_status ON shirt_orders(status);
CREATE INDEX idx_order_number ON shirt_orders(order_number);
