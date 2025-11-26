<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/Registration.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->connect();
$registration = new Registration($db);

// Handle AJAX Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_print_status') {
    $ids = json_decode($_POST['ids'], true);
    $status = (int)$_POST['status'];
    $success = true;
    
    foreach ($ids as $id) {
        if (!$registration->updateShippingStatus($id, $status)) {
            $success = false;
        }
    }
    
    echo json_encode(['success' => $success]);
    exit;
}

$stmt = $registration->readAll();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter for shipping (Approved + POST)
$shipping_list = array_filter($registrations, function($reg) {
    return ($reg['status'] === 'approved' && $reg['shipping_method'] === 'POST');
});

$pending_print = [];
$printed = [];

foreach ($shipping_list as $item) {
    if (empty($item['is_printed'])) {
        $pending_print[] = $item;
    } else {
        $printed[] = $item;
    }
}
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
<body class="bg-slate-50 text-slate-600">
    <div class="no-print">
        <?php include 'navbar.php'; ?>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 no-print">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">จัดการการจัดส่ง (Shipping)</h1>
                <p class="text-slate-500 text-sm">รายการที่ต้องจัดส่งทางไปรษณีย์</p>
            </div>
            <div class="flex gap-2">
                <button onclick="printSelected()" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition font-bold flex items-center gap-2">
                    <i class="fas fa-print"></i> พิมพ์รายการที่เลือก
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-slate-200 no-print">
            <button onclick="switchTab('pending')" id="tab-pending" class="px-4 py-2 font-bold text-blue-600 border-b-2 border-blue-600 transition-colors">
                รอพิมพ์ (<?php echo count($pending_print); ?>)
            </button>
            <button onclick="switchTab('printed')" id="tab-printed" class="px-4 py-2 font-bold text-slate-500 hover:text-slate-700 transition-colors">
                พิมพ์แล้ว (<?php echo count($printed); ?>)
            </button>
        </div>

        <!-- Content Area -->
        <div id="content-pending" class="tab-content">
            <?php if (empty($pending_print)): ?>
                <div class="text-center py-12 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                    <p class="text-slate-500">ไม่มีรายการค้างพิมพ์</p>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2 mb-4 no-print bg-blue-50 p-3 rounded-lg border border-blue-100">
                    <input type="checkbox" id="selectAllPending" onchange="toggleAll('pending')" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="selectAllPending" class="font-bold text-slate-700 cursor-pointer">เลือกทั้งหมด</label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-container">
                    <?php foreach ($pending_print as $row): ?>
                        <?php include 'shipping_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="content-printed" class="tab-content hidden">
            <?php if (empty($printed)): ?>
                <div class="text-center py-12 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <i class="fas fa-print text-4xl text-slate-300 mb-3"></i>
                    <p class="text-slate-500">ยังไม่มีรายการที่พิมพ์แล้ว</p>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2 mb-4 no-print bg-slate-100 p-3 rounded-lg border border-slate-200">
                    <input type="checkbox" id="selectAllPrinted" onchange="toggleAll('printed')" class="w-5 h-5 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                    <label for="selectAllPrinted" class="font-bold text-slate-700 cursor-pointer">เลือกทั้งหมด</label>
                    <button onclick="markAsUnprinted()" class="ml-auto text-sm text-red-600 hover:underline">
                        <i class="fas fa-undo"></i> ย้ายกลับไป "รอพิมพ์"
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-container">
                    <?php foreach ($printed as $row): ?>
                        <?php include 'shipping_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('content-' + tab).classList.remove('hidden');
            
            // Update buttons
            document.getElementById('tab-pending').className = 'px-4 py-2 font-bold text-slate-500 hover:text-slate-700 transition-colors';
            document.getElementById('tab-printed').className = 'px-4 py-2 font-bold text-slate-500 hover:text-slate-700 transition-colors';
            
            document.getElementById('tab-' + tab).className = 'px-4 py-2 font-bold text-blue-600 border-b-2 border-blue-600 transition-colors';
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
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function printSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการที่จะพิมพ์', 'warning');
                return;
            }

            // Prepare for print: Hide unselected items
            document.querySelectorAll('.label-card').forEach(card => {
                const id = card.getAttribute('data-id');
                if (ids.includes(id)) {
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
                    updateStatus(ids, 1);
                }
            });
        }

        function markAsUnprinted() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
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
                    updateStatus(ids, 0);
                }
            });
        }

        function updateStatus(ids, status) {
            const formData = new FormData();
            formData.append('action', 'update_print_status');
            formData.append('ids', JSON.stringify(ids));
            formData.append('status', status);

            fetch('shipping.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
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