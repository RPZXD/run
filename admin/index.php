<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/Registration.php';

$database = new Database();
$db = $database->connect();
$registration = new Registration($db);

$message = '';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $reason = $_POST['reject_reason'] ?? '';
    
    if ($registration->updateStatus($id, $status, $reason)) {
        $message = "อัพเดทสถานะเรียบร้อยแล้ว";
    } else {
        $message = "เกิดข้อผิดพลาดในการอัพเดท";
    }
}

// Handle Full Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_info') {
    $id = $_POST['id'];
    if ($registration->update($id, $_POST)) {
        $message = "อัพเดทข้อมูลเรียบร้อยแล้ว";
    } else {
        $message = "เกิดข้อผิดพลาดในการอัพเดทข้อมูล";
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    if ($registration->delete($id)) {
        $message = "ลบข้อมูลเรียบร้อยแล้ว";
    } else {
        $message = "เกิดข้อผิดพลาดในการลบ";
    }
}

$stmt = $registration->readAll();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Stats
$stats = [
    'total' => count($registrations),
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'income' => 0
];

foreach ($registrations as $reg) {
    $status = $reg['status'] ?? 'pending';
    if (isset($stats[$status])) {
        $stats[$status]++;
    }
    if ($status === 'approved') {
        $stats['income'] += (float)($reg['payment_amount'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG"> 
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- K2D Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
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
        body { font-family: 'K2D', sans-serif; }
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
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Table row hover effect */
        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
            transform: scale(1.002);
        }
        
        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50 font-k2d text-slate-600 min-h-screen">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <!-- Total -->
            <div class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-sm border border-white/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">ผู้สมัครทั้งหมด</p>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-1"><?php echo number_format($stats['total']); ?></h3>
                        <p class="text-[10px] text-slate-400 mt-1 hidden md:block">คน</p>
                    </div>
                    <div class="p-3 md:p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pending -->
            <div class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-sm border border-white/50 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">รอตรวจสอบ</p>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-amber-600 mt-1"><?php echo number_format($stats['pending']); ?></h3>
                        <p class="text-[10px] text-slate-400 mt-1 hidden md:block">รายการ</p>
                    </div>
                    <div class="p-3 md:p-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl text-white shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-clock text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-sm border border-white/50 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">อนุมัติแล้ว</p>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-emerald-600 mt-1"><?php echo number_format($stats['approved']); ?></h3>
                        <p class="text-[10px] text-slate-400 mt-1 hidden md:block">รายการ</p>
                    </div>
                    <div class="p-3 md:p-4 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-check-circle text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Income -->
            <div class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 md:p-6 shadow-sm border border-white/50 hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">ยอดเงินรวม</p>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-rose-600 mt-1">฿<?php echo number_format($stats['income']); ?></h3>
                        <p class="text-[10px] text-slate-400 mt-1 hidden md:block">บาท</p>
                    </div>
                    <div class="p-3 md:p-4 bg-gradient-to-br from-rose-400 to-pink-500 rounded-2xl text-white shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-coins text-lg md:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl shadow-slate-200/50 border border-white/50 overflow-hidden animate-fade-in-up">
            <div class="p-4 md:p-6 border-b border-slate-100/80 flex flex-col md:flex-row justify-between items-center gap-4 bg-gradient-to-r from-white to-slate-50/50">
                <h2 class="text-lg md:text-xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl shadow-lg shadow-blue-500/30">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    <span class="gradient-text">รายการผู้สมัคร</span>
                </h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="ค้นหาชื่อ, เบอร์โทร..." class="pl-10 pr-4 py-2.5 border-2 border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 w-full sm:w-64 transition-all bg-white/80 hover:bg-white hover:border-slate-300 placeholder:text-slate-400">
                    </div>
                    <div class="flex gap-2">
                        <select id="statusFilter" onchange="filterTable()" class="px-4 py-2.5 border-2 border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-400 bg-white/80 hover:bg-white hover:border-slate-300 text-slate-600 cursor-pointer transition-all font-medium">
                            <option value="all">📋 สถานะทั้งหมด</option>
                            <option value="pending">⏳ รอตรวจสอบ</option>
                            <option value="approved">✅ อนุมัติแล้ว</option>
                            <option value="rejected">❌ ปฏิเสธ</option>
                        </select>
                        <button onclick="exportTableToCSV('registrations.csv')" class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl text-sm font-bold hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-file-excel"></i> <span class="hidden sm:inline">Export</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="registrationsTable">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100/80 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200/50">
                            <th class="px-4 md:px-6 py-4 font-bold">ID</th>
                            <th class="px-4 md:px-6 py-4 font-bold">ผู้สมัคร</th>
                            <th class="px-4 md:px-6 py-4 font-bold hidden md:table-cell">ข้อมูลการวิ่ง</th>
                            <th class="px-4 md:px-6 py-4 font-bold hidden lg:table-cell">การชำระเงิน</th>
                            <th class="px-4 md:px-6 py-4 font-bold text-center">สถานะ</th>
                            <th class="px-4 md:px-6 py-4 font-bold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/80">
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-inbox text-4xl text-slate-300"></i>
                                        </div>
                                        <p class="text-lg font-medium">ยังไม่มีข้อมูลผู้สมัคร</p>
                                        <p class="text-sm text-slate-400 mt-1">ข้อมูลจะแสดงที่นี่เมื่อมีผู้สมัครใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $row): ?>
                                <tr class="table-row-hover group">
                                    <td class="px-4 md:px-6 py-4">
                                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1.5 rounded-lg group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 md:h-11 md:w-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/30 ring-2 ring-white group-hover:scale-110 transition-transform duration-300">
                                                    <?php echo mb_substr($row['first_name'] ?? $row['full_name'], 0, 1); ?>
                                                </div>
                                                <?php if(($row['gender'] ?? '') == 'Male'): ?>
                                                    <div class="absolute -bottom-0.5 -right-0.5 bg-blue-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px] border-2 border-white shadow"><i class="fas fa-mars"></i></div>
                                                <?php elseif(($row['gender'] ?? '') == 'Female'): ?>
                                                    <div class="absolute -bottom-0.5 -right-0.5 bg-pink-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px] border-2 border-white shadow"><i class="fas fa-venus"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm group-hover:text-blue-600 transition-colors"><?php echo $row['full_name']; ?></div>
                                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                                    <span class="bg-slate-100 px-2 py-0.5 rounded-md text-[10px] text-slate-500 font-medium group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                                        <i class="fas fa-phone-alt text-[9px] mr-1"></i> <?php echo $row['phone']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 hidden md:table-cell">
                                        <div class="flex flex-col items-start gap-2">
                                            <?php 
                                                $catColor = 'bg-slate-100 text-slate-600 border-slate-200';
                                                $catIcon = '🏃';
                                                if(strpos($row['category'], 'VIP') !== false) {
                                                    $catColor = 'bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 border-amber-300 shadow-amber-100';
                                                    $catIcon = '⭐';
                                                } elseif(strpos($row['category'], 'Fun Run') !== false) {
                                                    $catColor = 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 border-emerald-200';
                                                    $catIcon = '🏃';
                                                } elseif(strpos($row['category'], 'Walk') !== false) {
                                                    $catColor = 'bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 border-blue-200';
                                                    $catIcon = '🚶';
                                                }
                                            ?>
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold border shadow-sm <?php echo $catColor; ?>">
                                                <?php echo $catIcon; ?> <?php echo $row['category']; ?>
                                            </span>
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 group-hover:bg-indigo-50 group-hover:border-indigo-200 transition-colors">
                                                    <i class="fas fa-tshirt text-indigo-400"></i> 
                                                    <span class="font-bold text-slate-700"><?php echo $row['shirt_size']; ?></span>
                                                </span>
                                                <?php 
                                                $collarType = $row['collar_type'] ?? 'round';
                                                $collarLabel = $collarType === 'polo' ? 'คอปก' : 'คอกลม';
                                                $collarStyle = $collarType === 'polo' ? 'bg-orange-50 text-orange-600 border-orange-200' : 'bg-slate-50 text-slate-500 border-slate-200';
                                                ?>
                                                <span class="inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-lg font-medium <?php echo $collarStyle; ?> border" title="ประเภทคอ">
                                                    <?php echo $collarLabel; ?>
                                                </span>
                                                <?php if(($row['shipping_method'] ?? '') == 'POST'): ?>
                                                    <span class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-200 font-medium" title="จัดส่งไปรษณีย์">
                                                        <i class="fas fa-truck text-[10px]"></i> ส่ง ปณ.
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 hidden lg:table-cell">
                                        <?php if (!empty($row['payment_date'])): ?>
                                            <div class="flex flex-col">
                                                <span class="text-base font-extrabold text-emerald-600">฿<?php echo number_format($row['payment_amount']); ?></span>
                                                <span class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                                    <i class="far fa-calendar-alt"></i> <?php echo date('d/m/y', strtotime($row['payment_date'])); ?>
                                                    <span class="text-slate-300">•</span>
                                                    <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['payment_time'])); ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-400 text-xs font-medium border border-slate-200">
                                                <i class="fas fa-minus-circle mr-1.5"></i> ยังไม่แจ้ง
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-center">
                                        <?php 
                                            $statusStyles = [
                                                'pending' => 'bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 border-amber-300 shadow-amber-100',
                                                'approved' => 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 border-emerald-300 shadow-emerald-100',
                                                'rejected' => 'bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border-red-300 shadow-red-100'
                                            ];
                                            $statusLabels = [
                                                'pending' => '⏳ รอตรวจสอบ',
                                                'approved' => '✅ อนุมัติแล้ว',
                                                'rejected' => '❌ ปฏิเสธ'
                                            ];
                                            $s = $row['status'] ?? 'pending';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold border shadow-sm <?php echo $statusStyles[$s]; ?>" data-status="<?php echo $statusLabels[$s]; ?>">
                                            <?php echo $statusLabels[$s]; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <?php if ($row['payment_slip']): ?>
                                                <button onclick='viewSlip(<?php echo json_encode($row); ?>)' class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 text-white hover:shadow-lg hover:shadow-purple-500/40 hover:scale-110 flex items-center justify-center transition-all duration-300" title="ดูสลิป">
                                                    <i class="fas fa-receipt text-sm"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button onclick='openModal(<?php echo json_encode($row); ?>)' class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white hover:shadow-lg hover:shadow-blue-500/40 hover:scale-110 flex items-center justify-center transition-all duration-300" title="แก้ไข">
                                                <i class="fas fa-pen text-sm"></i>
                                            </button>
                                            
                                            <form method="POST" onsubmit="return confirmAction(event, 'ยืนยันการลบข้อมูลนี้?', 'ลบข้อมูล');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white hover:shadow-lg hover:shadow-red-500/40 hover:scale-110 flex items-center justify-center transition-all duration-300" title="ลบ">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            <div class="px-4 md:px-6 py-4 border-t border-slate-100/80 bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-sm text-slate-600 font-medium">แสดง <span class="font-bold text-slate-800"><?php echo count($registrations); ?></span> รายการ</span>
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 rounded-lg border-2 border-slate-200 bg-white text-slate-500 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-2" disabled>
                        <i class="fas fa-chevron-left text-xs"></i> ก่อนหน้า
                    </button>
                    <div class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold shadow-lg shadow-blue-500/30">1</div>
                    <button class="px-4 py-2 rounded-lg border-2 border-slate-200 bg-white text-slate-500 text-sm font-medium hover:bg-slate-50 hover:border-slate-300 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-2" disabled>
                        ถัดไป <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
        <div class="relative top-4 md:top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all scale-100 mb-10 mx-4">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 flex justify-between items-center sticky top-0 z-10">
                <h3 class="text-lg font-bold text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    แก้ไขข้อมูลผู้สมัคร
                </h3>
                <button onclick="closeModal()" class="w-10 h-10 rounded-xl bg-white/20 text-white hover:bg-white/30 transition-colors flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-4 md:p-6 max-h-[80vh] overflow-y-auto">
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="update_info">
                    <input type="hidden" name="id" id="modal_id">
                    
                    <!-- Status Section -->
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-2xl border border-blue-100">
                        <h4 class="text-blue-800 font-bold mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                            <i class="fas fa-info-circle"></i> สถานะการสมัคร
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">สถานะ</label>
                                <select name="status" id="modal_status" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-400 bg-white transition-all font-medium" onchange="toggleReason()">
                                    <option value="pending">⏳ รอตรวจสอบ</option>
                                    <option value="approved">✅ อนุมัติ (ตรวจสอบแล้ว)</option>
                                    <option value="rejected">❌ ปฏิเสธ / มีปัญหา</option>
                                </select>
                            </div>
                            <div id="reason_div" class="hidden">
                                <label class="block text-slate-700 text-sm font-bold mb-2">เหตุผล (กรณีปฏิเสธ)</label>
                                <input type="text" name="reject_reason" id="modal_reason" class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-500/20 focus:border-red-400 bg-white transition-all" placeholder="ระบุเหตุผล...">
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <h4 class="text-slate-800 font-bold mb-4 pb-3 border-b-2 border-slate-200 flex items-center gap-2">
                        <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fas fa-user"></i>
                        </span>
                        ข้อมูลส่วนตัว
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">คำนำหน้า</label>
                            <select name="prefix" id="modal_prefix" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all">
                                <optgroup label="บุคคลทั่วไป">
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="ด.ช.">ด.ช.</option>
                                    <option value="ด.ญ.">ด.ญ.</option>
                                </optgroup>
                                <optgroup label="วุฒิการศึกษา">
                                    <option value="ดร.">ดร.</option>
                                    <option value="ผศ.">ผศ.</option>
                                    <option value="รศ.">รศ.</option>
                                    <option value="ศ.">ศ.</option>
                                    <option value="ผศ.ดร.">ผศ.ดร.</option>
                                    <option value="รศ.ดร.">รศ.ดร.</option>
                                    <option value="ศ.ดร.">ศ.ดร.</option>
                                </optgroup>
                                <optgroup label="วิชาชีพแพทย์">
                                    <option value="นพ.">นพ.</option>
                                    <option value="พญ.">พญ.</option>
                                    <option value="ทพ.">ทพ.</option>
                                    <option value="ทพญ.">ทพญ.</option>
                                    <option value="สพ.ญ.">สพ.ญ.</option>
                                    <option value="น.สพ.">น.สพ.</option>
                                </optgroup>
                                <optgroup label="ยศตำรวจ/ทหาร">
                                    <option value="ว่าที่ร้อยตรี">ว่าที่ร้อยตรี</option>
                                    <option value="ร.ต.ต.">ร.ต.ต.</option>
                                    <option value="ร.ต.ท.">ร.ต.ท.</option>
                                    <option value="ร.ต.อ.">ร.ต.อ.</option>
                                    <option value="พ.ต.ต.">พ.ต.ต.</option>
                                    <option value="พ.ต.ท.">พ.ต.ท.</option>
                                    <option value="พ.ต.อ.">พ.ต.อ.</option>
                                    <option value="พล.ต.ต.">พล.ต.ต.</option>
                                    <option value="พล.ต.ท.">พล.ท.</option>
                                    <option value="พล.ต.อ.">พล.ต.อ.</option>
                                    <option value="ร.ต.">ร.ต.</option>
                                    <option value="ร.ท.">ร.ท.</option>
                                    <option value="ร.อ.">ร.อ.</option>
                                    <option value="พ.ต.">พ.ต.</option>
                                    <option value="พ.ท.">พ.ท.</option>
                                    <option value="พ.อ.">พ.อ.</option>
                                    <option value="พล.ต.">พล.ต.</option>
                                    <option value="พล.ท.">พล.ท.</option>
                                    <option value="พล.อ.">พล.อ.</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ชื่อ-นามสกุล (ไม่รวมคำนำหน้า)</label>
                            <input type="text" name="full_name" id="modal_full_name" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all" placeholder="ชื่อ นามสกุล">
                            <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><i class="fas fa-info-circle"></i> กรุณากรอกเฉพาะชื่อและนามสกุล ไม่ต้องใส่คำนำหน้า</p>
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">เลขบัตรประชาชน</label>
                            <input type="text" name="citizen_id" id="modal_citizen_id" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">วันเกิด</label>
                            <input type="date" name="birth_date" id="modal_birth_date" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">อายุ</label>
                            <input type="number" name="age" id="modal_age" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">เพศ</label>
                            <select name="gender" id="modal_gender" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-400 bg-white hover:border-slate-300 transition-all">
                                <option value="Male">👨 ชาย</option>
                                <option value="Female">👩 หญิง</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <h4 class="text-slate-800 font-bold mb-4 pb-3 border-b-2 border-slate-200 mt-8 flex items-center gap-2">
                        <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fas fa-address-book"></i>
                        </span>
                        ข้อมูลติดต่อ
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" id="modal_phone" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">อีเมล</label>
                            <input type="email" name="email" id="modal_email" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ที่อยู่</label>
                            <textarea name="address" id="modal_address" rows="2" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 bg-white hover:border-slate-300 transition-all resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <h4 class="text-slate-800 font-bold mb-4 pb-3 border-b-2 border-slate-200 mt-8 flex items-center gap-2">
                        <span class="w-8 h-8 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        ผู้ติดต่อฉุกเฉิน
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ชื่อผู้ติดต่อ</label>
                            <input type="text" name="emergency_contact_name" id="modal_emergency_contact_name" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">เบอร์โทรผู้ติดต่อ</label>
                            <input type="text" name="emergency_contact_phone" id="modal_emergency_contact_phone" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                    </div>

                    <!-- Race Info -->
                    <h4 class="text-slate-800 font-bold mb-4 pb-3 border-b-2 border-slate-200 mt-8 flex items-center gap-2">
                        <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fas fa-running"></i>
                        </span>
                        ข้อมูลการสมัคร
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ประเภทการวิ่ง</label>
                            <select name="category" id="modal_category" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-400 bg-white hover:border-slate-300 transition-all">
                                <optgroup label="Walk & Run 3.5km">
                                    <option value="Walk & Run 3.5km - ประถมศึกษา">ประถมศึกษา (30 บาท)</option>
                                    <option value="Walk & Run 3.5km - ม.ต้น">ม.ต้น (30 บาท)</option>
                                    <option value="Walk & Run 3.5km - ม.ปลาย/ปวช.">ม.ปลาย/ปวช. (30 บาท)</option>
                                    <option value="Walk & Run 3.5km - บุคคลทั่วไป">บุคคลทั่วไป (450 บาท)</option>
                                    <option value="Walk & Run 3.5km - VIP">VIP (1,200 บาท)</option>
                                </optgroup>
                                <optgroup label="Fun Run 5.5km นักเรียน">
                                    <option value="Fun Run 5.5km นักเรียน - ประถมศึกษา">ประถมศึกษา (300 บาท)</option>
                                    <option value="Fun Run 5.5km นักเรียน - ม.ต้น">ม.ต้น (300 บาท)</option>
                                    <option value="Fun Run 5.5km นักเรียน - ม.ปลาย/ปวช.">ม.ปลาย/ปวช. (300 บาท)</option>
                                </optgroup>
                                <optgroup label="Fun Run 5.5km บุคคลทั่วไป">
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - 19-29 ปี">19-29 ปี (450 บาท)</option>
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - 30-39 ปี">30-39 ปี (450 บาท)</option>
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - 40-49 ปี">40-49 ปี (450 บาท)</option>
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - 50-59 ปี">50-59 ปี (450 บาท)</option>
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - 60 ปีขึ้นไป">60 ปีขึ้นไป (450 บาท)</option>
                                    <option value="Fun Run 5.5km บุคคลทั่วไป - VIP">VIP (1,200 บาท)</option>
                                </optgroup>
                                <optgroup label="Merchandise">
                                    <option value="Shirt Only">สั่งซื้อเสื้อที่ระลึก (ไม่วิ่ง) (250 บาท)</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ไซส์เสื้อ</label>
                            <select name="shirt_size" id="modal_shirt_size" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-400 bg-white hover:border-slate-300 transition-all">
                                <optgroup label="Adult Sizes">
                                    <option value="XS">XS (34")</option>
                                    <option value="S">S (36")</option>
                                    <option value="M">M (38")</option>
                                    <option value="L">L (40")</option>
                                    <option value="XL">XL (42")</option>
                                    <option value="2XL">2XL (44")</option>
                                    <option value="3XL">3XL (46")</option>
                                    <option value="4XL">4XL (48")</option>
                                    <option value="5XL">5XL (50")</option>
                                </optgroup>
                                <optgroup label="Kids Sizes">
                                    <option value="Kids S">Kids S (24")</option>
                                    <option value="Kids M">Kids M (26")</option>
                                    <option value="Kids L">Kids L (28")</option>
                                    <option value="Kids XL">Kids XL (30")</option>
                                    <option value="Kids 2XL">Kids 2XL (32")</option>
                                </optgroup>
                                <option value="No Shirt">ไม่รับเสื้อ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">การจัดส่ง</label>
                            <select name="shipping_method" id="modal_shipping_method" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-400 bg-white hover:border-slate-300 transition-all">
                                <option value="SELF">🏃 รับด้วยตนเอง</option>
                                <option value="POST">📦 จัดส่งไปรษณีย์ (+50 บาท)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <h4 class="text-slate-800 font-bold mb-4 pb-3 border-b-2 border-slate-200 mt-8 flex items-center gap-2">
                        <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        ข้อมูลการชำระเงิน
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">ยอดเงิน (บาท)</label>
                            <input type="number" step="0.01" name="payment_amount" id="modal_payment_amount" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">วันที่โอน</label>
                            <input type="date" name="payment_date" id="modal_payment_date" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-2 uppercase tracking-wider">เวลาโอน</label>
                            <input type="time" name="payment_time" id="modal_payment_time" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-400 bg-white hover:border-slate-300 transition-all">
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8 sticky bottom-0 bg-white pt-4 border-t-2 border-slate-100">
                        <button type="button" onclick="closeModal()" class="flex-1 px-6 py-3.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all flex items-center justify-center gap-2 border-2 border-slate-200">
                            <i class="fas fa-times"></i> ยกเลิก
                        </button>
                        <button type="submit" class="flex-1 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Slip Modal -->
    <div id="slipModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-[60] transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto flex flex-col md:flex-row">
            <!-- Image Section -->
            <div class="w-full md:w-1/2 bg-black flex items-center justify-center p-4 relative group">
                <button onclick="closeSlipModal()" class="absolute top-4 left-4 bg-black/50 text-white rounded-full w-8 h-8 flex items-center justify-center md:hidden z-10">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <img id="slipImage" src="" alt="Payment Slip" class="max-h-[50vh] md:max-h-[80vh] max-w-full object-contain transition-transform duration-300">
                <a id="slipDownloadLink" href="" target="_blank" class="absolute bottom-4 right-4 bg-white/90 text-gray-800 px-3 py-1 rounded-lg text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-external-link-alt mr-1"></i> เปิดรูปเต็ม
                </a>
            </div>
            <!-- Details Section -->
            <div class="w-full md:w-1/2 p-6 flex flex-col bg-white">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-receipt text-purple-500"></i> ตรวจสอบสลิป
                    </h3>
                    <button onclick="closeSlipModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="space-y-6 flex-grow overflow-y-auto pr-2">
                    <!-- Registration Info -->
                    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-800 mb-3 uppercase tracking-wider">ข้อมูลการสมัคร</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 whitespace-nowrap mr-2">ประเภท</span>
                                <span class="font-bold text-slate-800 text-right" id="slipCategory"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">ไซส์เสื้อ</span>
                                <span class="font-bold text-slate-800" id="slipShirtSize"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">ประเภทคอ</span>
                                <span class="font-bold" id="slipCollarType"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">การจัดส่ง</span>
                                <span class="font-bold text-slate-800" id="slipShipping"></span>
                            </div>

                            <!-- Breakdown -->
                            <div class="bg-white/60 rounded-lg p-2 mt-2 space-y-1 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span>ค่าสมัคร</span>
                                    <span class="font-medium" id="slipBasePrice"></span>
                                </div>
                                <div class="flex justify-between hidden" id="slipShippingCostRow">
                                    <span>ค่าจัดส่ง</span>
                                    <span class="font-medium">50 ฿</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-blue-200 flex justify-between items-end mt-2">
                                <span class="text-slate-500">ยอดที่ต้องชำระ</span>
                                <span class="font-bold text-blue-600 text-lg" id="slipExpectedAmount"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Info -->
                    <div class="bg-purple-50 p-5 rounded-xl border border-purple-100">
                        <h4 class="text-sm font-bold text-purple-800 mb-3 uppercase tracking-wider">ข้อมูลการโอนเงิน</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-end border-b border-purple-100 pb-2">
                                <span class="text-sm text-slate-500">ชื่อผู้โอน</span>
                                <span class="font-bold text-slate-800 text-lg" id="slipName"></span>
                            </div>
                            <div class="flex justify-between items-end border-b border-purple-100 pb-2">
                                <span class="text-sm text-slate-500">ยอดเงิน</span>
                                <span class="font-bold text-green-600 text-2xl" id="slipAmount"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-1">
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">วันที่โอน</p>
                                    <p class="font-bold text-slate-800 bg-white px-3 py-1 rounded border border-purple-100 inline-block" id="slipDate"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">เวลา</p>
                                    <p class="font-bold text-slate-800 bg-white px-3 py-1 rounded border border-purple-100 inline-block" id="slipTime"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Form -->
                    <form method="POST" class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" id="slipOrderId">
                        
                        <h4 class="text-sm font-bold text-slate-600 mb-3 uppercase tracking-wider">จัดการสถานะ</h4>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">สถานะการสมัคร</label>
                            <div class="relative">
                                <select name="status" id="slipStatus" onchange="toggleSlipReason()" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 appearance-none bg-white">
                                    <option value="pending">รอตรวจสอบ</option>
                                    <option value="approved">อนุมัติ (ตรวจสอบผ่าน)</option>
                                    <option value="rejected">ปฏิเสธ / มีปัญหา</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tasks text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4 hidden" id="slipReasonDiv">
                            <label class="block text-sm font-medium text-slate-700 mb-1">เหตุผล (กรณีปฏิเสธ)</label>
                            <input type="text" name="reject_reason" id="slipReason" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="ระบุเหตุผล...">
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-purple-500/30 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> บันทึกการตรวจสอบ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if ($message): ?>
            Toast.fire({
                icon: '<?php echo strpos($message, 'เรียบร้อย') !== false ? 'success' : 'error'; ?>',
                title: '<?php echo $message; ?>'
            });
        <?php endif; ?>

        function confirmAction(event, message, confirmButtonText) {
            event.preventDefault();
            const form = event.target;
            
            Swal.fire({
                title: 'ยืนยันการทำรายการ',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function viewSlip(data) {
            const slipPath = 'view_slip.php?file=' + data.payment_slip;
            document.getElementById('slipImage').src = slipPath;
            document.getElementById('slipDownloadLink').href = slipPath;
            
            // Registration Info
            document.getElementById('slipCategory').textContent = data.category;
            document.getElementById('slipShirtSize').textContent = data.shirt_size;
            
            // Collar Type
            const collarType = data.collar_type || 'round';
            const collarTypeEl = document.getElementById('slipCollarType');
            if (collarType === 'polo') {
                collarTypeEl.textContent = 'คอปก (+100 บาท)';
                collarTypeEl.className = 'font-bold text-orange-600';
            } else {
                collarTypeEl.textContent = 'คอกลม';
                collarTypeEl.className = 'font-bold text-slate-800';
            }
            
            const isShipping = data.shipping_method === 'POST';
            document.getElementById('slipShipping').textContent = isShipping ? 'จัดส่งไปรษณีย์ (+50)' : 'รับด้วยตนเอง';

            // Calculate Expected Price
            let price = 0;
            const cat = data.category || '';
            
            if (cat.includes('VIP')) price = 1200;
            else if (cat.includes('Walk & Run') && cat.includes('บุคคลทั่วไป')) price = 450;
            else if (cat.includes('Walk & Run')) price = 30;
            else if (cat.includes('Fun Run') && cat.includes('นักเรียน')) price = 300;
            else if (cat.includes('Fun Run') && cat.includes('บุคคลทั่วไป')) price = 450;
            else if (cat.includes('Shirt Only')) price = 250;

            // Update Base Price
            document.getElementById('slipBasePrice').textContent = price.toLocaleString() + ' ฿';

            // Add collar type surcharge
            if (collarType === 'polo') {
                price += 100;
            }

            if (isShipping) {
                price += 50;
                document.getElementById('slipShippingCostRow').classList.remove('hidden');
            } else {
                document.getElementById('slipShippingCostRow').classList.add('hidden');
            }
            
            document.getElementById('slipExpectedAmount').textContent = price.toLocaleString() + ' ฿';

            // Transfer Info
            document.getElementById('slipName').textContent = data.full_name;
            document.getElementById('slipAmount').textContent = Number(data.payment_amount).toLocaleString() + ' ฿';
            document.getElementById('slipDate').textContent = data.payment_date || '-';
            document.getElementById('slipTime').textContent = data.payment_time || '-';
            
            document.getElementById('slipOrderId').value = data.id;
            document.getElementById('slipStatus').value = data.status || 'pending';
            document.getElementById('slipReason').value = data.reject_reason || '';
            
            toggleSlipReason();
            
            document.getElementById('slipModal').classList.remove('hidden');
            document.getElementById('slipModal').classList.add('flex');
        }

        function closeSlipModal() {
            document.getElementById('slipModal').classList.add('hidden');
            document.getElementById('slipModal').classList.remove('flex');
        }

        function toggleSlipReason() {
            const status = document.getElementById('slipStatus').value;
            const reasonDiv = document.getElementById('slipReasonDiv');
            if (status === 'rejected') {
                reasonDiv.classList.remove('hidden');
            } else {
                reasonDiv.classList.add('hidden');
            }
        }

        // List of all possible prefixes for stripping from full_name
        const allPrefixes = [
            'นาย', 'นาง', 'นางสาว', 'ด.ช.', 'ด.ญ.',
            'ดร.', 'ผศ.', 'รศ.', 'ศ.', 'ผศ.ดร.', 'รศ.ดร.', 'ศ.ดร.',
            'นพ.', 'พญ.', 'ทพ.', 'ทพญ.', 'สพ.ญ.', 'น.สพ.',
            'ว่าที่ร้อยตรี', 'ร.ต.ต.', 'ร.ต.ท.', 'ร.ต.อ.', 'พ.ต.ต.', 'พ.ต.ท.', 'พ.ต.อ.',
            'พล.ต.ต.', 'พล.ต.ท.', 'พล.ต.อ.', 'ร.ต.', 'ร.ท.', 'ร.อ.',
            'พ.ต.', 'พ.ท.', 'พ.อ.', 'พล.ต.', 'พล.ท.', 'พล.อ.'
        ];

        // Function to strip prefix from full_name
        function stripPrefix(fullName) {
            if (!fullName) return '';
            // Sort by length descending to match longer prefixes first
            const sortedPrefixes = [...allPrefixes].sort((a, b) => b.length - a.length);
            for (const prefix of sortedPrefixes) {
                if (fullName.startsWith(prefix + ' ')) {
                    return fullName.substring(prefix.length + 1).trim();
                }
                if (fullName.startsWith(prefix)) {
                    return fullName.substring(prefix.length).trim();
                }
            }
            return fullName;
        }

        function openModal(data) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('modal_id').value = data.id;
            
            // Status
            document.getElementById('modal_status').value = data.status || 'pending';
            document.getElementById('modal_reason').value = data.reject_reason || '';
            
            // Personal - Strip prefix from full_name
            const prefix = data.prefix || 'นาย';
            document.getElementById('modal_prefix').value = prefix;
            
            // Remove prefix from full_name for display
            const nameWithoutPrefix = stripPrefix(data.full_name);
            document.getElementById('modal_full_name').value = nameWithoutPrefix;
            
            document.getElementById('modal_citizen_id').value = data.citizen_id;
            document.getElementById('modal_birth_date').value = data.birth_date;
            document.getElementById('modal_age').value = data.age;
            document.getElementById('modal_gender').value = data.gender;
            
            // Contact
            document.getElementById('modal_phone').value = data.phone;
            document.getElementById('modal_email').value = data.email;
            document.getElementById('modal_address').value = data.address;
            
            // Emergency
            document.getElementById('modal_emergency_contact_name').value = data.emergency_contact_name || '';
            document.getElementById('modal_emergency_contact_phone').value = data.emergency_contact_phone || '';
            
            // Race
            document.getElementById('modal_category').value = data.category;
            document.getElementById('modal_shirt_size').value = data.shirt_size;
            document.getElementById('modal_shipping_method').value = data.shipping_method;
            
            // Payment
            document.getElementById('modal_payment_amount').value = data.payment_amount || '';
            document.getElementById('modal_payment_date').value = data.payment_date || '';
            document.getElementById('modal_payment_time').value = data.payment_time || '';

            toggleReason();
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function toggleReason() {
            const status = document.getElementById('modal_status').value;
            const reasonDiv = document.getElementById('reason_div');
            if (status === 'rejected') {
                reasonDiv.classList.remove('hidden');
            } else {
                reasonDiv.classList.add('hidden');
            }
        }

        // Handle form submit - combine prefix with full_name
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const prefix = document.getElementById('modal_prefix').value;
            const fullNameInput = document.getElementById('modal_full_name');
            const nameWithoutPrefix = fullNameInput.value.trim();
            
            // Combine prefix + name
            fullNameInput.value = prefix + ' ' + nameWithoutPrefix;
        });

        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const statusFilter = document.getElementById("statusFilter").value;
            const table = document.getElementById("registrationsTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                const nameTd = tr[i].getElementsByTagName("td")[1];
                const phoneTd = tr[i].getElementsByTagName("td")[1]; // Phone is in same cell div
                const statusTd = tr[i].getElementsByTagName("td")[4];
                
                if (nameTd && statusTd) {
                    const nameValue = nameTd.textContent || nameTd.innerText;
                    const statusValue = statusTd.getAttribute('data-status') || statusTd.textContent.trim(); // We need to handle status text mapping
                    
                    // Map displayed status text to filter values
                    let rowStatus = 'pending';
                    if (statusValue.includes('อนุมัติ')) rowStatus = 'approved';
                    else if (statusValue.includes('ปฏิเสธ')) rowStatus = 'rejected';
                    else if (statusValue.includes('รอตรวจสอบ')) rowStatus = 'pending';

                    const matchesSearch = nameValue.toLowerCase().indexOf(filter) > -1;
                    const matchesStatus = statusFilter === 'all' || rowStatus === statusFilter;

                    if (matchesSearch && matchesStatus) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function exportTableToCSV(filename) {
            // Use PHP export for proper Thai language support
            const statusFilter = document.getElementById("statusFilter").value;
            const url = 'export_excel.php?type=registrations&status=' + statusFilter;
            
            // Create a temporary link and click it
            const downloadLink = document.createElement("a");
            downloadLink.href = url;
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            
            Toast.fire({
                icon: 'success',
                title: 'กำลังดาวน์โหลดไฟล์ Excel...'
            });
        }
    </script>
</body>
</html>
