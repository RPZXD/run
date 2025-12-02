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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Use Sarabun for better Thai readability -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', 'Kanit', sans-serif; }
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
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-600 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwgMCwgMCwgMC4wNSkiLz48L3N2Zz4=')]">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 hidden">
            <!-- Total -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ผู้สมัครทั้งหมด</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1"><?php echo number_format($stats['total']); ?></h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pending -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">รอตรวจสอบ</p>
                        <h3 class="text-3xl font-bold text-yellow-600 mt-1"><?php echo number_format($stats['pending']); ?></h3>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">อนุมัติแล้ว</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-1"><?php echo number_format($stats['approved']); ?></h3>
                    </div>
                    <div class="p-3 bg-green-50 rounded-xl text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Income -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ยอดเงินรวม</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-1">฿<?php echo number_format($stats['income']); ?></h3>
                    </div>
                    <div class="p-3 bg-red-50 rounded-xl text-red-600">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-fade-in-up">
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-list-alt"></i>
                    </div>
                    รายการผู้สมัคร
                </h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="ค้นหาชื่อ, เบอร์โทร..." class="pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-full sm:w-64 transition-all bg-slate-50 focus:bg-white">
                    </div>
                    <div class="flex gap-2">
                        <select id="statusFilter" onchange="filterTable()" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50 focus:bg-white text-slate-600 cursor-pointer transition-all">
                            <option value="all">สถานะทั้งหมด</option>
                            <option value="pending">รอตรวจสอบ</option>
                            <option value="approved">อนุมัติแล้ว</option>
                            <option value="rejected">ปฏิเสธ</option>
                        </select>
                        <button onclick="exportTableToCSV('registrations.csv')" class="px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-green-500/30 transition-all transform hover:-translate-y-0.5 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-file-excel"></i> <span class="hidden sm:inline">Export</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="registrationsTable">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100 backdrop-blur-sm">
                            <th class="px-6 py-4 font-bold rounded-tl-2xl">ID</th>
                            <th class="px-6 py-4 font-bold">ผู้สมัคร</th>
                            <th class="px-6 py-4 font-bold">ข้อมูลการวิ่ง</th>
                            <th class="px-6 py-4 font-bold">การชำระเงิน</th>
                            <th class="px-6 py-4 font-bold text-center">สถานะ</th>
                            <th class="px-6 py-4 font-bold text-right rounded-tr-2xl">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-3xl text-slate-300"></i>
                                        </div>
                                        <p>ยังไม่มีข้อมูลผู้สมัคร</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $row): ?>
                                <tr class="hover:bg-blue-50/40 transition-all duration-200 group hover:shadow-sm">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-md">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white">
                                                    <?php echo mb_substr($row['first_name'] ?? $row['full_name'], 0, 1); ?>
                                                </div>
                                                <?php if(($row['gender'] ?? '') == 'Male'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-blue-100 text-blue-600 rounded-full w-4 h-4 flex items-center justify-center text-[10px] border border-white"><i class="fas fa-mars"></i></div>
                                                <?php elseif(($row['gender'] ?? '') == 'Female'): ?>
                                                    <div class="absolute -bottom-1 -right-1 bg-pink-100 text-pink-600 rounded-full w-4 h-4 flex items-center justify-center text-[10px] border border-white"><i class="fas fa-venus"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm"><?php echo $row['full_name']; ?></div>
                                                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                                    <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px] text-slate-500 font-medium group-hover:bg-white transition-colors">
                                                        <i class="fas fa-phone-alt text-[9px] mr-1 text-slate-400"></i> <?php echo $row['phone']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-start gap-1.5">
                                            <?php 
                                                $catColor = 'bg-slate-100 text-slate-600';
                                                if(strpos($row['category'], 'VIP') !== false) $catColor = 'bg-amber-100 text-amber-700 border border-amber-200';
                                                elseif(strpos($row['category'], 'Fun Run') !== false) $catColor = 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                                                elseif(strpos($row['category'], 'Walk') !== false) $catColor = 'bg-blue-50 text-blue-700 border border-blue-100';
                                            ?>
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo $catColor; ?>">
                                                <?php echo $row['category']; ?>
                                            </span>
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100">
                                                    <i class="fas fa-tshirt text-slate-400"></i> 
                                                    <span class="font-bold text-slate-700"><?php echo $row['shirt_size']; ?></span>
                                                </span>
                                                <?php if(($row['shipping_method'] ?? '') == 'POST'): ?>
                                                    <span class="text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100" title="จัดส่งไปรษณีย์"><i class="fas fa-truck mr-1"></i> ส่ง ปณ.</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if (!empty($row['payment_date'])): ?>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-700">฿<?php echo number_format($row['payment_amount']); ?></span>
                                                <span class="text-[10px] text-slate-400 mt-0.5">
                                                    <i class="far fa-calendar-alt mr-0.5"></i> <?php echo date('d/m/y', strtotime($row['payment_date'])); ?>
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    <i class="far fa-clock mr-0.5"></i> <?php echo date('H:i', strtotime($row['payment_time'])); ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-400 text-xs font-medium">
                                                <i class="fas fa-minus-circle mr-1"></i> ยังไม่แจ้ง
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                            $statusStyles = [
                                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 ring-yellow-500/20',
                                                'approved' => 'bg-green-50 text-green-700 border-green-200 ring-green-500/20',
                                                'rejected' => 'bg-red-50 text-red-700 border-red-200 ring-red-500/20'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'รอตรวจสอบ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'rejected' => 'ปฏิเสธ'
                                            ];
                                            $s = $row['status'] ?? 'pending';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border ring-2 ring-offset-1 <?php echo $statusStyles[$s]; ?>" data-status="<?php echo $statusLabels[$s]; ?>">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                            <?php echo $statusLabels[$s]; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if ($row['payment_slip']): ?>
                                                <button onclick='viewSlip(<?php echo json_encode($row); ?>)' class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 hover:bg-purple-500 hover:text-white flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="ดูสลิป">
                                                    <i class="fas fa-receipt"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button onclick='openModal(<?php echo json_encode($row); ?>)' class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="แก้ไข">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            
                                            <form method="POST" onsubmit="return confirmAction(event, 'ยืนยันการลบข้อมูลนี้?', 'ลบข้อมูล');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="ลบ">
                                                    <i class="fas fa-trash-alt"></i>
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
            
            <!-- Pagination (Placeholder) -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                <span class="text-xs text-slate-500">แสดง <?php echo count($registrations); ?> รายการ</span>
                <div class="flex gap-1">
                    <button class="px-3 py-1 rounded border border-slate-200 bg-white text-slate-500 text-xs hover:bg-slate-50 disabled:opacity-50" disabled>Previous</button>
                    <button class="px-3 py-1 rounded border border-slate-200 bg-white text-slate-500 text-xs hover:bg-slate-50 disabled:opacity-50" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-2xl bg-white overflow-hidden transform transition-all scale-100 mb-10">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center sticky top-0 z-10">
                <h3 class="text-lg font-bold text-slate-800">แก้ไขข้อมูลผู้สมัคร</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="update_info">
                    <input type="hidden" name="id" id="modal_id">
                    
                    <!-- Status Section -->
                    <div class="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <h4 class="text-blue-800 font-bold mb-3 flex items-center gap-2"><i class="fas fa-info-circle"></i> สถานะการสมัคร</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2">สถานะ</label>
                                <select name="status" id="modal_status" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white" onchange="toggleReason()">
                                    <option value="pending">รอตรวจสอบ</option>
                                    <option value="approved">อนุมัติ (ตรวจสอบแล้ว)</option>
                                    <option value="rejected">ปฏิเสธ / มีปัญหา</option>
                                </select>
                            </div>
                            <div id="reason_div" class="hidden">
                                <label class="block text-slate-700 text-sm font-bold mb-2">เหตุผล (กรณีปฏิเสธ)</label>
                                <input type="text" name="reject_reason" id="modal_reason" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <h4 class="text-slate-800 font-bold mb-3 border-b pb-2">ข้อมูลส่วนตัว</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">คำนำหน้า</label>
                            <select name="prefix" id="modal_prefix" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
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
                            <label class="block text-slate-600 text-xs font-bold mb-1">ชื่อ-นามสกุล (ไม่รวมคำนำหน้า)</label>
                            <input type="text" name="full_name" id="modal_full_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm" placeholder="ชื่อ นามสกุล">
                            <p class="text-xs text-slate-400 mt-1">* กรุณากรอกเฉพาะชื่อและนามสกุล ไม่ต้องใส่คำนำหน้า</p>
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">เลขบัตรประชาชน</label>
                            <input type="text" name="citizen_id" id="modal_citizen_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">วันเกิด</label>
                            <input type="date" name="birth_date" id="modal_birth_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">อายุ</label>
                            <input type="number" name="age" id="modal_age" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">เพศ</label>
                            <select name="gender" id="modal_gender" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                <option value="Male">ชาย</option>
                                <option value="Female">หญิง</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <h4 class="text-slate-800 font-bold mb-3 border-b pb-2 mt-6">ข้อมูลติดต่อ</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" id="modal_phone" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">อีเมล</label>
                            <input type="email" name="email" id="modal_email" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 text-xs font-bold mb-1">ที่อยู่</label>
                            <textarea name="address" id="modal_address" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <h4 class="text-slate-800 font-bold mb-3 border-b pb-2 mt-6">ผู้ติดต่อฉุกเฉิน</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">ชื่อผู้ติดต่อ</label>
                            <input type="text" name="emergency_contact_name" id="modal_emergency_contact_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">เบอร์โทรผู้ติดต่อ</label>
                            <input type="text" name="emergency_contact_phone" id="modal_emergency_contact_phone" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                    </div>

                    <!-- Race Info -->
                    <h4 class="text-slate-800 font-bold mb-3 border-b pb-2 mt-6">ข้อมูลการสมัคร</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">ประเภทการวิ่ง</label>
                            <select name="category" id="modal_category" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                <optgroup label="Walk & Run 3.5km">
                                    <option value="Walk & Run 3.5km - ประถมศึกษา">ประถมศึกษา (30 บาท)</option>
                                    <option value="Walk & Run 3.5km - ม.ต้น">ม.ต้น (30 บาท)</option>
                                    <option value="Walk & Run 3.5km - ม.ปลาย/ปวช.">ม.ปลาย/ปวช. (30 บาท)</option>
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
                            <label class="block text-slate-600 text-xs font-bold mb-1">ไซส์เสื้อ</label>
                            <select name="shirt_size" id="modal_shirt_size" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
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
                            <label class="block text-slate-600 text-xs font-bold mb-1">การจัดส่ง</label>
                            <select name="shipping_method" id="modal_shipping_method" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                <option value="SELF">รับด้วยตนเอง</option>
                                <option value="POST">จัดส่งไปรษณีย์ (+50 บาท)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <h4 class="text-slate-800 font-bold mb-3 border-b pb-2 mt-6">ข้อมูลการชำระเงิน</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">ยอดเงิน</label>
                            <input type="number" step="0.01" name="payment_amount" id="modal_payment_amount" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">วันที่โอน</label>
                            <input type="date" name="payment_date" id="modal_payment_date" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">เวลาโอน</label>
                            <input type="time" name="payment_time" id="modal_payment_time" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>

                    </div>

                    <div class="flex gap-3 mt-8 sticky bottom-0 bg-white pt-4 border-t">
                        <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                            ยกเลิก
                        </button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 transition transform hover:-translate-y-0.5">
                            บันทึกข้อมูล
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
            
            const isShipping = data.shipping_method === 'POST';
            document.getElementById('slipShipping').textContent = isShipping ? 'จัดส่งไปรษณีย์ (+50)' : 'รับด้วยตนเอง';

            // Calculate Expected Price
            let price = 0;
            const cat = data.category || '';
            
            if (cat.includes('VIP')) price = 1200;
            else if (cat.includes('Walk & Run')) price = 30;
            else if (cat.includes('Fun Run') && cat.includes('นักเรียน')) price = 300;
            else if (cat.includes('Fun Run') && cat.includes('บุคคลทั่วไป')) price = 450;
            else if (cat.includes('Shirt Only')) price = 250;

            // Update Base Price
            document.getElementById('slipBasePrice').textContent = price.toLocaleString() + ' ฿';

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
            const csv = [];
            const rows = document.querySelectorAll("table tr");
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (let j = 0; j < cols.length - 1; j++) { // Skip last column (Actions)
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/(\s\s)/gm, " ");
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                
                csv.push(row.join(","));
            }

            const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
            const downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            
            Toast.fire({
                icon: 'success',
                title: 'ส่งออกข้อมูลเรียบร้อยแล้ว'
            });
        }
    </script>
</body>
</html>
