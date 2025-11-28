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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Kanit', 'sans-serif'] },
                    colors: {
                        primary: '#E63946',
                        secondary: '#1D3557',
                        accent: '#F4A261'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-secondary">
                    <i class="fas fa-tshirt text-yellow-500 mr-2"></i> จัดการออเดอร์เสื้อ
                </h1>
                <p class="text-gray-500 mt-1">Shirt Orders (แยกจากการสมัครวิ่ง)</p>
            </div>
            <div class="flex gap-2">
                <a href="shirt_shipping.php" class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-4 py-2 rounded-lg transition shadow-lg shadow-yellow-500/30">
                    <i class="fas fa-print mr-2"></i> พิมพ์ใบจัดส่ง
                </a>
                <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> กลับ Dashboard
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">ออเดอร์ทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-tshirt text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">จำนวนเสื้อ</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total_shirts'] ?? 0; ?> ตัว</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-money-bill text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">รายได้รวม</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($stats['total_revenue'] ?? 0); ?> ฿</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">รอดำเนินการ</p>
                        <p class="text-2xl font-bold text-gray-800">
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
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="ค้นหา ชื่อ, เบอร์โทร, เลขออเดอร์..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">ทุกสถานะ</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>รอตรวจสอบ</option>
                    <option value="paid" <?php echo $statusFilter === 'paid' ? 'selected' : ''; ?>>ชำระแล้ว</option>
                    <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>จัดส่งแล้ว</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                </select>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> ค้นหา
                </button>
                <a href="shirt_orders.php" class="text-gray-500 hover:text-gray-700">รีเซ็ต</a>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">เลขออเดอร์</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">ผู้สั่ง</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">ไซส์เสื้อ</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">จำนวน</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">ยอดเงิน</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">การจัดส่ง</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">สถานะ</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">วันที่</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-600">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($allOrders) === 0): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>ไม่พบออเดอร์</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($allOrders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-bold text-yellow-600"><?php echo htmlspecialchars($order['order_number']); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['phone']); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm"><?php echo htmlspecialchars($order['shirt_sizes']); ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold"><?php echo $order['shirt_quantity']; ?></span> ตัว
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-green-600"><?php echo number_format($order['payment_amount']); ?> ฿</span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($order['shipping_method'] === 'POST'): ?>
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                        <i class="fas fa-truck mr-1"></i> ไปรษณีย์
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                        <i class="fas fa-store mr-1"></i> รับเอง
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'shipped' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-gray-100 text-gray-700',
                                    'cancelled' => 'bg-red-100 text-red-700'
                                ];
                                $statusLabels = [
                                    'pending' => 'รอตรวจสอบ',
                                    'paid' => 'ชำระแล้ว',
                                    'shipped' => 'จัดส่งแล้ว',
                                    'completed' => 'เสร็จสิ้น',
                                    'cancelled' => 'ยกเลิก'
                                ];
                                $color = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                                $label = $statusLabels[$order['status']] ?? $order['status'];
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $color; ?>">
                                    <?php echo $label; ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="viewOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)" 
                                            class="text-blue-500 hover:text-blue-700" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')" 
                                            class="text-green-500 hover:text-green-700" title="อัพเดตสถานะ">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($order['payment_slip']): ?>
                                    <a href="../<?php echo htmlspecialchars($order['payment_slip']); ?>" target="_blank" 
                                       class="text-purple-500 hover:text-purple-700" title="ดูสลิป">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    <?php endif; ?>
                                    <button onclick="deleteOrder(<?php echo $order['id']; ?>)" 
                                            class="text-red-500 hover:text-red-700" title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-secondary">รายละเอียดออเดอร์</h3>
                    <button onclick="closeModal('viewModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="orderDetails"></div>
            </div>
        </div>
    </div>

    <script>
    function viewOrder(order) {
        const statusLabels = {
            'pending': 'รอตรวจสอบ',
            'paid': 'ชำระแล้ว', 
            'shipped': 'จัดส่งแล้ว',
            'completed': 'เสร็จสิ้น',
            'cancelled': 'ยกเลิก'
        };
        
        let html = `
            <div class="space-y-4">
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <p class="text-sm text-gray-500">เลขออเดอร์</p>
                    <p class="text-xl font-bold text-yellow-600">${order.order_number}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ชื่อผู้สั่ง</p>
                        <p class="font-bold">${order.full_name}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">เบอร์โทร</p>
                        <p class="font-bold">${order.phone}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">ที่อยู่</p>
                        <p class="font-bold">${order.address}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ไซส์เสื้อ</p>
                        <p class="font-bold">${order.shirt_sizes}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">จำนวน</p>
                        <p class="font-bold">${order.shirt_quantity} ตัว</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ยอดเงิน</p>
                        <p class="font-bold text-green-600">${Number(order.payment_amount).toLocaleString()} บาท</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">การจัดส่ง</p>
                        <p class="font-bold">${order.shipping_method === 'POST' ? 'ไปรษณีย์' : 'รับเอง'}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">สถานะ</p>
                        <p class="font-bold">${statusLabels[order.status] || order.status}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">วันที่สั่ง</p>
                        <p class="font-bold">${new Date(order.created_at).toLocaleString('th-TH')}</p>
                    </div>
                </div>
                ${order.payment_date || order.payment_time ? `
                <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                    <p class="text-sm text-gray-500 mb-2"><i class="fas fa-calendar-check mr-1"></i> ข้อมูลการโอนเงิน</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">วันที่โอน</p>
                            <p class="font-bold text-green-700">${order.payment_date || '-'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">เวลาโอน</p>
                            <p class="font-bold text-green-700">${order.payment_time || '-'}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${order.tracking_number ? `
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">เลขพัสดุ</p>
                    <p class="font-bold text-blue-600">${order.tracking_number}</p>
                </div>
                ` : ''}
                ${order.notes ? `
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">หมายเหตุ</p>
                    <p>${order.notes}</p>
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
    
    function updateStatus(id, currentStatus) {
        Swal.fire({
            title: 'อัพเดตสถานะ',
            html: `
                <select id="newStatus" class="w-full p-2 border border-gray-300 rounded-lg mb-4">
                    <option value="pending" ${currentStatus === 'pending' ? 'selected' : ''}>รอตรวจสอบ</option>
                    <option value="paid" ${currentStatus === 'paid' ? 'selected' : ''}>ชำระแล้ว</option>
                    <option value="shipped" ${currentStatus === 'shipped' ? 'selected' : ''}>จัดส่งแล้ว</option>
                    <option value="completed" ${currentStatus === 'completed' ? 'selected' : ''}>เสร็จสิ้น</option>
                    <option value="cancelled" ${currentStatus === 'cancelled' ? 'selected' : ''}>ยกเลิก</option>
                </select>
                <input type="text" id="trackingNumber" placeholder="เลขพัสดุ (ถ้ามี)" class="w-full p-2 border border-gray-300 rounded-lg mb-4">
                <textarea id="orderNotes" placeholder="หมายเหตุ (ถ้ามี)" class="w-full p-2 border border-gray-300 rounded-lg" rows="2"></textarea>
            `,
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#EAB308',
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
            title: 'ยืนยันการลบ?',
            text: 'คุณต้องการลบออเดอร์นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
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

    // Show success message
    <?php if (isset($_GET['msg'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: '<?php echo $_GET['msg'] === 'updated' ? 'อัพเดตสถานะเรียบร้อย' : 'ลบออเดอร์เรียบร้อย'; ?>',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>
    </script>
</body>
</html>
