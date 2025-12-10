<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/ShirtOrder.php';

$database = new Database();
$db = $database->connect();
$shirtOrder = new ShirtOrder($db);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $id = $_POST['id'];
                $status = $_POST['status'];
                $tracking = isset($_POST['tracking_number']) ? $_POST['tracking_number'] : null;
                $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
                $shirtOrder->updateStatus($id, $status, $tracking, $notes);
                header('Location: shirt_orders.php?msg=updated');
                exit;
                break;
            case 'update_order':
                $id = $_POST['id'];
                $data = [
                    'full_name' => $_POST['full_name'],
                    'phone' => $_POST['phone'],
                    'email' => isset($_POST['email']) ? $_POST['email'] : null,
                    'citizen_id' => isset($_POST['citizen_id']) ? $_POST['citizen_id'] : null,
                    'address' => $_POST['address'],
                    'shirt_sizes' => $_POST['shirt_sizes'],
                    'shirt_quantity' => (int)$_POST['shirt_quantity'],
                    'shipping_method' => $_POST['shipping_method'],
                    'payment_amount' => $_POST['payment_amount'],
                    'status' => $_POST['status'],
                    'tracking_number' => isset($_POST['tracking_number']) ? $_POST['tracking_number'] : null,
                    'notes' => isset($_POST['notes']) ? $_POST['notes'] : null
                ];
                $shirtOrder->update($id, $data);
                header('Location: shirt_orders.php?msg=edited');
                exit;
                break;
            case 'delete':
                $id = $_POST['id'];
                $shirtOrder->delete($id);
                header('Location: shirt_orders.php?msg=deleted');
                exit;
                break;
        }
    }
}

// Get stats
$stats = $shirtOrder->getStats();

// Get all orders
$orders = $shirtOrder->readAll();
$allOrders = $orders->fetchAll(PDO::FETCH_ASSOC);

// Filter by status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
if ($statusFilter) {
    $allOrders = array_filter($allOrders, function($o) use ($statusFilter) {
        return $o['status'] === $statusFilter;
    });
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $allOrders = array_filter($allOrders, function($o) use ($search) {
        return stripos($o['full_name'], $search) !== false ||
               stripos($o['phone'], $search) !== false ||
               stripos($o['order_number'], $search) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการออเดอร์เสื้อ - Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['K2D', 'sans-serif'] },
                    colors: {
                        primary: '#E63946',
                        secondary: '#1D3557',
                        accent: '#F4A261'
                    }
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
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 font-sans min-h-screen">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in-up">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 bg-clip-text text-transparent">
                    <i class="fas fa-tshirt text-amber-500 mr-2 my-5"></i> จัดการออเดอร์เสื้อ
                </h1>
                <p class="text-gray-500 mt-1">Shirt Orders (แยกจากการสมัครวิ่ง)</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportToExcel()" class="group relative bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl transition-all duration-300 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 font-medium flex items-center gap-2">
                    <i class="fas fa-file-excel group-hover:animate-pulse"></i> 
                    <span>Export Excel</span>
                </button>
                <a href="shipping.php" class="group relative bg-gradient-to-r from-amber-400 via-yellow-500 to-orange-500 hover:from-amber-500 hover:via-yellow-600 hover:to-orange-600 text-white px-5 py-2.5 rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-0.5 font-medium flex items-center gap-2">
                    <i class="fas fa-print group-hover:animate-pulse"></i> 
                    <span>พิมพ์ใบจัดส่ง</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-blue-500/5 p-6 card-hover border border-slate-100 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">ออเดอร์ทั้งหมด</p>
                        <p class="text-3xl font-bold text-slate-800"><?php echo $stats['total']; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg shadow-amber-500/5 p-6 card-hover border border-slate-100 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="fas fa-tshirt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">จำนวนเสื้อ</p>
                        <p class="text-3xl font-bold text-slate-800"><?php echo $stats['total_shirts'] ?? 0; ?> <span class="text-sm font-normal text-gray-400">ตัว</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg shadow-emerald-500/5 p-6 card-hover border border-slate-100 animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-coins text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">รายได้รวม</p>
                        <p class="text-3xl font-bold text-slate-800"><?php echo number_format($stats['total_revenue'] ?? 0); ?> <span class="text-sm font-normal text-gray-400">฿</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg shadow-orange-500/5 p-6 card-hover border border-slate-100 animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <i class="fas fa-hourglass-half text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider">รอดำเนินการ</p>
                        <p class="text-3xl font-bold text-slate-800">
                            <?php 
                            $pending = 0;
                            foreach ($stats['by_status'] as $s) {
                                if ($s['status'] === 'pending') $pending = $s['count'];
                            }
                            echo $pending;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-5 mb-6 border border-slate-100 animate-fade-in-up" style="animation-delay: 0.5s">
            <form method="GET" class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px] relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="ค้นหา ชื่อ, เบอร์โทร, เลขออเดอร์..." 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 focus:bg-white transition-all duration-300">
                </div>
                <select name="status" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 focus:bg-white transition-all duration-300 cursor-pointer min-w-[150px]">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>🕐 รอตรวจสอบ</option>
                    <option value="paid" <?php echo $statusFilter === 'paid' ? 'selected' : ''; ?>>✅ ชำระแล้ว</option>
                    <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>🚚 จัดส่งแล้ว</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>✔️ เสร็จสิ้น</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>❌ ยกเลิก</option>
                </select>
                <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 font-medium flex items-center gap-2 hover:-translate-y-0.5">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
                <a href="shirt_orders.php" class="text-gray-500 hover:text-amber-600 transition-colors font-medium px-3 py-2 hover:bg-amber-50 rounded-lg">
                    <i class="fas fa-redo mr-1"></i> รีเซ็ต
                </a>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 overflow-hidden border border-slate-100 animate-fade-in-up" style="animation-delay: 0.6s">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">เลขออเดอร์</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้สั่ง</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ไซส์เสื้อ</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">จำนวน</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ยอดเงิน</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">การจัดส่ง</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">วันที่</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (count($allOrders) === 0): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-4xl text-slate-300"></i>
                                    </div>
                                    <p class="text-slate-400 text-lg font-medium">ไม่พบออเดอร์</p>
                                    <p class="text-slate-300 text-sm mt-1">ยังไม่มีรายการสั่งซื้อเสื้อ</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($allOrders as $order): ?>
                        <tr class="hover:bg-amber-50/50 transition-all duration-200 group">
                            <td class="px-5 py-4">
                                <span class="font-mono text-sm font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200"><?php echo htmlspecialchars($order['order_number']); ?></span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        <?php echo mb_substr($order['full_name'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                        <div class="text-xs text-slate-400 flex items-center gap-1">
                                            <i class="fas fa-phone-alt text-[10px]"></i> <?php echo htmlspecialchars($order['phone']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-slate-700 bg-slate-100 px-2 py-1 rounded"><?php echo htmlspecialchars($order['shirt_sizes']); ?></span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800"><?php echo $order['shirt_quantity']; ?></span>
                                <span class="text-slate-400 text-sm">ตัว</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-emerald-600 text-lg"><?php echo number_format($order['payment_amount']); ?></span>
                                <span class="text-slate-400 text-sm">฿</span>
                            </td>
                            <td class="px-5 py-4">
                                <?php if ($order['shipping_method'] === 'POST'): ?>
                                    <span class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-full border border-blue-200 inline-flex items-center gap-1">
                                        <i class="fas fa-truck"></i> ไปรษณีย์
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1.5 text-xs font-medium bg-slate-50 text-slate-600 rounded-full border border-slate-200 inline-flex items-center gap-1">
                                        <i class="fas fa-store"></i> รับเอง
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <?php
                                $statusStyles = [
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-300 ring-yellow-500/20',
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-emerald-500/20',
                                    'shipped' => 'bg-blue-50 text-blue-700 border-blue-300 ring-blue-500/20',
                                    'completed' => 'bg-slate-50 text-slate-700 border-slate-300 ring-slate-500/20',
                                    'cancelled' => 'bg-red-50 text-red-700 border-red-300 ring-red-500/20'
                                ];
                                $statusLabels = [
                                    'pending' => '🕐 รอตรวจสอบ',
                                    'paid' => '✅ ชำระแล้ว',
                                    'shipped' => '🚚 จัดส่งแล้ว',
                                    'completed' => '✔️ เสร็จสิ้น',
                                    'cancelled' => '❌ ยกเลิก'
                                ];
                                $style = $statusStyles[$order['status']] ?? 'bg-gray-50 text-gray-700 border-gray-300';
                                $label = $statusLabels[$order['status']] ?? $order['status'];
                                ?>
                                <span class="px-3 py-1.5 text-xs font-bold rounded-full border ring-2 ring-offset-1 <?php echo $style; ?>">
                                    <?php echo $label; ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm text-slate-600">
                                    <i class="far fa-calendar-alt text-slate-400 mr-1"></i>
                                    <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                                </div>
                                <div class="text-xs text-slate-400">
                                    <i class="far fa-clock mr-1"></i>
                                    <?php echo date('H:i', strtotime($order['created_at'])); ?> น.
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="viewOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)" 
                                            class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)" 
                                            class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($order['payment_slip']): ?>
                                    <button onclick="viewSlip(<?php echo htmlspecialchars(json_encode($order)); ?>)" 
                                       class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 hover:bg-purple-500 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" title="ดูสลิป">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button onclick="deleteOrder(<?php echo $order['id']; ?>)" 
                                            class="w-9 h-9 rounded-full bg-red-50 text-red-600 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md" title="ลบ">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-gradient-to-r from-slate-50 to-slate-100 flex justify-between items-center">
                <span class="text-sm text-slate-500">แสดง <span class="font-bold text-slate-700"><?php echo count($allOrders); ?></span> รายการ</span>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-tshirt"></i> รายละเอียดออเดอร์
                </h3>
                <button onclick="closeModal('viewModal')" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="orderDetails"></div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูลออเดอร์
                </h3>
                <button onclick="closeModal('editModal')" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST" class="p-6 max-h-[75vh] overflow-y-auto">
                <input type="hidden" name="action" value="update_order">
                <input type="hidden" name="id" id="editId">
                
                <!-- Order Number (Read-only) -->
                <div class="mb-5 bg-gradient-to-r from-amber-50 to-orange-50 p-4 rounded-xl border border-amber-200">
                    <label class="block text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">เลขออเดอร์</label>
                    <p id="editOrderNumber" class="text-xl font-bold text-amber-700 font-mono"></p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-user text-slate-400 mr-1"></i> ชื่อ-นามสกุล <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" id="editFullName" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-phone text-slate-400 mr-1"></i> เบอร์โทร <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="editPhone" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-envelope text-slate-400 mr-1"></i> อีเมล
                        </label>
                        <input type="email" name="email" id="editEmail"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Citizen ID -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-id-card text-slate-400 mr-1"></i> เลขบัตรประชาชน
                        </label>
                        <input type="text" name="citizen_id" id="editCitizenId"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                </div>
                
                <!-- Address -->
                <div class="mt-5">
                    <label class="block text-sm font-bold text-slate-600 mb-2">
                        <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> ที่อยู่
                    </label>
                    <textarea name="address" id="editAddress" rows="2"
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
                    <!-- Shirt Sizes -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-tshirt text-slate-400 mr-1"></i> ไซส์เสื้อ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="shirt_sizes" id="editShirtSizes" required placeholder="เช่น M, L, XL"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-sort-numeric-up text-slate-400 mr-1"></i> จำนวน <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="shirt_quantity" id="editShirtQuantity" required min="1"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Payment Amount -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-money-bill text-slate-400 mr-1"></i> ยอดเงิน (บาท)
                        </label>
                        <input type="number" name="payment_amount" id="editPaymentAmount" step="0.01"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <!-- Shipping Method -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-truck text-slate-400 mr-1"></i> วิธีจัดส่ง
                        </label>
                        <select name="shipping_method" id="editShippingMethod"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all cursor-pointer">
                            <option value="SELF">🏪 รับเอง</option>
                            <option value="POST">🚚 ไปรษณีย์ (+50)</option>
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-tag text-slate-400 mr-1"></i> สถานะ
                        </label>
                        <select name="status" id="editStatus"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all cursor-pointer">
                            <option value="pending">🕐 รอตรวจสอบ</option>
                            <option value="paid">✅ ชำระแล้ว</option>
                            <option value="shipped">🚚 จัดส่งแล้ว</option>
                            <option value="completed">✔️ เสร็จสิ้น</option>
                            <option value="cancelled">❌ ยกเลิก</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <!-- Tracking Number -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-barcode text-slate-400 mr-1"></i> เลขพัสดุ
                        </label>
                        <input type="text" name="tracking_number" id="editTrackingNumber" placeholder="EX123456789TH"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">
                            <i class="fas fa-sticky-note text-slate-400 mr-1"></i> หมายเหตุ
                        </label>
                        <input type="text" name="notes" id="editNotes"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3 mt-6 pt-5 border-t border-slate-200">
                    <button type="button" onclick="closeModal('editModal')" 
                            class="flex-1 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all duration-300">
                        <i class="fas fa-times mr-2"></i> ยกเลิก
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Slip Modal -->
    <div id="slipModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col md:flex-row">
            <!-- Image Section -->
            <div class="w-full md:w-1/2 bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center p-6 relative group min-h-[300px]">
                <button onclick="closeModal('slipModal')" class="absolute top-4 left-4 bg-white/10 hover:bg-white/20 text-white rounded-full w-10 h-10 flex items-center justify-center md:hidden z-10 transition">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <img id="slipImage" src="" alt="Payment Slip" class="max-h-[50vh] md:max-h-[80vh] max-w-full object-contain rounded-lg shadow-2xl transition-transform duration-300 hover:scale-105">
                <a id="slipDownloadLink" href="" target="_blank" class="absolute bottom-4 right-4 bg-white/90 text-slate-800 px-4 py-2 rounded-xl text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-white hover:-translate-y-1">
                    <i class="fas fa-external-link-alt mr-2"></i> เปิดรูปเต็ม
                </a>
            </div>
            <!-- Details Section -->
            <div class="w-full md:w-1/2 p-6 flex flex-col bg-white overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent flex items-center gap-2">
                        <i class="fas fa-receipt text-purple-500"></i> ตรวจสอบสลิป
                    </h3>
                    <button onclick="closeModal('slipModal')" class="text-slate-400 hover:text-slate-600 transition-colors hidden md:block">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="space-y-5 flex-grow overflow-y-auto pr-2">
                    <!-- Order Info -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-2xl border border-blue-100 shadow-sm">
                        <h4 class="text-sm font-bold text-blue-800 mb-3 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-shopping-bag text-blue-500"></i> ข้อมูลคำสั่งซื้อ
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500">สินค้า</span>
                                <span class="font-bold text-slate-800 text-right" id="slipOrderItems"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">การจัดส่ง</span>
                                <span class="font-bold text-slate-800" id="slipOrderShipping"></span>
                            </div>
                            
                            <!-- Breakdown -->
                            <div class="bg-white/80 rounded-xl p-3 mt-2 space-y-2 text-xs text-slate-600 border border-blue-100">
                                <div class="flex justify-between">
                                    <span>ค่าเสื้อ (<span id="slipOrderQty" class="font-bold"></span> ตัว × 250)</span>
                                    <span class="font-medium" id="slipOrderShirtTotal"></span>
                                </div>
                                <div class="flex justify-between" id="slipOrderShippingCostRow">
                                    <span>ค่าจัดส่ง</span>
                                    <span class="font-medium" id="slipOrderShippingCost"></span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-blue-200 flex justify-between items-end mt-2">
                                <span class="text-slate-500 font-medium">ยอดที่ต้องชำระ</span>
                                <span class="font-bold text-blue-600 text-xl" id="slipOrderExpectedAmount"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Info -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-5 rounded-2xl border border-purple-100 shadow-sm">
                        <h4 class="text-sm font-bold text-purple-800 mb-3 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-purple-500"></i> ข้อมูลการโอนเงิน
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-end border-b border-purple-100 pb-3">
                                <span class="text-sm text-slate-500">ชื่อผู้โอน</span>
                                <span class="font-bold text-slate-800 text-lg" id="slipName"></span>
                            </div>
                            <div class="flex justify-between items-end border-b border-purple-100 pb-3">
                                <span class="text-sm text-slate-500">ยอดเงิน</span>
                                <span class="font-bold text-emerald-600 text-2xl" id="slipAmount"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-1">
                                <div>
                                    <p class="text-xs text-slate-400 mb-1 uppercase tracking-wider">วันที่โอน</p>
                                    <p class="font-bold text-slate-800 bg-white px-3 py-2 rounded-lg border border-purple-100 inline-block shadow-sm" id="slipDate"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 mb-1 uppercase tracking-wider">เวลา</p>
                                    <p class="font-bold text-slate-800 bg-white px-3 py-2 rounded-lg border border-purple-100 inline-block shadow-sm" id="slipTime"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Form -->
                    <form method="POST" class="bg-gradient-to-br from-slate-50 to-gray-100 p-5 rounded-2xl border border-slate-200 shadow-sm">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" id="slipOrderId">
                        
                        <h4 class="text-sm font-bold text-slate-600 mb-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-tasks text-slate-500"></i> จัดการสถานะ
                        </h4>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">สถานะออเดอร์</label>
                            <div class="relative">
                                <select name="status" id="slipStatus" class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 appearance-none bg-white transition-all cursor-pointer">
                                    <option value="pending">🕐 รอตรวจสอบ</option>
                                    <option value="paid">✅ ชำระแล้ว (ตรวจสอบผ่าน)</option>
                                    <option value="shipped">🚚 จัดส่งแล้ว</option>
                                    <option value="completed">✔️ เสร็จสิ้น</option>
                                    <option value="cancelled">❌ ยกเลิก</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2">หมายเหตุ</label>
                            <textarea name="notes" id="slipNotes" rows="2" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/30 focus:border-purple-500 text-sm transition-all bg-white" placeholder="ระบุหมายเหตุเพิ่มเติม..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 hover:from-purple-700 hover:via-indigo-700 hover:to-purple-700 text-white font-bold py-3.5 rounded-xl transition-all duration-300 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 flex items-center justify-center gap-2 hover:-translate-y-0.5">
                            <i class="fas fa-check-circle"></i> บันทึกการตรวจสอบ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function viewOrder(order) {
        const statusLabels = {
            'pending': '🕐 รอตรวจสอบ',
            'paid': '✅ ชำระแล้ว', 
            'shipped': '🚚 จัดส่งแล้ว',
            'completed': '✔️ เสร็จสิ้น',
            'cancelled': '❌ ยกเลิก'
        };
        
        let html = `
            <div class="space-y-5">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-200 shadow-sm">
                    <p class="text-xs text-amber-600 uppercase tracking-wider font-bold mb-1">เลขออเดอร์</p>
                    <p class="text-2xl font-bold text-amber-600 font-mono">${order.order_number}</p>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">ชื่อผู้สั่ง</p>
                        <p class="font-bold text-slate-800">${order.full_name}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">เบอร์โทร</p>
                        <p class="font-bold text-slate-800">${order.phone}</p>
                    </div>
                    <div class="col-span-2 bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">ที่อยู่</p>
                        <p class="font-medium text-slate-800">${order.address}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">ไซส์เสื้อ</p>
                        <p class="font-bold text-slate-800">${order.shirt_sizes}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">จำนวน</p>
                        <p class="font-bold text-slate-800">${order.shirt_quantity} <span class="text-slate-400 font-normal">ตัว</span></p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200">
                        <p class="text-xs text-emerald-600 uppercase tracking-wider mb-1">ยอดเงิน</p>
                        <p class="font-bold text-emerald-600 text-xl">${Number(order.payment_amount).toLocaleString()} <span class="text-sm">฿</span></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">การจัดส่ง</p>
                        <p class="font-bold text-slate-800">${order.shipping_method === 'POST' ? '🚚 ไปรษณีย์' : '🏪 รับเอง'}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">สถานะ</p>
                        <p class="font-bold text-slate-800">${statusLabels[order.status] || order.status}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">วันที่สั่ง</p>
                        <p class="font-bold text-slate-800">${new Date(order.created_at).toLocaleString('th-TH')}</p>
                    </div>
                </div>
                ${order.payment_date || order.payment_time ? `
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-5 border border-emerald-200 shadow-sm">
                    <p class="text-xs text-emerald-600 uppercase tracking-wider font-bold mb-3"><i class="fas fa-calendar-check mr-2"></i>ข้อมูลการโอนเงิน</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-1">วันที่โอน</p>
                            <p class="font-bold text-emerald-700">${order.payment_date || '-'}</p>
                        </div>
                        <div class="bg-white/80 rounded-xl p-3">
                            <p class="text-xs text-slate-400 mb-1">เวลาโอน</p>
                            <p class="font-bold text-emerald-700">${order.payment_time || '-'}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${order.tracking_number ? `
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-200 shadow-sm">
                    <p class="text-xs text-blue-600 uppercase tracking-wider font-bold mb-1"><i class="fas fa-truck mr-2"></i>เลขพัสดุ</p>
                    <p class="font-bold text-blue-700 text-lg font-mono">${order.tracking_number}</p>
                </div>
                ` : ''}
                ${order.notes ? `
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1"><i class="fas fa-sticky-note mr-2"></i>หมายเหตุ</p>
                    <p class="text-slate-700">${order.notes}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('orderDetails').innerHTML = html;
        document.getElementById('viewModal').classList.remove('hidden');
        document.getElementById('viewModal').classList.add('flex');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
    
    function editOrder(order) {
        document.getElementById('editId').value = order.id;
        document.getElementById('editOrderNumber').textContent = order.order_number;
        document.getElementById('editFullName').value = order.full_name || '';
        document.getElementById('editPhone').value = order.phone || '';
        document.getElementById('editEmail').value = order.email || '';
        document.getElementById('editCitizenId').value = order.citizen_id || '';
        document.getElementById('editAddress').value = order.address || '';
        document.getElementById('editShirtSizes').value = order.shirt_sizes || '';
        document.getElementById('editShirtQuantity').value = order.shirt_quantity || 1;
        document.getElementById('editPaymentAmount').value = order.payment_amount || '';
        document.getElementById('editShippingMethod').value = order.shipping_method || 'SELF';
        document.getElementById('editStatus').value = order.status || 'pending';
        document.getElementById('editTrackingNumber').value = order.tracking_number || '';
        document.getElementById('editNotes').value = order.notes || '';
        
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }
    
    function updateStatus(id, currentStatus) {
        Swal.fire({
            title: '<span class="text-slate-800">อัพเดตสถานะ</span>',
            html: `
                <div class="text-left">
                    <label class="block text-sm font-bold text-slate-600 mb-2">สถานะใหม่</label>
                    <select id="newStatus" class="w-full p-3 border border-slate-200 rounded-xl mb-4 focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                        <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>🕐 รอตรวจสอบ</option>
                        <option value="paid" ${currentStatus === 'paid' ? 'selected' : ''}>✅ ชำระแล้ว</option>
                        <option value="shipped" ${currentStatus === 'shipped' ? 'selected' : ''}>🚚 จัดส่งแล้ว</option>
                        <option value="completed" ${currentStatus === 'completed' ? 'selected' : ''}>✔️ เสร็จสิ้น</option>
                        <option value="cancelled" ${currentStatus === 'cancelled' ? 'selected' : ''}>❌ ยกเลิก</option>
                    </select>
                    <label class="block text-sm font-bold text-slate-600 mb-2">เลขพัสดุ (ถ้ามี)</label>
                    <input type="text" id="trackingNumber" placeholder="EX123456789TH" class="w-full p-3 border border-slate-200 rounded-xl mb-4 focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                    <label class="block text-sm font-bold text-slate-600 mb-2">หมายเหตุ</label>
                    <textarea id="orderNotes" placeholder="ระบุหมายเหตุเพิ่มเติม..." class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500" rows="2"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check mr-2"></i>บันทึก',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
            confirmButtonColor: '#F59E0B',
            cancelButtonColor: '#94A3B8',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-3 font-bold',
                cancelButton: 'rounded-xl px-6 py-3 font-bold'
            },
            preConfirm: () => {
                return {
                    status: document.getElementById('newStatus').value,
                    tracking: document.getElementById('trackingNumber').value,
                    notes: document.getElementById('orderNotes').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="status" value="${result.value.status}">
                    <input type="hidden" name="tracking_number" value="${result.value.tracking}">
                    <input type="hidden" name="notes" value="${result.value.notes}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function deleteOrder(id) {
        Swal.fire({
            title: '<span class="text-red-600">ยืนยันการลบ?</span>',
            html: '<p class="text-slate-500">คุณต้องการลบออเดอร์นี้หรือไม่?<br><span class="text-red-500 text-sm">การดำเนินการนี้ไม่สามารถย้อนกลับได้</span></p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#94A3B8',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>ลบออเดอร์',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>ยกเลิก',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-3 font-bold',
                cancelButton: 'rounded-xl px-6 py-3 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function viewSlip(order) {
        const slipPath = '../' + order.payment_slip;
        document.getElementById('slipImage').src = slipPath;
        document.getElementById('slipDownloadLink').href = slipPath;
        
        // Order Info
        document.getElementById('slipOrderItems').textContent = order.shirt_sizes;
        document.getElementById('slipOrderShipping').textContent = order.shipping_method === 'POST' ? '🚚 ไปรษณีย์' : '🏪 รับเอง';
        
        // Breakdown
        const qty = parseInt(order.shirt_quantity);
        const shirtTotal = qty * 250;
        document.getElementById('slipOrderQty').textContent = qty;
        document.getElementById('slipOrderShirtTotal').textContent = shirtTotal.toLocaleString() + ' ฿';
        
        if (order.shipping_method === 'POST') {
            document.getElementById('slipOrderShippingCostRow').classList.remove('hidden');
            document.getElementById('slipOrderShippingCost').textContent = '50 ฿';
        } else {
            document.getElementById('slipOrderShippingCostRow').classList.add('hidden');
        }

        document.getElementById('slipOrderExpectedAmount').textContent = Number(order.payment_amount).toLocaleString() + ' ฿';

        document.getElementById('slipName').textContent = order.full_name;
        document.getElementById('slipAmount').textContent = Number(order.payment_amount).toLocaleString() + ' ฿';
        document.getElementById('slipDate').textContent = order.payment_date || '-';
        document.getElementById('slipTime').textContent = order.payment_time || '-';
        
        document.getElementById('slipOrderId').value = order.id;
        document.getElementById('slipStatus').value = order.status;
        document.getElementById('slipNotes').value = order.notes || '';
        
        document.getElementById('slipModal').classList.remove('hidden');
        document.getElementById('slipModal').classList.add('flex');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const viewModal = document.getElementById('viewModal');
        const slipModal = document.getElementById('slipModal');
        const editModal = document.getElementById('editModal');
        if (event.target == viewModal) {
            closeModal('viewModal');
        }
        if (event.target == slipModal) {
            closeModal('slipModal');
        }
        if (event.target == editModal) {
            closeModal('editModal');
        }
    }

    // Show success message
    <?php if (isset($_GET['msg'])): ?>
    Swal.fire({
        icon: 'success',
        title: '<span class="text-emerald-600">สำเร็จ!</span>',
        html: '<p class="text-slate-600"><?php 
            $messages = [
                'updated' => 'อัพเดตสถานะเรียบร้อย',
                'edited' => 'แก้ไขข้อมูลเรียบร้อย',
                'deleted' => 'ลบออเดอร์เรียบร้อย'
            ];
            echo $messages[$_GET['msg']] ?? 'ดำเนินการเรียบร้อย';
        ?></p>',
        timer: 2500,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-2xl'
        }
    });
    <?php endif; ?>

    // Export to Excel with Thai language support
    function exportToExcel() {
        const statusFilter = new URLSearchParams(window.location.search).get('status') || '';
        const url = 'export_excel.php?type=shirt_orders&status=' + statusFilter;
        
        const downloadLink = document.createElement("a");
        downloadLink.href = url;
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'กำลังดาวน์โหลดไฟล์ Excel...',
            showConfirmButton: false,
            timer: 2000
        });
    }
    </script>
</body>
</html>
