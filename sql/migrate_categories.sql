-- Migration: Update category ENUM to new structure
-- Run this to update existing database

-- Step 1: Change category column to VARCHAR temporarily
ALTER TABLE registrations 
MODIFY COLUMN category VARCHAR(100) NOT NULL;

-- Step 2: Migrate old categories to new format
UPDATE registrations SET category = 'Fun Run 5.5km นักเรียน - ประถมศึกษา' WHERE category = 'Fun Run 5.5km - ประถมศึกษา';
UPDATE registrations SET category = 'Fun Run 5.5km นักเรียน - ม.ต้น' WHERE category = 'Fun Run 5.5km - ม.ต้น';
UPDATE registrations SET category = 'Fun Run 5.5km นักเรียน - ม.ปลาย/ปวช.' WHERE category = 'Fun Run 5.5km - ม.ปลาย/ปวช.';
UPDATE registrations SET category = 'Fun Run 5.5km บุคคลทั่วไป - 40-49 ปี' WHERE category = 'Fun Run 5.5km - บุคคลทั่วไป';
UPDATE registrations SET category = 'Fun Run 5.5km บุคคลทั่วไป - 50-59 ปี' WHERE category = 'Fun Run 5.5km - อายุมากกว่า 50';
UPDATE registrations SET category = 'Fun Run 5.5km บุคคลทั่วไป - VIP' WHERE category = 'Fun Run 5.5km - VIP';

-- Step 3: Change back to ENUM with new values
ALTER TABLE registrations 
MODIFY COLUMN category ENUM(
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
) NOT NULL;
