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

// Get Registration shipping list (Approved + POST)
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

// Get Shirt Orders shipping list (POST + pending/paid/shipped status)
$stmt = $db->query("SELECT * FROM shirt_orders WHERE shipping_method = 'POST' AND status IN ('pending', 'paid', 'shipped') ORDER BY created_at DESC");
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        
        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-inside: avoid; break-inside: avoid; }
            body { background: white; padding: 0; margin: 0; }
            .print-container {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                padding: 10px;
            }
            .label-card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            /* Hide items not selected for printing */
            .print-hidden { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwgMCwgMCwgMC4wNSkiLz48L3N2Zz4=')]">
    <div class="no-print">
        <?php include 'navbar.php'; ?>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl animate-fade-in-up">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8 no-print">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    จัดการการจัดส่ง (Shipping)
                </h1>
                <p class="text-slate-500 text-sm mt-1 ml-12">รวมรายการจัดส่งทั้งหมด - การสมัครวิ่ง & สั่งซื้อเสื้อ</p>
            </div>
            <div class="flex gap-3 items-center">
                <div class="hidden md:flex gap-2 text-sm">
                    <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <i class="fas fa-running"></i> <?php echo count($reg_pending) + count($reg_printed); ?>
                    </span>
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <i class="fas fa-tshirt"></i> <?php echo count($shirt_pending) + count($shirt_printed); ?>
                    </span>
                </div>
                <button onclick="printSelected()" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all font-bold flex items-center gap-2">
                    <i class="fas fa-print"></i> พิมพ์รายการที่เลือก
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-200 no-print">
            <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-3 font-bold text-blue-600 border-b-2 border-blue-600 transition-all hover:bg-blue-50/50 rounded-t-lg flex items-center gap-2">
                <span class="bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full"><?php echo $total_pending; ?></span>
                รอพิมพ์
            </button>
            <button onclick="switchTab('printed')" id="tab-printed" class="px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-t-lg flex items-center gap-2">
                <span class="bg-slate-200 text-slate-600 text-xs px-2 py-0.5 rounded-full"><?php echo $total_printed; ?></span>
                พิมพ์แล้ว
            </button>
        </div>

        <!-- Content Area - Pending -->
        <div id="content-pending" class="tab-content">
            <?php if ($total_pending === 0): ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-4xl text-green-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">ยอดเยี่ยม!</h3>
                    <p class="text-slate-500 mt-2">ไม่มีรายการค้างพิมพ์ในขณะนี้</p>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2 mb-4 no-print bg-blue-50 p-4 rounded-xl border border-blue-100 shadow-sm">
                    <input type="checkbox" id="selectAllPending" onchange="toggleAll('pending')" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <label for="selectAllPending" class="font-bold text-slate-700 cursor-pointer select-none">เลือกทั้งหมด</label>
                    <div class="ml-auto flex gap-2 text-sm">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-running"></i> สมัครวิ่ง: <?php echo count($reg_pending); ?>
                        </span>
                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-tshirt"></i> สั่งเสื้อ: <?php echo count($shirt_pending); ?>
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($reg_pending)): ?>
                <!-- Registration Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-running text-blue-600"></i>
                        </div>
                        จากการสมัครวิ่ง
                        <span class="text-sm font-normal text-slate-500">(<?php echo count($reg_pending); ?> รายการ)</span>
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
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tshirt text-yellow-600"></i>
                        </div>
                        จากการสั่งซื้อเสื้อ
                        <span class="text-sm font-normal text-slate-500">(<?php echo count($shirt_pending); ?> รายการ)</span>
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
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-print text-4xl text-slate-300"></i>
                    </div>
                    <p class="text-slate-500">ยังไม่มีรายการที่พิมพ์แล้ว</p>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2 mb-4 no-print bg-slate-100 p-4 rounded-xl border border-slate-200 shadow-sm">
                    <input type="checkbox" id="selectAllPrinted" onchange="toggleAll('printed')" class="w-5 h-5 rounded border-slate-300 text-slate-600 focus:ring-slate-500 cursor-pointer">
                    <label for="selectAllPrinted" class="font-bold text-slate-700 cursor-pointer select-none">เลือกทั้งหมด</label>
                    <div class="ml-auto flex gap-2 items-center">
                        <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-running"></i> <?php echo count($reg_printed); ?>
                        </span>
                        <span class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-tshirt"></i> <?php echo count($shirt_printed); ?>
                        </span>
                        <button onclick="markAsUnprinted()" class="text-sm text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors font-bold flex items-center gap-1">
                            <i class="fas fa-undo"></i> ย้ายกลับ
                        </button>
                    </div>
                </div>
                
                <?php if (!empty($reg_printed)): ?>
                <!-- Registration Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-running text-blue-600"></i>
                        </div>
                        จากการสมัครวิ่ง
                        <span class="text-sm font-normal text-slate-500">(<?php echo count($reg_printed); ?> รายการ)</span>
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
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tshirt text-yellow-600"></i>
                        </div>
                        จากการสั่งซื้อเสื้อ
                        <span class="text-sm font-normal text-slate-500">(<?php echo count($shirt_printed); ?> รายการ)</span>
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
            const inactiveClass = 'px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-t-lg flex items-center gap-2';
            const activeClass = 'px-6 py-3 font-bold text-blue-600 border-b-2 border-blue-600 transition-all hover:bg-blue-50/50 rounded-t-lg flex items-center gap-2';
            
            btnPending.className = inactiveClass;
            btnPrinted.className = inactiveClass;
            
            document.getElementById('tab-' + tab).className = activeClass;
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
                Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการที่จะพิมพ์', 'warning');
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
                title: 'พิมพ์สำเร็จหรือไม่?',
                text: "ต้องการเปลี่ยนสถานะรายการที่เลือกเป็น 'พิมพ์แล้ว' หรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ใช่, พิมพ์แล้ว',
                cancelButtonText: 'ไม่, ยังไม่ได้พิมพ์'
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
                            title: 'เรียบร้อย',
                            text: 'อัพเดทสถานะเรียบร้อยแล้ว',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
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
                Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการ', 'warning');
                return;
            }

            Swal.fire({
                title: 'ยืนยัน?',
                text: "ย้ายรายการที่เลือกกลับไป 'รอพิมพ์'?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
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
                            title: 'เรียบร้อย',
                            text: 'ย้ายกลับไปรอพิมพ์แล้ว',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
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
                        title: 'เรียบร้อย',
                        text: 'อัพเดทสถานะเรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error');
                }
            });
        }
    </script>
</body>
</html>