-- Update shirt_size column to TEXT to support multiple sizes (e.g., "M: 2, L: 1, XL: 3")
ALTER TABLE registrations 
MODIFY COLUMN shirt_size VARCHAR(500) NULL;

-- Add shirt_quantity column to store total quantity for Shirt Only orders
ALTER TABLE registrations 
ADD COLUMN IF NOT EXISTS shirt_quantity INT DEFAULT 1 AFTER shirt_size;
