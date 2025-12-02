<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/Registration.php';
require_once '../app/models/ShirtOrder.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->connect();
$registration = new Registration($db);
$shirtOrder = new ShirtOrder($db);

// Handle AJAX Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_print_status') {
    $ids = json_decode($_POST['ids'], true);
    $status = (int)$_POST['status'];
    $type = $_POST['type'] ?? 'registration'; // 'registration' or 'shirt'
    $success = true;
    
    foreach ($ids as $id) {
        if ($type === 'shirt') {
            $stmt = $db->prepare("UPDATE shirt_orders SET is_printed = ? WHERE id = ?");
            if (!$stmt->execute([$status, $id])) {
                $success = false;
            }
        } else {
            if (!$registration->updateShippingStatus($id, $status)) {
                $success = false;
            }
        }
    }
    
    echo json_encode(['success' => $success]);
    exit;
}

// Get Registration shipping list (Approved + POST only)
$stmt = $registration->readAll();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reg_shipping_list = array_filter($registrations, function($reg) {
    return ($reg['status'] === 'approved' && $reg['shipping_method'] === 'POST');
});

// Add type marker to registration items
$reg_pending = [];
$reg_printed = [];
foreach ($reg_shipping_list as $item) {
    $item['_type'] = 'registration';
    if (empty($item['is_printed'])) {
        $reg_pending[] = $item;
    } else {
        $reg_printed[] = $item;
    }
}

// Get Shirt Orders shipping list (POST + paid status only - ชำระแล้ว/ตรวจสอบผ่าน)
$stmt = $db->query("SELECT * FROM shirt_orders WHERE shipping_method = 'POST' AND status = 'paid' ORDER BY created_at DESC");
$shirtOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add type marker to shirt order items
$shirt_pending = [];
$shirt_printed = [];
foreach ($shirtOrders as $item) {
    $item['_type'] = 'shirt';
    if (empty($item['is_printed'])) {
        $shirt_pending[] = $item;
    } else {
        $shirt_printed[] = $item;
    }
}

// Combined counts
$total_pending = count($reg_pending) + count($shirt_pending);
$total_printed = count($reg_printed) + count($shirt_printed);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Labels - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG"> 
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['K2D', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'K2D', sans-serif; }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out forwards;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        /* Screen only - hide print elements */
        @media screen {
            .print-address { display: none !important; }
            .type-badge { display: none !important; }
        }
        
        /* Print Styles - Beautiful like screen */
        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                background: white !important;
                font-family: 'K2D', sans-serif;
                font-size: 10pt;
            }
            
            .no-print { display: none !important; }
            
            /* Container */
            .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Hide tabs and sections headers */
            .tab-content > .mb-10 > h3 { display: none !important; }
            .tab-content > div:first-child { display: none !important; }
            
            /* Print Grid - 2 columns */
            .print-container {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 4mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Beautiful Label Card */
            .label-card {
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                padding: 12px !important;
                margin: 0 !important;
                background: white !important;
                height: 90mm !important;
                max-height: 90mm !important;
                overflow: hidden !important;
                position: relative !important;
            }
            
            /* Type Badge - Beautiful */
            .label-card .type-badge {
                position: absolute !important;
                top: 10px !important;
                left: 12px !important;
                font-size: 7pt !important;
                padding: 3px 10px !important;
                border-radius: 20px !important;
                font-weight: 700 !important;
                display: block !important;
            }
            
            .label-card .type-badge.reg {
                background: linear-gradient(135deg, #dbeafe, #c7d2fe) !important;
                color: #3730a3 !important;
                border: 1px solid #a5b4fc !important;
            }
            
            .label-card .type-badge.shirt {
                background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
                color: #92400e !important;
                border: 1px solid #fbbf24 !important;
            }
            
            /* Order ID */
            .label-card .order-id {
                position: absolute !important;
                top: 10px !important;
                right: 12px !important;
                font-size: 8pt !important;
                font-weight: 700 !important;
                color: #64748b !important;
                background: #f1f5f9 !important;
                padding: 3px 8px !important;
                border-radius: 6px !important;
            }
            
            /* Sender Section */
            .label-card .sender-section {
                font-size: 8pt !important;
                padding: 8px 10px !important;
                padding-top: 28px !important;
                margin-bottom: 8px !important;
                border-bottom: 1px dashed #cbd5e1 !important;
                background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
                border-radius: 8px 8px 0 0 !important;
                margin: -12px -12px 10px -12px !important;
            }
            
            .label-card .sender-section span:first-child {
                font-size: 7pt !important;
                color: #64748b !important;
                margin-bottom: 3px !important;
            }
            
            .label-card .sender-section p {
                margin: 2px 0 !important;
                line-height: 1.3 !important;
                color: #475569 !important;
            }
            
            .label-card .sender-section p:first-of-type {
                font-weight: 700 !important;
                color: #1e293b !important;
                font-size: 9pt !important;
            }
            
            /* Receiver Section - Clear & Beautiful */
            .label-card .receiver-section {
                padding-left: 12px !important;
                margin-bottom: 8px !important;
                border-left: 4px solid #6366f1 !important;
            }
            
            .label-card[data-type="shirt"] .receiver-section {
                border-left-color: #f59e0b !important;
            }
            
            .label-card .receiver-section span:first-child {
                font-size: 7pt !important;
                color: #64748b !important;
                margin-bottom: 4px !important;
                display: block !important;
            }
            
            .label-card .receiver-section h3 {
                font-size: 13pt !important;
                font-weight: 700 !important;
                margin-bottom: 6px !important;
                color: #0f172a !important;
            }
            
            .label-card .receiver-section .address-text {
                font-size: 9pt !important;
                line-height: 1.25 !important;
                color: #334155 !important;
                display: block !important;
                -webkit-line-clamp: unset !important;
                overflow: visible !important;
            }
            
            .label-card .receiver-section .screen-address {
                display: none !important;
            }
            
            .label-card .receiver-section .print-address {
                display: block !important;
                white-space: normal !important;
            }
            
            .label-card .receiver-section .print-address br {
                line-height: 0.8 !important;
            }
            
            .label-card .receiver-section .phone-text {
                font-size: 10pt !important;
                font-weight: 700 !important;
                margin-top: 8px !important;
                background: linear-gradient(135deg, #f1f5f9, #e2e8f0) !important;
                padding: 5px 10px !important;
                display: inline-block !important;
                border-radius: 8px !important;
                color: #1e293b !important;
                border: 1px solid #cbd5e1 !important;
            }
            
            .label-card .receiver-section .phone-text::before {
                content: '📞 ' !important;
            }
            
            /* Footer Section - Beautiful */
            .label-card .footer-section {
                position: absolute !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                padding: 8px 12px !important;
                margin: 0 !important;
                border-top: 1px solid #e2e8f0 !important;
                border-radius: 0 0 12px 12px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            
            .label-card[data-type="registration"] .footer-section {
                background: linear-gradient(135deg, #f8fafc, #eef2ff) !important;
            }
            
            .label-card[data-type="shirt"] .footer-section {
                background: linear-gradient(135deg, #fffbeb, #fef3c7) !important;
            }
            
            .label-card .footer-section span:first-child {
                font-size: 6pt !important;
                text-transform: uppercase !important;
                color: #64748b !important;
                font-weight: 700 !important;
            }
            
            .label-card .footer-section .category-text {
                font-size: 8pt !important;
                padding: 3px 8px !important;
                background: white !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 6px !important;
                font-weight: 600 !important;
                color: #334155 !important;
                max-width: 120px !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }
            
            .label-card .footer-section .size-text {
                font-size: 9pt !important;
                padding: 4px 10px !important;
                border-radius: 6px !important;
                font-weight: 700 !important;
            }
            
            .label-card[data-type="registration"] .footer-section .size-text {
                background: linear-gradient(135deg, #e0e7ff, #c7d2fe) !important;
                color: #3730a3 !important;
                border: 1px solid #a5b4fc !important;
            }
            
            .label-card[data-type="shirt"] .footer-section .size-text {
                background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
                color: #92400e !important;
                border: 1px solid #fbbf24 !important;
            }
            
            /* Hide items not selected for printing */
            .print-hidden { display: none !important; }
            
            /* Hide checkbox in print */
            .item-checkbox { display: none !important; }
            
            /* Hide FontAwesome icons in print */
            .label-card .fa, .label-card .fas, .label-card .far, .label-card .fab {
                display: none !important;
            }
            
            /* Hide decorative sidebar bars */
            .label-card .receiver-section > .absolute,
            .label-card .sender-section > .absolute {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50 text-slate-600 min-h-screen">
    <div class="no-print">
        <?php include 'navbar.php'; ?>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl animate-fade-in-up">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8 no-print">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-2xl shadow-lg shadow-blue-500/30">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    จัดการการจัดส่ง (Shipping)
                </h1>
                <p class="text-slate-500 text-sm mt-2 ml-14">รวมรายการจัดส่งทั้งหมด - การสมัครวิ่ง & สั่งซื้อเสื้อ</p>
            </div>
            <div class="flex gap-3 items-center">
                <div class="hidden md:flex gap-2 text-sm">
                    <span class="bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-blue-200 shadow-sm">
                        <i class="fas fa-running"></i> 
                        <span class="font-bold"><?php echo count($reg_pending) + count($reg_printed); ?></span> สมัครวิ่ง
                    </span>
                    <span class="bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-amber-200 shadow-sm">
                        <i class="fas fa-tshirt"></i> 
                        <span class="font-bold"><?php echo count($shirt_pending) + count($shirt_printed); ?></span> สั่งเสื้อ
                    </span>
                </div>
                <button onclick="printSelected()" class="group bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 text-white px-6 py-3 rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-300 font-bold flex items-center gap-2">
                    <i class="fas fa-print group-hover:animate-pulse"></i> พิมพ์รายการที่เลือก
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-6 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 no-print">
            <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-3 font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl transition-all shadow-lg shadow-blue-500/30 flex items-center gap-2">
                <i class="fas fa-clock"></i>
                <span>รอพิมพ์</span>
                <span class="bg-white/20 text-white text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_pending; ?></span>
            </button>
            <button onclick="switchTab('printed')" id="tab-printed" class="px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-xl flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>พิมพ์แล้ว</span>
                <span class="bg-slate-200 text-slate-600 text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_printed; ?></span>
            </button>
        </div>

        <!-- Content Area - Pending -->
        <div id="content-pending" class="tab-content">
            <?php if ($total_pending === 0): ?>
                <div class="text-center py-20 bg-white rounded-3xl shadow-lg border border-slate-100">
                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-green-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-check-circle text-5xl text-emerald-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">ยอดเยี่ยม! 🎉</h3>
                    <p class="text-slate-500 mt-2">ไม่มีรายการค้างพิมพ์ในขณะนี้</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-6 no-print bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 p-5 rounded-2xl border border-blue-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="selectAllPending" onchange="toggleAll('pending')" class="w-5 h-5 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <label for="selectAllPending" class="font-bold text-slate-700 cursor-pointer select-none">เลือกทั้งหมด</label>
                    </div>
                    <div class="md:ml-auto flex flex-wrap gap-2 text-sm">
                        <span class="bg-white text-blue-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-blue-200 shadow-sm">
                            <i class="fas fa-running"></i> สมัครวิ่ง: <span class="font-bold"><?php echo count($reg_pending); ?></span>
                        </span>
                        <span class="bg-white text-amber-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-amber-200 shadow-sm">
                            <i class="fas fa-tshirt"></i> สั่งเสื้อ: <span class="font-bold"><?php echo count($shirt_pending); ?></span>
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($reg_pending)): ?>
                <!-- Registration Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-slate-700 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="fas fa-running text-white"></i>
                        </div>
                        <span>จากการสมัครวิ่ง</span>
                        <span class="text-sm font-normal text-slate-400 bg-slate-100 px-3 py-1 rounded-full"><?php echo count($reg_pending); ?> รายการ</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                        <?php foreach ($reg_pending as $row): ?>
                            <?php include 'shipping_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($shirt_pending)): ?>
                <!-- Shirt Orders Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-slate-700 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i class="fas fa-tshirt text-white"></i>
                        </div>
                        <span>จากการสั่งซื้อเสื้อ</span>
                        <span class="text-sm font-normal text-slate-400 bg-slate-100 px-3 py-1 rounded-full"><?php echo count($shirt_pending); ?> รายการ</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                        <?php foreach ($shirt_pending as $row): ?>
                            <?php include 'shirt_shipping_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Content Area - Printed -->
        <div id="content-printed" class="tab-content hidden">
            <?php if ($total_printed === 0): ?>
                <div class="text-center py-20 bg-white rounded-3xl shadow-lg border border-slate-100">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-print text-5xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500 text-lg">ยังไม่มีรายการที่พิมพ์แล้ว</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-6 no-print bg-gradient-to-r from-slate-100 via-slate-50 to-slate-100 p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="selectAllPrinted" onchange="toggleAll('printed')" class="w-5 h-5 rounded-lg border-slate-300 text-slate-600 focus:ring-slate-500 cursor-pointer">
                        <label for="selectAllPrinted" class="font-bold text-slate-700 cursor-pointer select-none">เลือกทั้งหมด</label>
                    </div>
                    <div class="md:ml-auto flex flex-wrap gap-2 items-center">
                        <span class="text-sm bg-white text-blue-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-blue-200 shadow-sm">
                            <i class="fas fa-running"></i> <span class="font-bold"><?php echo count($reg_printed); ?></span>
                        </span>
                        <span class="text-sm bg-white text-amber-700 px-4 py-2 rounded-xl flex items-center gap-2 border border-amber-200 shadow-sm">
                            <i class="fas fa-tshirt"></i> <span class="font-bold"><?php echo count($shirt_printed); ?></span>
                        </span>
                        <button onclick="markAsUnprinted()" class="text-sm text-red-600 hover:text-white hover:bg-red-500 bg-red-50 border border-red-200 px-4 py-2 rounded-xl transition-all duration-300 font-bold flex items-center gap-2 shadow-sm">
                            <i class="fas fa-undo"></i> ย้ายกลับ
                        </button>
                    </div>
                </div>
                
                <?php if (!empty($reg_printed)): ?>
                <!-- Registration Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-slate-700 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="fas fa-running text-white"></i>
                        </div>
                        <span>จากการสมัครวิ่ง</span>
                        <span class="text-sm font-normal text-slate-400 bg-slate-100 px-3 py-1 rounded-full"><?php echo count($reg_printed); ?> รายการ</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                        <?php foreach ($reg_printed as $row): ?>
                            <?php include 'shipping_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($shirt_printed)): ?>
                <!-- Shirt Orders Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-slate-700 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i class="fas fa-tshirt text-white"></i>
                        </div>
                        <span>จากการสั่งซื้อเสื้อ</span>
                        <span class="text-sm font-normal text-slate-400 bg-slate-100 px-3 py-1 rounded-full"><?php echo count($shirt_printed); ?> รายการ</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                        <?php foreach ($shirt_printed as $row): ?>
                            <?php include 'shirt_shipping_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('content-' + tab).classList.remove('hidden');
            
            // Update buttons
            const btnPending = document.getElementById('tab-pending');
            const btnPrinted = document.getElementById('tab-printed');
            
            // Reset classes
            const inactiveClass = 'px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-xl flex items-center gap-2';
            const activeClass = 'px-6 py-3 font-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl transition-all shadow-lg shadow-blue-500/30 flex items-center gap-2';
            
            btnPending.className = inactiveClass;
            btnPrinted.className = inactiveClass;
            
            // Update count badges
            if (tab === 'pending') {
                btnPending.className = activeClass;
                btnPending.innerHTML = `<i class="fas fa-clock"></i><span>รอพิมพ์</span><span class="bg-white/20 text-white text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_pending; ?></span>`;
                btnPrinted.innerHTML = `<i class="fas fa-check-circle"></i><span>พิมพ์แล้ว</span><span class="bg-slate-200 text-slate-600 text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_printed; ?></span>`;
            } else {
                btnPrinted.className = activeClass;
                btnPending.innerHTML = `<i class="fas fa-clock"></i><span>รอพิมพ์</span><span class="bg-slate-200 text-slate-600 text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_pending; ?></span>`;
                btnPrinted.innerHTML = `<i class="fas fa-check-circle"></i><span>พิมพ์แล้ว</span><span class="bg-white/20 text-white text-xs px-2.5 py-1 rounded-full font-bold"><?php echo $total_printed; ?></span>`;
            }
        }

        function toggleAll(type) {
            const master = document.getElementById(type === 'pending' ? 'selectAllPending' : 'selectAllPrinted');
            const container = document.getElementById('content-' + type);
            const checkboxes = container.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = master.checked);
        }

        function getSelectedIds() {
            // Check visible tab
            const pendingVisible = !document.getElementById('content-pending').classList.contains('hidden');
            const container = document.getElementById(pendingVisible ? 'content-pending' : 'content-printed');
            const checkboxes = container.querySelectorAll('.item-checkbox:checked');
            
            // Group by type
            const result = {
                registration: [],
                shirt: []
            };
            
            checkboxes.forEach(cb => {
                const card = cb.closest('.label-card');
                const type = card.getAttribute('data-type') || 'registration';
                result[type].push(cb.value);
            });
            
            return result;
        }

        function printSelected() {
            const selected = getSelectedIds();
            const totalSelected = selected.registration.length + selected.shirt.length;
            
            if (totalSelected === 0) {
                Swal.fire({
                    title: 'แจ้งเตือน',
                    text: 'กรุณาเลือกรายการที่จะพิมพ์',
                    icon: 'warning',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold'
                    }
                });
                return;
            }

            // Prepare for print: Hide unselected items
            document.querySelectorAll('.label-card').forEach(card => {
                const id = card.getAttribute('data-id');
                const type = card.getAttribute('data-type') || 'registration';
                
                if ((type === 'registration' && selected.registration.includes(id)) ||
                    (type === 'shirt' && selected.shirt.includes(id))) {
                    card.classList.remove('print-hidden');
                } else {
                    card.classList.add('print-hidden');
                }
            });

            // Print
            window.print();

            // Restore visibility
            document.querySelectorAll('.label-card').forEach(card => {
                card.classList.remove('print-hidden');
            });

            // Ask to update status
            Swal.fire({
                title: '<span class="text-slate-800">พิมพ์สำเร็จหรือไม่?</span>',
                html: '<p class="text-slate-500">ต้องการเปลี่ยนสถานะรายการที่เลือกเป็น \'พิมพ์แล้ว\' หรือไม่?</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check mr-2"></i>ใช่, พิมพ์แล้ว',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>ไม่, ยังไม่ได้พิมพ์',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#94A3B8',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update both types
                    let promises = [];
                    if (selected.registration.length > 0) {
                        promises.push(updateStatusAsync(selected.registration, 1, 'registration'));
                    }
                    if (selected.shirt.length > 0) {
                        promises.push(updateStatusAsync(selected.shirt, 1, 'shirt'));
                    }
                    Promise.all(promises).then(() => {
                        Swal.fire({
                            title: '<span class="text-emerald-600">เรียบร้อย!</span>',
                            html: '<p class="text-slate-600">อัพเดทสถานะเรียบร้อยแล้ว</p>',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-2xl'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    });
                }
            });
        }

        function markAsUnprinted() {
            const selected = getSelectedIds();
            const totalSelected = selected.registration.length + selected.shirt.length;
            
            if (totalSelected === 0) {
                Swal.fire({
                    title: 'แจ้งเตือน',
                    text: 'กรุณาเลือกรายการ',
                    icon: 'warning',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold'
                    }
                });
                return;
            }

            Swal.fire({
                title: '<span class="text-slate-800">ยืนยัน?</span>',
                html: '<p class="text-slate-500">ย้ายรายการที่เลือกกลับไป \'รอพิมพ์\'?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-undo mr-2"></i>ยืนยัน',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#94A3B8',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let promises = [];
                    if (selected.registration.length > 0) {
                        promises.push(updateStatusAsync(selected.registration, 0, 'registration'));
                    }
                    if (selected.shirt.length > 0) {
                        promises.push(updateStatusAsync(selected.shirt, 0, 'shirt'));
                    }
                    Promise.all(promises).then(() => {
                        Swal.fire({
                            title: '<span class="text-emerald-600">เรียบร้อย!</span>',
                            html: '<p class="text-slate-600">ย้ายกลับไปรอพิมพ์แล้ว</p>',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-2xl'
                            }
                        }).then(() => {
                            location.reload();
                        });
                    });
                }
            });
        }

        function updateStatusAsync(ids, status, type) {
            const formData = new FormData();
            formData.append('action', 'update_print_status');
            formData.append('ids', JSON.stringify(ids));
            formData.append('status', status);
            formData.append('type', type);

            return fetch('shipping.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json());
        }

        function updateStatus(ids, status) {
            updateStatusAsync(ids, status, 'registration').then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '<span class="text-emerald-600">เรียบร้อย!</span>',
                        html: '<p class="text-slate-600">อัพเดทสถานะเรียบร้อยแล้ว</p>',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'เกิดข้อผิดพลาด',
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-6 py-3 font-bold'
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>