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
    <style>body { font-family: 'Sarabun', 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 font-sans text-slate-600">
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
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-list-alt text-slate-400"></i> รายการผู้สมัคร
                </h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="ค้นหาชื่อ, เบอร์โทร..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-full sm:w-64 transition-all">
                    </div>
                    <div class="flex gap-2">
                        <select id="statusFilter" onchange="filterTable()" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white text-slate-600 cursor-pointer">
                            <option value="all">สถานะทั้งหมด</option>
                            <option value="pending">รอตรวจสอบ</option>
                            <option value="approved">อนุมัติแล้ว</option>
                            <option value="rejected">ปฏิเสธ</option>
                        </select>
                        <button onclick="exportTableToCSV('registrations.csv')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition shadow-lg shadow-red-200 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Export</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="registrationsTable">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">ผู้สมัคร</th>
                            <th class="px-6 py-4 font-semibold">ประเภท/ไซส์</th>
                            <th class="px-6 py-4 font-semibold">การชำระเงิน</th>
                            <th class="px-6 py-4 font-semibold text-center">สถานะ</th>
                            <th class="px-6 py-4 font-semibold text-center">หลักฐาน</th>
                            <th class="px-6 py-4 font-semibold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-inbox text-4xl mb-3 block opacity-50"></i>
                                    ยังไม่มีข้อมูลผู้สมัคร
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $row): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-4 text-slate-400 font-mono text-xs">#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 font-bold text-sm">
                                                <?php echo mb_substr($row['first_name'] ?? $row['full_name'], 0, 1); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800"><?php echo $row['full_name']; ?></div>
                                                <div class="text-xs text-slate-500 flex items-center gap-2">
                                                    <span><i class="fas fa-phone-alt text-[10px]"></i> <?php echo $row['phone']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-700"><?php echo $row['category']; ?></div>
                                        <div class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wide">
                                            <i class="fas fa-tshirt"></i> <?php echo $row['shirt_size']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if (!empty($row['payment_date'])): ?>
                                            <div class="text-sm font-bold text-slate-700">฿<?php echo number_format($row['payment_amount']); ?></div>
                                            <div class="text-xs text-slate-400">
                                                <?php echo date('d/m/y', strtotime($row['payment_date'])); ?> • <?php echo date('H:i', strtotime($row['payment_time'])); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-300 text-xs italic">ยังไม่แจ้งโอน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                            $statusStyles = [
                                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'approved' => 'bg-green-50 text-green-700 border-green-200',
                                                'rejected' => 'bg-red-50 text-red-700 border-red-200'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'รอตรวจสอบ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'rejected' => 'ปฏิเสธ'
                                            ];
                                            $s = $row['status'] ?? 'pending';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border <?php echo $statusStyles[$s]; ?>">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                            <?php echo $statusLabels[$s]; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($row['payment_slip']): ?>
                                            <div class="relative group/slip inline-block">
                                                <img src="view_slip.php?file=<?php echo $row['payment_slip']; ?>" 
                                                     class="h-10 w-10 object-cover rounded-lg border border-slate-200 shadow-sm cursor-zoom-in hover:scale-110 transition-transform"
                                                     onclick="openImageModal('view_slip.php?file=<?php echo $row['payment_slip']; ?>')"
                                                     alt="Slip">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-300">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <form method="POST" onsubmit="return confirmAction(event, 'ยืนยันการอนุมัติ?', 'อนุมัติ');">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-500 hover:text-white flex items-center justify-center transition-all" title="อนุมัติ">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <button onclick='openModal(<?php echo json_encode($row); ?>)' class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all" title="แก้ไข">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            
                                            <form method="POST" onsubmit="return confirmAction(event, 'ยืนยันการลบข้อมูลนี้?', 'ลบข้อมูล');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all" title="ลบ">
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
                                <option value="นาย">นาย</option>
                                <option value="นาง">นาง</option>
                                <option value="นางสาว">นางสาว</option>
                                <option value="ด.ช.">ด.ช.</option>
                                <option value="ด.ญ.">ด.ญ.</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 text-xs font-bold mb-1">ชื่อ-นามสกุล</label>
                            <input type="text" name="full_name" id="modal_full_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
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
                                <optgroup label="Fun Run 5.5km">
                                    <option value="Fun Run 5.5km - ประถมศึกษา">ประถมศึกษา (300 บาท)</option>
                                    <option value="Fun Run 5.5km - ม.ต้น">ม.ต้น (300 บาท)</option>
                                    <option value="Fun Run 5.5km - ม.ปลาย/ปวช.">ม.ปลาย/ปวช. (300 บาท)</option>
                                    <option value="Fun Run 5.5km - บุคคลทั่วไป">บุคคลทั่วไป (450 บาท)</option>
                                    <option value="Fun Run 5.5km - อายุมากกว่า 50">อายุมากกว่า 50 (450 บาท)</option>
                                    <option value="Fun Run 5.5km - VIP">VIP (1,200 บาท)</option>
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
                        <div>
                            <label class="block text-slate-600 text-xs font-bold mb-1">ธนาคาร/อ้างอิง</label>
                            <input type="text" name="bank_ref" id="modal_bank_ref" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-slate-600 text-xs font-bold mb-1">ชื่อผู้โอน</label>
                            <input type="text" name="sender_name" id="modal_sender_name" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
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

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden flex items-center justify-center z-[60] transition-opacity" onclick="closeImageModal()">
        <div class="relative max-w-5xl max-h-screen p-4 w-full flex justify-center">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center transition backdrop-blur-md z-50">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl object-contain" onclick="event.stopPropagation()">
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

        function openImageModal(src) {
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('modalImage').src = src;
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            setTimeout(() => {
                document.getElementById('modalImage').src = '';
            }, 200);
        }

        function openModal(data) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('modal_id').value = data.id;
            
            // Status
            document.getElementById('modal_status').value = data.status || 'pending';
            document.getElementById('modal_reason').value = data.reject_reason || '';
            
            // Personal
            document.getElementById('modal_prefix').value = data.prefix || 'นาย';
            document.getElementById('modal_full_name').value = data.full_name;
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
            document.getElementById('modal_bank_ref').value = data.bank_ref || '';
            document.getElementById('modal_sender_name').value = data.sender_name || '';

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
