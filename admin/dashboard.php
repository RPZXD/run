<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/Registration.php';
require_once '../app/models/ShirtOrder.php';

$database = new Database();
$db = $database->connect();
$registration = new Registration($db);
$shirtOrderModel = new ShirtOrder($db);

$stmt = $registration->readAll();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Shirt Orders
$shirtOrdersStmt = $shirtOrderModel->readAll();
$shirtOrders = $shirtOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

// ===== REGISTRATION STATS =====
$reg_income = 0;
$reg_income_approved = 0;
$by_category = [];
$by_gender = [];
$reg_by_size = [];
$by_status = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
$reg_with_shirt = 0;
$reg_without_shirt = 0;
$stats_by_cat_gender = [];

foreach ($registrations as $reg) {
    $status = $reg['status'] ?? 'pending';
    $cat = $reg['category'] ?? 'Unknown';
    $gender = $reg['gender'] ?? 'Unknown';
    $size = $reg['shirt_size'] ?? '';
    $qty = (int)($reg['shirt_quantity'] ?? 1);
    
    // Category & Gender Stats
    if (!isset($stats_by_cat_gender[$cat])) {
        $stats_by_cat_gender[$cat] = ['Male' => 0, 'Female' => 0, 'Total' => 0];
    }
    if ($gender === 'Male') $stats_by_cat_gender[$cat]['Male']++;
    elseif ($gender === 'Female') $stats_by_cat_gender[$cat]['Female']++;
    $stats_by_cat_gender[$cat]['Total']++;

    // Status
    if (isset($by_status[$status])) {
        $by_status[$status]++;
    } else {
        $by_status[$status] = 1;
    }

    // Income
    $payment = (float)($reg['payment_amount'] ?? 0);
    $reg_income += $payment;
    if ($status === 'approved') {
        $reg_income_approved += $payment;
    }

    // Category
    if (!isset($by_category[$cat])) $by_category[$cat] = 0;
    $by_category[$cat]++;

    // Gender
    if (!isset($by_gender[$gender])) $by_gender[$gender] = 0;
    $by_gender[$gender]++;

    // Size (only for registrations with shirt)
    if (!empty($size) && $size !== 'ไม่รับเสื้อ' && $size !== '-') {
        $reg_with_shirt++;
        if (!isset($reg_by_size[$size])) $reg_by_size[$size] = 0;
        $reg_by_size[$size] += $qty;
    } else {
        $reg_without_shirt++;
    }
}

ksort($reg_by_size);
ksort($by_category);
ksort($stats_by_cat_gender);

// ===== SHIRT ORDER STATS =====
$shirt_order_count = count($shirtOrders);
$shirt_order_income = 0;
$shirt_order_income_approved = 0;
$shirt_order_by_size = [];
$shirt_order_total_shirts = 0;
$shirt_order_by_status = ['pending' => 0, 'paid' => 0, 'shipped' => 0, 'completed' => 0, 'cancelled' => 0];

foreach ($shirtOrders as $order) {
    $status = $order['status'] ?? 'pending';
    $payment = (float)($order['payment_amount'] ?? 0);
    $qty = (int)($order['shirt_quantity'] ?? 0);
    
    // Status count
    if (isset($shirt_order_by_status[$status])) {
        $shirt_order_by_status[$status]++;
    }
    
    // Income
    $shirt_order_income += $payment;
    if ($status === 'paid' || $status === 'shipped' || $status === 'completed') {
        $shirt_order_income_approved += $payment;
    }
    
    // Total shirts
    $shirt_order_total_shirts += $qty;
    
    // Parse shirt sizes (format: "S:2,M:3,L:1")
    $sizes_str = $order['shirt_sizes'] ?? '';
    if (!empty($sizes_str)) {
        $size_pairs = explode(',', $sizes_str);
        foreach ($size_pairs as $pair) {
            $parts = explode(':', trim($pair));
            if (count($parts) == 2) {
                $size = trim($parts[0]);
                $count = (int)trim($parts[1]);
                if (!isset($shirt_order_by_size[$size])) $shirt_order_by_size[$size] = 0;
                $shirt_order_by_size[$size] += $count;
            }
        }
    }
}

ksort($shirt_order_by_size);

// ===== COMBINED STATS =====
$total_income = $reg_income + $shirt_order_income;
$total_income_approved = $reg_income_approved + $shirt_order_income_approved;

// Combined shirt sizes
$combined_sizes = [];
$all_sizes = array_unique(array_merge(array_keys($reg_by_size), array_keys($shirt_order_by_size)));
sort($all_sizes);
foreach ($all_sizes as $size) {
    $combined_sizes[$size] = [
        'registration' => $reg_by_size[$size] ?? 0,
        'shirt_order' => $shirt_order_by_size[$size] ?? 0,
        'total' => ($reg_by_size[$size] ?? 0) + ($shirt_order_by_size[$size] ?? 0)
    ];
}

$total_shirts_all = array_sum(array_column($combined_sizes, 'total'));
$total_reg_shirts = array_sum($reg_by_size);
$total_shirt_order_shirts = array_sum($shirt_order_by_size);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 font-sarabun bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwgMCwgMCwgMC4wNSkiLz48L3N2Zz4=')]">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 animate-fade-in-up">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
                <p class="text-slate-500 mt-1">ภาพรวมข้อมูลการสมัครและการสั่งซื้อ Phichai Run 2026</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">
                    <i class="far fa-clock mr-2 text-blue-500"></i> ข้อมูลล่าสุด: <?php echo date('H:i'); ?> น.
                </div>
                <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">
                    <i class="far fa-calendar-alt mr-2 text-blue-500"></i> <?php echo date('d M Y'); ?>
                </div>
            </div>
        </div>

        <!-- 1. Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-green-400 to-green-600"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">รายได้รวม (อนุมัติแล้ว)</div>
                        <div class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-green-600 transition-colors">฿<?php echo number_format($total_income_approved); ?></div>
                        <div class="text-xs text-slate-400 mt-1">จากทั้งหมด ฿<?php echo number_format($total_income); ?></div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors shadow-sm">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Runners -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-200 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-400 to-blue-600"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">ผู้สมัครวิ่ง</div>
                        <div class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-blue-600 transition-colors"><?php echo number_format(count($registrations)); ?></div>
                        <div class="text-xs text-slate-400 mt-1">คน</div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                        <i class="fas fa-running text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Shirt Orders -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-300 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-yellow-400 to-yellow-600"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">ออเดอร์สั่งซื้อเสื้อ</div>
                        <div class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-yellow-600 transition-colors"><?php echo number_format($shirt_order_count); ?></div>
                        <div class="text-xs text-slate-400 mt-1">รายการ</div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-colors shadow-sm">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Shirts -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-300 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-indigo-400 to-indigo-600"></div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">ยอดเสื้อรวมทั้งหมด</div>
                        <div class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-indigo-600 transition-colors"><?php echo number_format($total_shirts_all); ?></div>
                        <div class="text-xs text-slate-400 mt-1">ตัว (วิ่ง + สั่งซื้อ)</div>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                        <i class="fas fa-tshirt text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Detailed Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Registration Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-200">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                        ข้อมูลการสมัครวิ่ง (Registration)
                    </h3>
                    <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-600 rounded-lg">Runners</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">รายได้ (อนุมัติ)</div>
                            <div class="text-xl font-bold text-green-600">฿<?php echo number_format($reg_income_approved); ?></div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">รอตรวจสอบ</div>
                            <div class="text-xl font-bold text-yellow-500"><?php echo number_format($by_status['pending'] ?? 0); ?></div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-check-circle text-green-500 w-5"></i> อนุมัติแล้ว</span>
                            <span class="font-bold text-slate-700"><?php echo number_format($by_status['approved'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-tshirt text-indigo-500 w-5"></i> รับเสื้อ</span>
                            <span class="font-bold text-slate-700"><?php echo number_format($reg_with_shirt); ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-user-slash text-slate-400 w-5"></i> ไม่รับเสื้อ</span>
                            <span class="font-bold text-slate-700"><?php echo number_format($reg_without_shirt); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shirt Order Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up delay-300">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-yellow-500 rounded-full"></span>
                        ข้อมูลการสั่งซื้อเสื้อ (Orders)
                    </h3>
                    <span class="text-xs font-bold px-2 py-1 bg-yellow-100 text-yellow-600 rounded-lg">Shop</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">รายได้ (อนุมัติ)</div>
                            <div class="text-xl font-bold text-green-600">฿<?php echo number_format($shirt_order_income_approved); ?></div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="text-xs text-slate-400 mb-1">รอตรวจสอบ</div>
                            <div class="text-xl font-bold text-yellow-500"><?php echo number_format($shirt_order_by_status['pending'] ?? 0); ?></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-box text-blue-500 w-5"></i> ชำระเงิน/ส่งแล้ว</span>
                            <span class="font-bold text-slate-700"><?php echo number_format(($shirt_order_by_status['paid'] ?? 0) + ($shirt_order_by_status['shipped'] ?? 0) + ($shirt_order_by_status['completed'] ?? 0)); ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-layer-group text-indigo-500 w-5"></i> จำนวนเสื้อรวม</span>
                            <span class="font-bold text-slate-700"><?php echo number_format($shirt_order_total_shirts); ?> ตัว</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500"><i class="fas fa-times-circle text-red-400 w-5"></i> ยกเลิก</span>
                            <span class="font-bold text-slate-700"><?php echo number_format($shirt_order_by_status['cancelled'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Shirt Size Matrix -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden animate-fade-in-up delay-300">
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i class="fas fa-ruler-combined text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">สรุปยอดเสื้อตามไซส์ (Size Breakdown)</h3>
                        <p class="text-sm text-slate-500">รวมยอดจากทั้งผู้สมัครวิ่งและออเดอร์สั่งซื้อแยก</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <button onclick="exportTable('shirtTable', 'csv')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 hover:text-slate-800 transition-colors flex items-center gap-2">
                        <i class="fas fa-file-csv text-green-600"></i> CSV
                    </button>
                    <button onclick="exportTable('shirtTable', 'xlsx')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 hover:text-slate-800 transition-colors flex items-center gap-2">
                        <i class="fas fa-file-excel text-green-600"></i> Excel
                    </button>
                    <div class="h-6 w-px bg-slate-200 mx-1"></div>
                    <div class="px-3 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-bold flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-blue-500"></div> วิ่ง</div>
                    <div class="px-3 py-1 rounded-md bg-yellow-50 text-yellow-600 text-xs font-bold flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-yellow-500"></div> สั่งซื้อ</div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="shirtTable" class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-lg">Size</th>
                            <th class="px-6 py-4 text-center text-blue-600">จากวิ่ง (ตัว)</th>
                            <th class="px-6 py-4 text-center text-yellow-600">จากสั่งซื้อ (ตัว)</th>
                            <th class="px-6 py-4 text-center text-indigo-600">รวมทั้งหมด (ตัว)</th>
                            <th class="px-6 py-4 w-1/3 rounded-tr-lg">สัดส่วน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($combined_sizes as $size => $data): 
                            $percent = $total_shirts_all > 0 ? ($data['total'] / $total_shirts_all) * 100 : 0;
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-700"><?php echo $size; ?></td>
                            <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo number_format($data['registration']); ?></td>
                            <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo number_format($data['shirt_order']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold">
                                    <?php echo number_format($data['total']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden flex">
                                        <?php if($data['total'] > 0): 
                                            $reg_pct = ($data['registration'] / $data['total']) * 100;
                                            $ord_pct = ($data['shirt_order'] / $data['total']) * 100;
                                        ?>
                                        <div class="h-full bg-blue-500" style="width: <?php echo $reg_pct; ?>%"></div>
                                        <div class="h-full bg-yellow-500" style="width: <?php echo $ord_pct; ?>%"></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs text-slate-400 w-12 text-right"><?php echo number_format($percent, 1); ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="bg-slate-50 font-bold">
                            <td class="px-6 py-4 text-slate-800">รวมทั้งสิ้น</td>
                            <td class="px-6 py-4 text-center text-blue-700"><?php echo number_format($total_reg_shirts); ?></td>
                            <td class="px-6 py-4 text-center text-yellow-700"><?php echo number_format($total_shirt_order_shirts); ?></td>
                            <td class="px-6 py-4 text-center text-indigo-700 text-lg"><?php echo number_format($total_shirts_all); ?></td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Registration Summary Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden animate-fade-in-up delay-300">
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-pink-100 text-pink-600 rounded-lg">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">สรุปยอดผู้สมัครตามประเภท (Registration Summary)</h3>
                        <p class="text-sm text-slate-500">แยกตามประเภทการวิ่งและเพศ</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportTable('regTable', 'csv')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 hover:text-slate-800 transition-colors flex items-center gap-2">
                        <i class="fas fa-file-csv text-green-600"></i> CSV
                    </button>
                    <button onclick="exportTable('regTable', 'xlsx')" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 hover:text-slate-800 transition-colors flex items-center gap-2">
                        <i class="fas fa-file-excel text-green-600"></i> Excel
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="regTable" class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-lg">Category</th>
                            <th class="px-6 py-4 text-center text-blue-600">ชาย</th>
                            <th class="px-6 py-4 text-center text-pink-600">หญิง</th>
                            <th class="px-6 py-4 text-center text-slate-600 rounded-tr-lg">รวม</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($stats_by_cat_gender as $cat => $data): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-700"><?php echo $cat; ?></td>
                            <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo number_format($data['Male']); ?></td>
                            <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo number_format($data['Female']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full font-bold">
                                    <?php echo number_format($data['Total']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="bg-slate-50 font-bold">
                            <td class="px-6 py-4 text-slate-800">รวมทั้งสิ้น</td>
                            <td class="px-6 py-4 text-center text-blue-700"><?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Male'))); ?></td>
                            <td class="px-6 py-4 text-center text-pink-700"><?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Female'))); ?></td>
                            <td class="px-6 py-4 text-center text-slate-700 text-lg"><?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Total'))); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Category Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 animate-fade-in-up delay-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                        จำนวนผู้สมัครแยกตามประเภท
                    </h3>
                </div>
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Gender Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 animate-fade-in-up delay-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-8 bg-pink-500 rounded-full"></span>
                        สัดส่วนเพศ (ผู้สมัครวิ่ง)
                    </h3>
                </div>
                <div class="h-64 flex justify-center relative">
                    <canvas id="genderChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <div class="text-xs text-slate-400 uppercase font-bold">Total</div>
                            <div class="text-2xl font-bold text-slate-700"><?php echo count($registrations); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportTable(tableId, type) {
            const table = document.getElementById(tableId);
            const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
            const fileName = tableId === 'shirtTable' ? 'shirt_summary' : 'registration_summary';
            
            if (type === 'csv') {
                XLSX.writeFile(wb, fileName + '.csv');
            } else {
                XLSX.writeFile(wb, fileName + '.xlsx');
            }
        }
    </script>
    <script>
        // Chart Defaults
        Chart.defaults.font.family = "'Sarabun', sans-serif";
        Chart.defaults.color = '#64748b';
        
        // Category Chart
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        const gradientCat = ctxCat.createLinearGradient(0, 0, 0, 400);
        gradientCat.addColorStop(0, '#3b82f6');
        gradientCat.addColorStop(1, '#60a5fa');

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($by_category)); ?>,
                datasets: [{
                    label: 'จำนวนผู้สมัคร',
                    data: <?php echo json_encode(array_values($by_category)); ?>,
                    backgroundColor: gradientCat,
                    borderRadius: 6,
                    barThickness: 40,
                    hoverBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e2e8f0', drawBorder: false },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Gender Chart
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($by_gender)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($by_gender)); ?>,
                    backgroundColor: ['#3b82f6', '#ec4899', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                let value = context.raw;
                                let total = context.chart._metasets[context.datasetIndex].total;
                                let percentage = Math.round((value / total) * 100) + '%';
                                return label + value + ' (' + percentage + ')';
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
</body>
</html>