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
$reg_by_size_status = []; // [size => [pending => 0, approved => 0]]

foreach ($registrations as $reg) {
    $status = $reg['status'] ?? 'pending';
    $cat = $reg['category'] ?? 'Unknown';
    $gender = $reg['gender'] ?? 'Unknown';
    $size = $reg['shirt_size'] ?? '';
    // Normalize size with collar type
    $collar = $reg['collar_type'] ?? 'round';
    if (!empty($size) && $size !== 'ไม่รับเสื้อ' && $size !== '-' && $size !== 'No Shirt' && $size !== 'ไม่ต้องการเสื้อ') {
        if (strpos($size, '(') === false) {
            $suffix = ($collar === 'polo') ? ' (คอปก)' : ' (คอกลม)';
            $size .= $suffix;
        }
    }

    $qty = (int) ($reg['shirt_quantity'] ?? 1);

    // Category & Gender Stats
    if (!isset($stats_by_cat_gender[$cat])) {
        $stats_by_cat_gender[$cat] = ['Male' => 0, 'Female' => 0, 'Total' => 0];
    }
    if ($gender === 'Male')
        $stats_by_cat_gender[$cat]['Male']++;
    elseif ($gender === 'Female')
        $stats_by_cat_gender[$cat]['Female']++;
    $stats_by_cat_gender[$cat]['Total']++;

    // Status
    if (isset($by_status[$status])) {
        $by_status[$status]++;
    } else {
        $by_status[$status] = 1;
    }

    // Income
    $payment = (float) ($reg['payment_amount'] ?? 0);
    $reg_income += $payment;
    if ($status === 'approved') {
        $reg_income_approved += $payment;
    }

    // Category
    if (!isset($by_category[$cat]))
        $by_category[$cat] = 0;
    $by_category[$cat]++;

    // Gender
    if (!isset($by_gender[$gender]))
        $by_gender[$gender] = 0;
    $by_gender[$gender]++;

    // Size (only for registrations with shirt)
    if (!empty($size) && $size !== 'ไม่รับเสื้อ' && $size !== '-' && $size !== 'No Shirt' && $size !== 'ไม่ต้องการเสื้อ') {
        $reg_with_shirt++;
        if (!isset($reg_by_size[$size]))
            $reg_by_size[$size] = 0;
        $reg_by_size[$size] += $qty;

        // Count by status for status-based table
        if (!isset($reg_by_size_status[$size]))
            $reg_by_size_status[$size] = ['pending' => 0, 'approved' => 0];
        if ($status === 'approved') {
            $reg_by_size_status[$size]['approved'] += $qty;
        } elseif ($status === 'pending') {
            $reg_by_size_status[$size]['pending'] += $qty;
        }
    } else {
        $reg_without_shirt++;
    }
}

// Recalculate total reg shirts from sizes to be 100% consistent
$total_reg_shirts = array_sum($reg_by_size);
// Update reg_with_shirt if we want it to be exactly the number of shirts (usually 1:1 for runners)
// $reg_with_shirt = $total_reg_shirts; 


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
$shirt_order_by_size_status = []; // [size => [pending => 0, approved => 0]]

foreach ($shirtOrders as $order) {
    $status = $order['status'] ?? 'pending';
    $payment = (float) ($order['payment_amount'] ?? 0);
    $qty = (int) ($order['shirt_quantity'] ?? 0);
    $orderCollar = $order['collar_type'] ?? 'round';

    // Status count
    if (isset($shirt_order_by_status[$status])) {
        $shirt_order_by_status[$status]++;
    }

    // Income
    $shirt_order_income += $payment;
    if ($status === 'paid' || $status === 'shipped' || $status === 'completed') {
        $shirt_order_income_approved += $payment;
    }

    // Total shirts - we'll calculate this from parsed sizes for 100% consistency with the table
    // $shirt_order_total_shirts += $qty; 

    // Parse shirt sizes (format: "S:2,M:3,L:1")
    $sizes_str = $order['shirt_sizes'] ?? '';
    if (!empty($sizes_str)) {
        $size_pairs = explode(',', $sizes_str);
        foreach ($size_pairs as $pair) {
            $parts = explode(':', trim($pair));
            if (count($parts) == 2) {
                $size = trim($parts[0]);

                // Normalize older orders that might miss the suffix
                if (!empty($size) && strpos($size, '(') === false && $size !== 'No Shirt' && $size !== '-') {
                    $suffix = ($orderCollar === 'polo') ? ' (คอปก)' : ' (คอกลม)';
                    $size .= $suffix;
                }

                $count = (int) trim($parts[1]);
                if (empty($size) || $size === 'No Shirt' || $size === 'ไม่รับเสื้อ' || $size === 'ไม่ต้องการเสื้อ' || $size === '-')
                    continue;
                if (!isset($shirt_order_by_size[$size]))
                    $shirt_order_by_size[$size] = 0;
                $shirt_order_by_size[$size] += $count;

                // Count by status for status-based table
                if (!isset($shirt_order_by_size_status[$size]))
                    $shirt_order_by_size_status[$size] = ['pending' => 0, 'approved' => 0];
                if ($status === 'paid' || $status === 'shipped' || $status === 'completed') {
                    $shirt_order_by_size_status[$size]['approved'] += $count;
                } elseif ($status === 'pending') {
                    $shirt_order_by_size_status[$size]['pending'] += $count;
                }
            }
        }
    }
}

$total_shirt_order_shirts = array_sum($shirt_order_by_size);

// ===== COMBINED STATS =====
$total_income = $reg_income + $shirt_order_income;
$total_income_approved = $reg_income_approved + $shirt_order_income_approved;

// Combined shirt sizes
$combined_sizes = [];
$all_sizes = array_unique(array_merge(array_keys($reg_by_size), array_keys($shirt_order_by_size)));
sort($all_sizes);
foreach ($all_sizes as $size) {
    if ($size === 'No Shirt' || $size === 'ไม่รับเสื้อ' || $size === 'ไม่ต้องการเสื้อ' || $size === '-')
        continue;
    $combined_sizes[$size] = [
        'registration' => $reg_by_size[$size] ?? 0,
        'shirt_order' => $shirt_order_by_size[$size] ?? 0,
        'total' => ($reg_by_size[$size] ?? 0) + ($shirt_order_by_size[$size] ?? 0)
    ];
}

$total_shirts_all = $total_reg_shirts + $total_shirt_order_shirts;

// Combined shirt sizes by status
$combined_size_by_status = [];
foreach ($all_sizes as $size) {
    if ($size === 'No Shirt' || $size === 'ไม่รับเสื้อ' || $size === 'ไม่ต้องการเสื้อ' || $size === '-')
        continue;

    $pending = ($reg_by_size_status[$size]['pending'] ?? 0) + ($shirt_order_by_size_status[$size]['pending'] ?? 0);
    $approved = ($reg_by_size_status[$size]['approved'] ?? 0) + ($shirt_order_by_size_status[$size]['approved'] ?? 0);

    $combined_size_by_status[$size] = [
        'pending' => $pending,
        'approved' => $approved,
        'total' => $pending + $approved
    ];
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- K2D Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'k2d': ['K2D', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'K2D', sans-serif;
        }

        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glass effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Shine animation */
        @keyframes shine {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .shine-effect {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            background-size: 200% 100%;
            animation: shine 3s infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50 font-k2d text-slate-600 min-h-screen">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 animate-fade-in-up">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold gradient-text">Dashboard</h1>
                <p class="text-slate-500 mt-2 font-medium">ภาพรวมข้อมูลการสมัครและการสั่งซื้อ Phichai Run 2026</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-2 md:gap-3">
                <div
                    class="text-sm text-slate-600 bg-white/80 backdrop-blur-sm px-4 py-2.5 rounded-xl shadow-sm border border-white/50 font-medium flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <i class="far fa-clock text-emerald-500"></i> <?php echo date('H:i'); ?> น.
                </div>
                <div
                    class="text-sm text-slate-600 bg-white/80 backdrop-blur-sm px-4 py-2.5 rounded-xl shadow-sm border border-white/50 font-medium flex items-center gap-2">
                    <i class="far fa-calendar-alt text-blue-500"></i> <?php echo date('d M Y'); ?>
                </div>
            </div>
        </div>

        <!-- 1. Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <!-- Total Revenue -->
            <div
                class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-lg shadow-emerald-500/5 border border-white/50 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 animate-fade-in-up delay-100 relative overflow-hidden">
                <div
                    class="absolute right-0 top-0 h-full w-1.5 bg-gradient-to-b from-emerald-400 to-teal-600 rounded-l-full">
                </div>
                <div
                    class="absolute -right-8 -top-8 w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider">รายได้รวม
                            (อนุมัติแล้ว)</div>
                        <div
                            class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-2 group-hover:text-emerald-600 transition-colors">
                            ฿<?php echo number_format($total_income_approved); ?></div>
                        <div class="text-[10px] md:text-xs text-slate-400 mt-1">จากทั้งหมด
                            ฿<?php echo number_format($total_income); ?></div>
                    </div>
                    <div
                        class="p-3 md:p-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-wallet text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Runners -->
            <div
                class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-lg shadow-blue-500/5 border border-white/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1 animate-fade-in-up delay-200 relative overflow-hidden">
                <div
                    class="absolute right-0 top-0 h-full w-1.5 bg-gradient-to-b from-blue-400 to-indigo-600 rounded-l-full">
                </div>
                <div
                    class="absolute -right-8 -top-8 w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                            ผู้สมัครวิ่ง</div>
                        <div
                            class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-2 group-hover:text-blue-600 transition-colors">
                            <?php echo number_format(count($registrations)); ?>
                        </div>
                        <div class="text-[10px] md:text-xs text-slate-400 mt-1">คน</div>
                    </div>
                    <div
                        class="p-3 md:p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-running text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Shirt Orders -->
            <div
                class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-lg shadow-amber-500/5 border border-white/50 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1 animate-fade-in-up delay-300 relative overflow-hidden">
                <div
                    class="absolute right-0 top-0 h-full w-1.5 bg-gradient-to-b from-amber-400 to-orange-600 rounded-l-full">
                </div>
                <div
                    class="absolute -right-8 -top-8 w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                            ออเดอร์สั่งซื้อเสื้อ</div>
                        <div
                            class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-2 group-hover:text-amber-600 transition-colors">
                            <?php echo number_format($shirt_order_count); ?>
                        </div>
                        <div class="text-[10px] md:text-xs text-slate-400 mt-1">รายการ</div>
                    </div>
                    <div
                        class="p-3 md:p-4 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl text-white shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shopping-bag text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Shirts -->
            <div
                class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-lg shadow-violet-500/5 border border-white/50 hover:shadow-xl hover:shadow-violet-500/10 transition-all duration-300 hover:-translate-y-1 animate-fade-in-up delay-300 relative overflow-hidden">
                <div
                    class="absolute right-0 top-0 h-full w-1.5 bg-gradient-to-b from-violet-400 to-purple-600 rounded-l-full">
                </div>
                <div
                    class="absolute -right-8 -top-8 w-24 h-24 bg-gradient-to-br from-violet-100 to-purple-100 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500">
                </div>
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <div class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                            ยอดเสื้อรวมทั้งหมด</div>
                        <div
                            class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-2 group-hover:text-violet-600 transition-colors">
                            <?php echo number_format($total_shirts_all); ?>
                        </div>
                        <div class="text-[10px] md:text-xs text-slate-400 mt-1">ตัว (วิ่ง + สั่งซื้อ)</div>
                    </div>
                    <div
                        class="p-3 md:p-4 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl text-white shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-tshirt text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Detailed Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mb-8">
            <!-- Registration Stats -->
            <div
                class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 overflow-hidden animate-fade-in-up delay-200">
                <div
                    class="p-5 md:p-6 border-b border-slate-100/80 flex justify-between items-center bg-gradient-to-r from-blue-50/50 to-indigo-50/50">
                    <h3 class="font-bold text-base md:text-lg text-slate-800 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></span>
                        ข้อมูลการสมัครวิ่ง
                    </h3>
                    <span
                        class="text-[10px] md:text-xs font-bold px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg shadow-lg shadow-blue-500/30">🏃
                        Runners</span>
                </div>
                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div
                            class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100 hover:shadow-md transition-shadow">
                            <div class="text-[10px] md:text-xs text-slate-400 mb-1 uppercase font-bold">รายได้ (อนุมัติ)
                            </div>
                            <div class="text-lg md:text-xl font-extrabold text-emerald-600">
                                ฿<?php echo number_format($reg_income_approved); ?></div>
                        </div>
                        <div
                            class="p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-100 hover:shadow-md transition-shadow">
                            <div class="text-[10px] md:text-xs text-slate-400 mb-1 uppercase font-bold">รอตรวจสอบ</div>
                            <div class="text-lg md:text-xl font-extrabold text-amber-600">
                                <?php echo number_format($by_status['pending'] ?? 0); ?>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center text-sm p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-slate-600 font-medium flex items-center gap-2"><i
                                    class="fas fa-check-circle text-emerald-500"></i> อนุมัติแล้ว</span>
                            <span
                                class="font-bold text-slate-800 bg-emerald-50 px-3 py-1 rounded-lg"><?php echo number_format($by_status['approved'] ?? 0); ?></span>
                        </div>
                        <div
                            class="flex justify-between items-center text-sm p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-slate-600 font-medium flex items-center gap-2"><i
                                    class="fas fa-tshirt text-indigo-500"></i> รับเสื้อ</span>
                            <span
                                class="font-bold text-slate-800 bg-indigo-50 px-3 py-1 rounded-lg"><?php echo number_format($reg_with_shirt); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shirt Order Stats -->
            <div
                class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 overflow-hidden animate-fade-in-up delay-300">
                <div
                    class="p-5 md:p-6 border-b border-slate-100/80 flex justify-between items-center bg-gradient-to-r from-amber-50/50 to-orange-50/50">
                    <h3 class="font-bold text-base md:text-lg text-slate-800 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-gradient-to-b from-amber-500 to-orange-600 rounded-full"></span>
                        ข้อมูลการสั่งซื้อเสื้อ
                    </h3>
                    <span
                        class="text-[10px] md:text-xs font-bold px-3 py-1.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg shadow-lg shadow-amber-500/30">👕
                        Shop</span>
                </div>
                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div
                            class="p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border border-emerald-100 hover:shadow-md transition-shadow">
                            <div class="text-[10px] md:text-xs text-slate-400 mb-1 uppercase font-bold">รายได้ (อนุมัติ)
                            </div>
                            <div class="text-lg md:text-xl font-extrabold text-emerald-600">
                                ฿<?php echo number_format($shirt_order_income_approved); ?></div>
                        </div>
                        <div
                            class="p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-100 hover:shadow-md transition-shadow">
                            <div class="text-[10px] md:text-xs text-slate-400 mb-1 uppercase font-bold">รอตรวจสอบ</div>
                            <div class="text-lg md:text-xl font-extrabold text-amber-600">
                                <?php echo number_format($shirt_order_by_status['pending'] ?? 0); ?>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center text-sm p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-slate-600 font-medium flex items-center gap-2"><i
                                    class="fas fa-box text-blue-500"></i> ชำระเงิน/ส่งแล้ว</span>
                            <span
                                class="font-bold text-slate-800 bg-blue-50 px-3 py-1 rounded-lg"><?php echo number_format(($shirt_order_by_status['paid'] ?? 0) + ($shirt_order_by_status['shipped'] ?? 0) + ($shirt_order_by_status['completed'] ?? 0)); ?></span>
                        </div>
                        <div
                            class="flex justify-between items-center text-sm p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-slate-600 font-medium flex items-center gap-2"><i
                                    class="fas fa-layer-group text-violet-500"></i> จำนวนเสื้อรวม</span>
                            <span
                                class="font-bold text-slate-800 bg-violet-50 px-3 py-1 rounded-lg"><?php echo number_format($shirt_order_total_shirts); ?>
                                ตัว</span>
                        </div>
                        <div
                            class="flex justify-between items-center text-sm p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-slate-600 font-medium flex items-center gap-2"><i
                                    class="fas fa-times-circle text-rose-500"></i> ยกเลิก</span>
                            <span
                                class="font-bold text-slate-800 bg-rose-50 px-3 py-1 rounded-lg"><?php echo number_format($shirt_order_by_status['cancelled'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Shirt Size Matrix -->
        <div
            class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 mb-8 overflow-hidden animate-fade-in-up delay-300">
            <div
                class="p-5 md:p-6 border-b border-slate-100/80 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-violet-50/50 to-purple-50/50">
                <div class="flex items-center gap-3">
                    <div
                        class="p-3 bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-xl shadow-lg shadow-violet-500/30">
                        <i class="fas fa-ruler-combined text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg md:text-xl text-slate-800">สรุปยอดเสื้อตามไซส์</h3>
                        <p class="text-xs md:text-sm text-slate-500">รวมยอดจากทั้งผู้สมัครวิ่งและออเดอร์สั่งซื้อแยก</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <button onclick="exportTable('shirtTable', 'csv')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2">
                        <i class="fas fa-file-csv text-emerald-600"></i> CSV
                    </button>
                    <button onclick="exportTable('shirtTable', 'xlsx')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2">
                        <i class="fas fa-file-excel text-emerald-600"></i> Excel
                    </button>
                    <div class="h-6 w-px bg-slate-200 mx-1 hidden md:block"></div>
                    <div
                        class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 text-xs font-bold flex items-center gap-1.5 border border-blue-100">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div> วิ่ง
                    </div>
                    <div
                        class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-50 to-orange-50 text-amber-700 text-xs font-bold flex items-center gap-1.5 border border-amber-100">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div> สั่งซื้อ
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="shirtTable" class="w-full text-sm text-left">
                    <thead
                        class="bg-gradient-to-r from-slate-50 to-slate-100/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-4 md:px-6 py-4">Size</th>
                            <th class="px-4 md:px-6 py-4 text-center text-blue-600">จากวิ่ง</th>
                            <th class="px-4 md:px-6 py-4 text-center text-amber-600">จากสั่งซื้อ</th>
                            <th class="px-4 md:px-6 py-4 text-center text-violet-600">รวมทั้งหมด</th>
                            <th class="px-4 md:px-6 py-4 w-1/4 hidden md:table-cell">สัดส่วน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/80">
                        <?php foreach ($combined_sizes as $size => $data):
                            $percent = $total_shirts_all > 0 ? ($data['total'] / $total_shirts_all) * 100 : 0;
                            ?>
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-violet-50/30 transition-all">
                                <td class="px-4 md:px-6 py-4 font-bold text-slate-800"><?php echo $size; ?></td>
                                <td class="px-4 md:px-6 py-4 text-center font-medium text-slate-600">
                                    <?php echo number_format($data['registration']); ?>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center font-medium text-slate-600">
                                    <?php echo number_format($data['shirt_order']); ?>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center">
                                    <span
                                        class="inline-block px-3 py-1.5 bg-gradient-to-r from-violet-50 to-purple-50 text-violet-700 rounded-lg font-bold border border-violet-100">
                                        <?php echo number_format($data['total']); ?>
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-4 hidden md:table-cell">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                                            <?php if ($data['total'] > 0):
                                                $reg_pct = ($data['registration'] / $data['total']) * 100;
                                                $ord_pct = ($data['shirt_order'] / $data['total']) * 100;
                                                ?>
                                                <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500"
                                                    style="width: <?php echo $reg_pct; ?>%"></div>
                                                <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500"
                                                    style="width: <?php echo $ord_pct; ?>%"></div>
                                            <?php endif; ?>
                                        </div>
                                        <span
                                            class="text-xs text-slate-500 font-bold w-12 text-right"><?php echo number_format($percent, 1); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 font-bold">
                            <td class="px-4 md:px-6 py-4 text-slate-800 text-base">รวมทั้งสิ้น</td>
                            <td class="px-4 md:px-6 py-4 text-center text-blue-700 text-base">
                                <?php echo number_format($total_reg_shirts); ?>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-center text-amber-700 text-base">
                                <?php echo number_format($total_shirt_order_shirts); ?>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-center text-violet-700 text-lg">
                                <?php echo number_format($total_shirts_all); ?>
                            </td>
                            <td class="px-4 md:px-6 py-4 hidden md:table-cell"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3.1 Shirt Size Summary by Status -->
        <div
            class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 mb-8 overflow-hidden animate-fade-in-up delay-300">
            <div
                class="p-5 md:p-6 border-b border-slate-100/80 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-emerald-50/50 to-blue-50/50">
                <div class="flex items-center gap-3">
                    <div
                        class="p-3 bg-gradient-to-br from-emerald-500 to-blue-600 text-white rounded-xl shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg md:text-xl text-slate-800">สรุปยอดเสื้อแยกตามสถานะ</h3>
                        <p class="text-xs md:text-sm text-slate-500">ยอดรวมทั้งจากวิ่งและสั่งซื้อ
                            แบ่งเป็นรอตรวจสอบและอนุมัติแล้ว</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportTable('shirtStatusTable', 'csv')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2">
                        <i class="fas fa-file-csv text-emerald-600"></i> CSV
                    </button>
                    <button onclick="exportTable('shirtStatusTable', 'xlsx')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2">
                        <i class="fas fa-file-excel text-emerald-600"></i> Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="shirtStatusTable" class="w-full text-sm text-left">
                    <thead
                        class="bg-gradient-to-r from-slate-50 to-slate-100/80 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-4 md:px-6 py-4">Size</th>
                            <th class="px-4 md:px-6 py-4 text-center text-amber-600">รอตรวจสอบ</th>
                            <th class="px-4 md:px-6 py-4 text-center text-emerald-600">อนุมัติแล้ว</th>
                            <th class="px-4 md:px-6 py-4 text-center text-violet-600">รวมทั้งหมด</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/80">
                        <?php
                        $grand_pending = 0;
                        $grand_approved = 0;
                        $grand_total = 0;
                        foreach ($combined_size_by_status as $size => $data):
                            $grand_pending += $data['pending'];
                            $grand_approved += $data['approved'];
                            $grand_total += $data['total'];
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-4 md:px-6 py-4 font-bold text-slate-800"><?php echo $size; ?></td>
                                <td class="px-4 md:px-6 py-4 text-center font-medium">
                                    <?php echo number_format($data['pending']); ?></td>
                                <td class="px-4 md:px-6 py-4 text-center font-medium text-emerald-600">
                                    <?php echo number_format($data['approved']); ?></td>
                                <td class="px-4 md:px-6 py-4 text-center">
                                    <span
                                        class="inline-block px-3 py-1 bg-violet-50 text-violet-700 rounded-lg font-bold border border-violet-100">
                                        <?php echo number_format($data['total']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 font-bold">
                            <td class="px-4 md:px-6 py-4 text-slate-800">รวมทั้งสิ้น</td>
                            <td class="px-4 md:px-6 py-4 text-center text-amber-700">
                                <?php echo number_format($grand_pending); ?></td>
                            <td class="px-4 md:px-6 py-4 text-center text-emerald-700">
                                <?php echo number_format($grand_approved); ?></td>
                            <td class="px-4 md:px-6 py-4 text-center text-violet-700 text-lg">
                                <?php echo number_format($grand_total); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Registration Summary Table -->
        <div
            class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 mb-8 overflow-hidden animate-fade-in-up delay-300">
            <div
                class="p-5 md:p-6 border-b border-slate-100/80 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-pink-50/50 to-rose-50/50">
                <div class="flex items-center gap-3">
                    <div
                        class="p-3 bg-gradient-to-br from-pink-500 to-rose-600 text-white rounded-xl shadow-lg shadow-pink-500/30">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg md:text-xl text-slate-800">สรุปยอดผู้สมัครตามประเภท</h3>
                        <p class="text-xs md:text-sm text-slate-500">แยกตามประเภทการวิ่งและเพศ</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportTable('regTable', 'csv')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fas fa-file-csv text-emerald-600"></i> CSV
                    </button>
                    <button onclick="exportTable('regTable', 'xlsx')"
                        class="px-3 py-1.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fas fa-file-excel text-emerald-600"></i> Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table id="regTable" class="w-full text-sm text-left">
                    <thead
                        class="bg-gradient-to-r from-slate-50 to-slate-100 text-slate-600 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tag text-slate-400"></i>
                                    Category
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-mars text-blue-500"></i>
                                    <span class="text-blue-600">ชาย</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-venus text-pink-500"></i>
                                    <span class="text-pink-600">หญิง</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center rounded-tr-xl">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-calculator text-indigo-500"></i>
                                    <span class="text-slate-600">รวม</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($stats_by_cat_gender as $cat => $data): ?>
                            <tr
                                class="hover:bg-gradient-to-r hover:from-slate-50/50 hover:to-transparent transition-all duration-200 group">
                                <td class="px-6 py-4">
                                    <span
                                        class="font-bold text-slate-700 group-hover:text-blue-700 transition-colors"><?php echo $cat; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-semibold text-sm">
                                        <?php echo number_format($data['Male']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 bg-pink-50 text-pink-700 rounded-lg font-semibold text-sm">
                                        <?php echo number_format($data['Female']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 bg-gradient-to-r from-slate-100 to-slate-200 text-slate-700 rounded-lg font-bold text-sm shadow-sm">
                                        <?php echo number_format($data['Total']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 font-bold">
                            <td class="px-6 py-4 rounded-bl-xl">
                                <span class="text-slate-800 flex items-center gap-2">
                                    <i class="fas fa-sigma text-indigo-500"></i>
                                    รวมทั้งสิ้น
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 bg-blue-500 text-white rounded-lg font-bold text-sm shadow-md">
                                    <?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Male'))); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 bg-pink-500 text-white rounded-lg font-bold text-sm shadow-md">
                                    <?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Female'))); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center rounded-br-xl">
                                <span
                                    class="inline-flex items-center justify-center min-w-[3rem] px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl font-bold text-lg shadow-lg">
                                    <?php echo number_format(array_sum(array_column($stats_by_cat_gender, 'Total'))); ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Category Chart -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 animate-fade-in-up delay-200 group">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-3">
                        <span
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-bar text-white"></i>
                        </span>
                        จำนวนผู้สมัครแยกตามประเภท
                    </h3>
                </div>
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Gender Chart -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 animate-fade-in-up delay-200 group">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-3">
                        <span
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg shadow-pink-500/30 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-pie text-white"></i>
                        </span>
                        สัดส่วนเพศ (ผู้สมัครวิ่ง)
                    </h3>
                </div>
                <div class="h-64 flex justify-center relative">
                    <canvas id="genderChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div
                            class="text-center bg-white/80 backdrop-blur-sm rounded-full w-24 h-24 flex flex-col items-center justify-center shadow-inner">
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Total</div>
                            <div
                                class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-pink-600 bg-clip-text text-transparent">
                                <?php echo count($registrations); ?>
                            </div>
                            <div class="text-[10px] text-slate-400">คน</div>
                        </div>
                    </div>
                </div>
                <!-- Gender Legend -->
                <div class="flex justify-center gap-6 mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm"></span>
                        <span class="text-sm text-slate-600 font-medium">ชาย</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-pink-500 shadow-sm"></span>
                        <span class="text-sm text-slate-600 font-medium">หญิง</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportTable(tableId, type) {
            const table = document.getElementById(tableId);
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
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
        Chart.defaults.font.family = "'K2D', 'Sarabun', sans-serif";
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
                            label: function (context) {
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