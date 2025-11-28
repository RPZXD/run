<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/ShirtOrder.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->connect();
$shirtOrder = new ShirtOrder($db);

// Handle AJAX Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_print_status') {
    $ids = json_decode($_POST['ids'], true);
    $status = (int)$_POST['status'];
    $success = true;
    
    foreach ($ids as $id) {
        $stmt = $db->prepare("UPDATE shirt_orders SET is_printed = ? WHERE id = ?");
        if (!$stmt->execute([$status, $id])) {
            $success = false;
        }
    }
    
    echo json_encode(['success' => $success]);
    exit;
}

// Get all shirt orders with POST shipping
$stmt = $db->query("SELECT * FROM shirt_orders WHERE shipping_method = 'POST' AND status IN ('pending', 'paid', 'shipped') ORDER BY created_at DESC");
$allOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pending_print = [];
$printed = [];

foreach ($allOrders as $item) {
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
    <title>พิมพ์ใบจัดส่งเสื้อ - Phichai Run 2026</title>
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
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    พิมพ์ใบจัดส่งเสื้อ
                </h1>
                <p class="text-slate-500 text-sm mt-1 ml-12">รายการสั่งซื้อเสื้อที่ต้องจัดส่งทางไปรษณีย์</p>
            </div>
            <div class="flex gap-2">
                <a href="shirt_orders.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl shadow-lg transition-all font-bold flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> กลับ
                </a>
                <button onclick="printSelected()" class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-yellow-500/30 hover:shadow-yellow-500/40 hover:-translate-y-0.5 transition-all font-bold flex items-center gap-2">
                    <i class="fas fa-print"></i> พิมพ์รายการที่เลือก
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-slate-200 no-print">
            <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-3 font-bold text-yellow-600 border-b-2 border-yellow-500 transition-all hover:bg-yellow-50/50 rounded-t-lg flex items-center gap-2">
                <span class="bg-yellow-100 text-yellow-600 text-xs px-2 py-0.5 rounded-full"><?php echo count($pending_print); ?></span>
                รอพิมพ์
            </button>
            <button onclick="switchTab('printed')" id="tab-printed" class="px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-t-lg flex items-center gap-2">
                <span class="bg-slate-200 text-slate-600 text-xs px-2 py-0.5 rounded-full"><?php echo count($printed); ?></span>
                พิมพ์แล้ว
            </button>
        </div>

        <!-- Content Area -->
        <div id="content-pending" class="tab-content">
            <?php if (empty($pending_print)): ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-4xl text-green-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">ยอดเยี่ยม!</h3>
                    <p class="text-slate-500 mt-2">ไม่มีรายการค้างพิมพ์ในขณะนี้</p>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2 mb-4 no-print bg-yellow-50 p-4 rounded-xl border border-yellow-100 shadow-sm">
                    <input type="checkbox" id="selectAllPending" onchange="toggleAll('pending')" class="w-5 h-5 rounded border-slate-300 text-yellow-600 focus:ring-yellow-500 cursor-pointer">
                    <label for="selectAllPending" class="font-bold text-slate-700 cursor-pointer select-none">เลือกทั้งหมด</label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                    <?php foreach ($pending_print as $row): ?>
                        <?php include 'shirt_shipping_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="content-printed" class="tab-content hidden">
            <?php if (empty($printed)): ?>
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
                    <button onclick="markAsUnprinted()" class="ml-auto text-sm text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors font-bold flex items-center gap-1">
                        <i class="fas fa-undo"></i> ย้ายกลับไป "รอพิมพ์"
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-container">
                    <?php foreach ($printed as $row): ?>
                        <?php include 'shirt_shipping_card.php'; ?>
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
            const btnPending = document.getElementById('tab-pending');
            const btnPrinted = document.getElementById('tab-printed');
            
            // Reset classes
            const inactiveClass = 'px-6 py-3 font-bold text-slate-500 hover:text-slate-700 transition-all hover:bg-slate-50 rounded-t-lg flex items-center gap-2';
            const activeClassPending = 'px-6 py-3 font-bold text-yellow-600 border-b-2 border-yellow-500 transition-all hover:bg-yellow-50/50 rounded-t-lg flex items-center gap-2';
            const activeClassPrinted = 'px-6 py-3 font-bold text-slate-700 border-b-2 border-slate-500 transition-all hover:bg-slate-50 rounded-t-lg flex items-center gap-2';
            
            btnPending.className = inactiveClass;
            btnPrinted.className = inactiveClass;
            
            if (tab === 'pending') {
                btnPending.className = activeClassPending;
            } else {
                btnPrinted.className = activeClassPrinted;
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
                cancelButtonText: 'ไม่, ยังไม่ได้พิมพ์',
                confirmButtonColor: '#EAB308'
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
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#EAB308'
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

            fetch('shirt_shipping.php', {
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
