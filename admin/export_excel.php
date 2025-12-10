<?php
/**
 * Excel Export with Thai Language Support
 * Uses UTF-8 BOM to ensure Thai characters display correctly in Excel
 */
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';

$database = new Database();
$db = $database->connect();

$type = isset($_GET['type']) ? $_GET['type'] : 'registrations';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Set headers for Excel download with proper encoding
$filename = '';
$headers = [];
$data = [];

if ($type === 'registrations') {
    require_once '../app/models/Registration.php';
    $registration = new Registration($db);
    
    $stmt = $registration->readAll();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Filter by status if specified
    if ($status && $status !== 'all') {
        $rows = array_filter($rows, function($row) use ($status) {
            return ($row['status'] ?? 'pending') === $status;
        });
    }
    
    $filename = 'registrations_' . date('Y-m-d_His') . '.csv';
    
    // Headers
    $headers = [
        'ID',
        'คำนำหน้า',
        'ชื่อ-นามสกุล',
        'เลขบัตรประชาชน',
        'วันเกิด',
        'อายุ',
        'เพศ',
        'เบอร์โทร',
        'อีเมล',
        'ที่อยู่',
        'ผู้ติดต่อฉุกเฉิน',
        'เบอร์ผู้ติดต่อฉุกเฉิน',
        'ประเภทการวิ่ง',
        'ไซส์เสื้อ',
        'การจัดส่ง',
        'ยอดเงิน',
        'วันที่โอน',
        'เวลาโอน',
        'สถานะ',
        'วันที่สมัคร'
    ];
    
    // Status labels
    $statusLabels = [
        'pending' => 'รอตรวจสอบ',
        'approved' => 'อนุมัติแล้ว',
        'rejected' => 'ปฏิเสธ'
    ];
    
    // Shipping labels
    $shippingLabels = [
        'POST' => 'จัดส่งไปรษณีย์',
        'SELF' => 'รับด้วยตนเอง'
    ];
    
    // Build data rows
    foreach ($rows as $row) {
        $data[] = [
            $row['id'] ?? '',
            $row['prefix'] ?? '',
            $row['full_name'] ?? '',
            $row['citizen_id'] ?? '',
            $row['birth_date'] ?? '',
            $row['age'] ?? '',
            $row['gender'] == 'Male' ? 'ชาย' : ($row['gender'] == 'Female' ? 'หญิง' : $row['gender']),
            $row['phone'] ?? '',
            $row['email'] ?? '',
            $row['address'] ?? '',
            $row['emergency_contact_name'] ?? '',
            $row['emergency_contact_phone'] ?? '',
            $row['category'] ?? '',
            $row['shirt_size'] ?? '',
            $shippingLabels[$row['shipping_method'] ?? 'SELF'] ?? $row['shipping_method'],
            $row['payment_amount'] ?? '',
            $row['payment_date'] ?? '',
            $row['payment_time'] ?? '',
            $statusLabels[$row['status'] ?? 'pending'] ?? $row['status'],
            $row['created_at'] ?? ''
        ];
    }
    
} elseif ($type === 'shirt_orders') {
    require_once '../app/models/ShirtOrder.php';
    $shirtOrder = new ShirtOrder($db);
    
    $stmt = $shirtOrder->readAll();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Filter by status if specified
    if ($status && $status !== 'all') {
        $rows = array_filter($rows, function($row) use ($status) {
            return $row['status'] === $status;
        });
    }
    
    $filename = 'shirt_orders_' . date('Y-m-d_His') . '.csv';
    
    // Headers
    $headers = [
        'ID',
        'เลขออเดอร์',
        'ชื่อ-นามสกุล',
        'เบอร์โทร',
        'อีเมล',
        'เลขบัตรประชาชน',
        'ที่อยู่',
        'ไซส์เสื้อ',
        'จำนวน',
        'ยอดเงิน',
        'การจัดส่ง',
        'เลขพัสดุ',
        'สถานะ',
        'วันที่สั่ง',
        'หมายเหตุ'
    ];
    
    // Status labels
    $statusLabels = [
        'pending' => 'รอตรวจสอบ',
        'paid' => 'ชำระแล้ว',
        'shipped' => 'จัดส่งแล้ว',
        'completed' => 'เสร็จสิ้น',
        'cancelled' => 'ยกเลิก'
    ];
    
    // Shipping labels
    $shippingLabels = [
        'POST' => 'จัดส่งไปรษณีย์',
        'SELF' => 'รับเอง'
    ];
    
    // Build data rows
    foreach ($rows as $row) {
        $data[] = [
            $row['id'] ?? '',
            $row['order_number'] ?? '',
            $row['full_name'] ?? '',
            $row['phone'] ?? '',
            $row['email'] ?? '',
            $row['citizen_id'] ?? '',
            $row['address'] ?? '',
            $row['shirt_sizes'] ?? '',
            $row['shirt_quantity'] ?? '',
            $row['payment_amount'] ?? '',
            $shippingLabels[$row['shipping_method'] ?? 'SELF'] ?? $row['shipping_method'],
            $row['tracking_number'] ?? '',
            $statusLabels[$row['status'] ?? 'pending'] ?? $row['status'],
            $row['created_at'] ?? '',
            $row['notes'] ?? ''
        ];
    }
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel to recognize Thai characters
fwrite($output, "\xEF\xBB\xBF");

// Write headers
fputcsv($output, $headers);

// Write data rows
foreach ($data as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
