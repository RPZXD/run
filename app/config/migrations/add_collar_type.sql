-- Migration: Add collar_type column to shirt_orders and registrations tables
-- Date: 2026-01-16
-- Description: Added shirt collar type (round/polo) with polo adding 100 baht per shirt

-- Add collar_type to shirt_orders table
ALTER TABLE `shirt_orders` 
ADD COLUMN `collar_type` VARCHAR(20) DEFAULT 'round' AFTER `shirt_quantity`;

-- Add collar_type to registrations table  
ALTER TABLE `registrations` 
ADD COLUMN `collar_type` VARCHAR(20) DEFAULT 'round' AFTER `shirt_quantity`;

-- Optional: Update existing records to have default value
UPDATE `shirt_orders` SET `collar_type` = 'round' WHERE `collar_type` IS NULL;
UPDATE `registrations` SET `collar_type` = 'round' WHERE `collar_type` IS NULL;
