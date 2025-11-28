<?php
require_once 'app/config/database.php';
require_once 'app/models/Registration.php';
require_once 'app/models/ShirtOrder.php';

$registrations = [];
$shirtOrders = [];
$error = '';
$searchQuery = '';
$searchType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchQuery = isset($_POST['query']) ? trim($_POST['query']) : '';
    
    if (empty($searchQuery)) {
        $error = "กรุณากรอกเบอร์โทรศัพท์หรือชื่อ";
    } else {
        $database = new Database();
        $db = $database->connect();
        
        // Determine search type: phone or name
        $phone = preg_replace('/\D/', '', $searchQuery);
        $isPhoneSearch = strlen($phone) >= 9;
        
        // Search Registrations (Running)
        if ($isPhoneSearch) {
            // Search by phone
            $registration = new Registration($db);
            $stmt = $registration->checkStatus($phone);
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $searchType = 'phone';
        } else {
            // Search by name (fuzzy search)
            $searchTerm = '%' . $searchQuery . '%';
            $stmt = $db->prepare("SELECT * FROM registrations WHERE full_name LIKE :name ORDER BY created_at DESC");
            $stmt->bindParam(':name', $searchTerm);
            $stmt->execute();
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $searchType = 'name';
        }
        
        // Search Shirt Orders
        if ($isPhoneSearch) {
            // Search by phone
            $stmt = $db->prepare("SELECT * FROM shirt_orders WHERE REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'+','') LIKE :phone ORDER BY created_at DESC");
            $phonePattern = '%' . $phone . '%';
            $stmt->bindParam(':phone', $phonePattern);
            $stmt->execute();
            $shirtOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Search by name (fuzzy search)
            $searchTerm = '%' . $searchQuery . '%';
            $stmt = $db->prepare("SELECT * FROM shirt_orders WHERE full_name LIKE :name ORDER BY created_at DESC");
            $stmt->bindParam(':name', $searchTerm);
            $stmt->execute();
            $shirtOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        if (empty($registrations) && empty($shirtOrders)) {
            $error = "ไม่พบข้อมูลจากคำค้นหา \"" . htmlspecialchars($searchQuery) . "\"";
        }
    }
}

$totalResults = count($registrations) + count($shirtOrders);
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบสถานะ - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="assets/images/logo01.JPG"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Kanit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#E63946',
                        secondary: '#1D3557',
                        accent: '#F4A261',
                        light: '#F1FAEE',
                        dark: '#111827'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased overflow-x-hidden text-gray-800 bg-light">

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 py-4 px-6">
        <div class="container mx-auto bg-white/90 backdrop-blur-md rounded-full shadow-lg border border-white/50 px-6 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary to-accent rounded-full blur opacity-75 group-hover:opacity-100 transition duration-200"></div>
                    <img src="assets/images/logo-1.png" alt="Logo" class="relative h-10 w-10 rounded-full border-2 border-white object-cover">
                </div>
                <span class="font-bold text-xl tracking-wide text-secondary group-hover:text-primary transition">PHICHAI RUN <span class="text-primary">2026</span></span>
            </a>
            
            <a href="index.php" class="text-gray-600 hover:text-primary transition font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
            </a>
        </div>
    </nav>

    <div class="relative min-h-screen pt-32 pb-20 flex flex-col items-center justify-start overflow-hidden hero-gradient">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-accent/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
        </div>

        <div class="container mx-auto px-4 max-w-3xl relative z-10">
            <div class="text-center mb-10 animate-fade-in-down">
                <h1 class="text-4xl md:text-5xl font-bold text-rose-500 mb-4">ตรวจสอบสถานะ</h1>
                <p class="text-lg text-white">ค้นหาจากเบอร์โทรศัพท์หรือชื่อของคุณ</p>
            </div>

            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up visible mb-10">
                <div class="p-8 md:p-10">
                    <form method="POST" class="relative">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors text-xl"></i>
                            </div>
                            <input type="text" name="query" class="w-full pl-14 pr-32 py-5 border-2 border-gray-100 rounded-2xl focus:outline-none focus:border-primary/50 focus:ring-4 focus:ring-primary/10 transition-all bg-gray-50 focus:bg-white text-xl font-medium tracking-wide placeholder-gray-300" placeholder="เบอร์โทร หรือ ชื่อ-นามสกุล" required value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit" class="absolute right-2 top-2 bottom-2 bg-primary hover:bg-red-600 text-white font-bold px-6 rounded-xl transition-all transform hover:scale-105 shadow-lg shadow-primary/30 flex items-center gap-2">
                                <i class="fas fa-search"></i> <span class="hidden sm:inline">ค้นหา</span>
                            </button>
                        </div>
                        <p class="text-center text-white/70 text-sm mt-4">
                            <i class="fas fa-info-circle mr-1"></i> ค้นหาได้ทั้งการสมัครวิ่งและการสั่งซื้อเสื้อ
                        </p>
                    </form>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="animate-fade-in-up bg-white rounded-2xl p-8 text-center shadow-lg border border-red-100">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search-minus text-3xl text-red-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ไม่พบข้อมูล</h3>
                    <p class="text-gray-500"><?php echo $error; ?></p>
                    <p class="text-gray-400 text-sm mt-4">ลองค้นหาด้วยเบอร์โทรหรือชื่ออื่น</p>
                </div>
            <?php endif; ?>

            <?php if ($totalResults > 0): ?>
                <div class="space-y-6 animate-fade-in-up">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="font-bold text-xl text-white">ผลการค้นหา</h3>
                        <span class="bg-white px-3 py-1 rounded-full text-sm font-medium text-gray-500 shadow-sm border border-gray-100">
                            พบ <?php echo $totalResults; ?> รายการ
                        </span>
                    </div>
                    
                    <!-- Running Registrations -->
                    <?php if (!empty($registrations)): ?>
                        <div class="mb-4">
                            <h4 class="text-lg font-bold text-white/90 mb-3 flex items-center gap-2">
                                <i class="fas fa-running text-accent"></i> การสมัครวิ่ง (<?php echo count($registrations); ?> รายการ)
                            </h4>
                        </div>
                        <?php foreach ($registrations as $row): ?>
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <!-- Status Bar -->
                                <?php 
                                    $statusColor = 'bg-yellow-500';
                                    $statusBg = 'bg-yellow-50';
                                    $statusText = 'text-yellow-700';
                                    $statusLabel = 'รอตรวจสอบ';
                                    $statusIcon = 'fa-clock';
                                    $statusDesc = 'เจ้าหน้าที่กำลังตรวจสอบหลักฐานการโอนเงิน';
                                    
                                    if ($row['status'] === 'approved') {
                                        $statusColor = 'bg-green-500';
                                        $statusBg = 'bg-green-50';
                                        $statusText = 'text-green-700';
                                        $statusLabel = 'อนุมัติแล้ว';
                                        $statusIcon = 'fa-check-circle';
                                        $statusDesc = 'การสมัครเสร็จสมบูรณ์ เจอกันวันงานครับ!';
                                    } elseif ($row['status'] === 'rejected') {
                                        $statusColor = 'bg-red-500';
                                        $statusBg = 'bg-red-50';
                                        $statusText = 'text-red-700';
                                        $statusLabel = 'ต้องแก้ไข';
                                        $statusIcon = 'fa-exclamation-circle';
                                        $statusDesc = 'พบปัญหาในข้อมูลการสมัคร';
                                    }
                                ?>
                                <div class="h-2 w-full <?php echo $statusColor; ?>"></div>
                                
                                <div class="p-6 md:p-8">
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                                        <div>
                                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                                <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-full">
                                                    <i class="fas fa-running mr-1"></i> สมัครวิ่ง
                                                </span>
                                                <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                                                <span class="<?php echo $statusBg . ' ' . $statusText; ?> px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5">
                                                    <i class="fas <?php echo $statusIcon; ?>"></i> <?php echo $statusLabel; ?>
                                                </span>
                                            </div>
                                            <p class="text-gray-500 text-sm flex items-center gap-2">
                                                <i class="far fa-calendar-alt"></i> สมัครเมื่อ: <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-5 border border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-primary">
                                                <i class="fas fa-running"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-bold uppercase">ประเภท</p>
                                                <p class="font-medium text-gray-700"><?php echo htmlspecialchars($row['category']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-500">
                                                <i class="fas fa-tshirt"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-bold uppercase">ไซส์เสื้อ</p>
                                                <p class="font-medium text-gray-700"><?php echo htmlspecialchars($row['shirt_size']); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($row['status'] === 'rejected' && !empty($row['reject_reason'])): ?>
                                        <div class="mt-6 bg-red-50 border border-red-100 rounded-xl p-5">
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-info-circle text-red-500 mt-1 text-lg"></i>
                                                <div>
                                                    <h4 class="font-bold text-red-800 mb-1">สิ่งที่ต้องแก้ไข</h4>
                                                    <p class="text-red-600 text-sm"><?php echo htmlspecialchars($row['reject_reason']); ?></p>
                                                    <div class="mt-3">
                                                        <a href="https://m.me/phichairun2026" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition">
                                                            <i class="fab fa-facebook-messenger"></i> ติดต่อเจ้าหน้าที่
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-6 flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-100 rounded-lg p-3">
                                            <i class="fas fa-info-circle text-gray-300"></i>
                                            <?php echo $statusDesc; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Shirt Orders -->
                    <?php if (!empty($shirtOrders)): ?>
                        <div class="mb-4 mt-8">
                            <h4 class="text-lg font-bold text-white/90 mb-3 flex items-center gap-2">
                                <i class="fas fa-tshirt text-yellow-400"></i> การสั่งซื้อเสื้อ (<?php echo count($shirtOrders); ?> รายการ)
                            </h4>
                        </div>
                        <?php foreach ($shirtOrders as $order): ?>
                            <?php
                                // Status mapping for shirt orders
                                $shirtStatusColor = 'bg-yellow-500';
                                $shirtStatusBg = 'bg-yellow-50';
                                $shirtStatusText = 'text-yellow-700';
                                $shirtStatusLabel = 'รอตรวจสอบ';
                                $shirtStatusIcon = 'fa-clock';
                                $shirtStatusDesc = 'เจ้าหน้าที่กำลังตรวจสอบหลักฐานการโอนเงิน';
                                
                                switch ($order['status']) {
                                    case 'paid':
                                        $shirtStatusColor = 'bg-blue-500';
                                        $shirtStatusBg = 'bg-blue-50';
                                        $shirtStatusText = 'text-blue-700';
                                        $shirtStatusLabel = 'ชำระแล้ว';
                                        $shirtStatusIcon = 'fa-check';
                                        $shirtStatusDesc = 'ยืนยันการชำระเงินแล้ว รอจัดส่ง';
                                        break;
                                    case 'shipped':
                                        $shirtStatusColor = 'bg-indigo-500';
                                        $shirtStatusBg = 'bg-indigo-50';
                                        $shirtStatusText = 'text-indigo-700';
                                        $shirtStatusLabel = 'จัดส่งแล้ว';
                                        $shirtStatusIcon = 'fa-shipping-fast';
                                        $shirtStatusDesc = 'จัดส่งแล้ว กรุณารอรับพัสดุ';
                                        break;
                                    case 'completed':
                                        $shirtStatusColor = 'bg-green-500';
                                        $shirtStatusBg = 'bg-green-50';
                                        $shirtStatusText = 'text-green-700';
                                        $shirtStatusLabel = 'เสร็จสิ้น';
                                        $shirtStatusIcon = 'fa-check-circle';
                                        $shirtStatusDesc = 'ดำเนินการเสร็จสิ้น';
                                        break;
                                    case 'cancelled':
                                        $shirtStatusColor = 'bg-red-500';
                                        $shirtStatusBg = 'bg-red-50';
                                        $shirtStatusText = 'text-red-700';
                                        $shirtStatusLabel = 'ยกเลิก';
                                        $shirtStatusIcon = 'fa-times-circle';
                                        $shirtStatusDesc = 'คำสั่งซื้อถูกยกเลิก';
                                        break;
                                }
                            ?>
                            <div class="bg-white rounded-2xl shadow-lg border border-yellow-200 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <!-- Status Bar -->
                                <div class="h-2 w-full <?php echo $shirtStatusColor; ?>"></div>
                                
                                <div class="p-6 md:p-8">
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                                        <div>
                                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">
                                                    <i class="fas fa-tshirt mr-1"></i> สั่งเสื้อ
                                                </span>
                                                <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($order['full_name']); ?></h3>
                                                <span class="<?php echo $shirtStatusBg . ' ' . $shirtStatusText; ?> px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5">
                                                    <i class="fas <?php echo $shirtStatusIcon; ?>"></i> <?php echo $shirtStatusLabel; ?>
                                                </span>
                                            </div>
                                            <p class="text-gray-500 text-sm flex items-center gap-2">
                                                <i class="far fa-calendar-alt"></i> สั่งซื้อเมื่อ: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400 uppercase font-bold">เลขออเดอร์</p>
                                            <p class="text-lg font-mono font-bold text-yellow-600"><?php echo htmlspecialchars($order['order_number']); ?></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-yellow-50 rounded-xl p-5 border border-yellow-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-yellow-600">
                                                <i class="fas fa-tshirt"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-bold uppercase">รายการเสื้อ</p>
                                                <p class="font-medium text-gray-700 text-sm"><?php echo htmlspecialchars($order['shirt_sizes']); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-blue-500">
                                                <i class="fas fa-hashtag"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-bold uppercase">จำนวน</p>
                                                <p class="font-medium text-gray-700"><?php echo $order['shirt_quantity']; ?> ตัว</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-green-500">
                                                <i class="fas fa-money-bill"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-bold uppercase">ยอดเงิน</p>
                                                <p class="font-medium text-green-600"><?php echo number_format($order['payment_amount']); ?> บาท</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-center gap-3 text-sm">
                                            <i class="fas fa-truck text-gray-400"></i>
                                            <span class="text-gray-600">
                                                การรับเสื้อ: <strong><?php echo $order['shipping_method'] === 'POST' ? 'จัดส่งไปรษณีย์' : 'รับเอง'; ?></strong>
                                            </span>
                                        </div>
                                        <?php if (!empty($order['tracking_number'])): ?>
                                        <div class="flex items-center gap-3 text-sm">
                                            <i class="fas fa-barcode text-gray-400"></i>
                                            <span class="text-gray-600">
                                                เลขพัสดุ: <strong class="text-indigo-600"><?php echo htmlspecialchars($order['tracking_number']); ?></strong>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-6 flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-100 rounded-lg p-3">
                                        <i class="fas fa-info-circle text-gray-300"></i>
                                        <?php echo $shirtStatusDesc; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer id="contact" class="bg-dark text-white pt-20 pb-10 relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="assets/images/logo01.JPG" class="h-16 w-16 rounded-full border-2 border-white/20">
                        <div>
                            <h3 class="text-2xl font-bold text-white">PHICHAI RUN 2026</h3>
                            <p class="text-gray-400 text-sm">พิชัยรัน2026</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-8 max-w-md leading-relaxed">
                        จัดขึ้นเพื่อเพื่อสมทบทุนจัดซื้อจอ LED, รถจัดหญ้า และพัฒนาการศึกษาโรงเรียนพิชัย
                    </p>
                    <div class="flex gap-4">
                        <a href="https://www.facebook.com/phichairun2026/" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center hover:bg-blue-500 transition transform hover:scale-110">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center hover:bg-green-400 transition transform hover:scale-110">
                            <i class="fab fa-line"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-pink-600 flex items-center justify-center hover:bg-pink-500 transition transform hover:scale-110">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6">ลิงก์ด่วน</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="index.php" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> หน้าแรก</a></li>
                        <li><a href="register.php" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> สมัครวิ่ง</a></li>
                        <li><a href="order_shirt.php" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> สั่งเสื้อ</a></li>
                        <li><a href="check_status.php" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> ตรวจสอบสถานะ</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6">ติดต่อเรา</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1.5 text-primary"></i>
                            <span>โรงเรียนพิชัย<br>9/9 หมู่ 3 ต.ในเมือง อ.พิชัย<br>จ.อุตรดิตถ์ 53120</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-primary"></i>
                            <a href="tel:055-421-402" class="hover:text-white transition">055-421-402</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <a href="mailto:phichaischool@gmail.com" class="hover:text-white transition">phichaischool@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-gray-600 text-sm">
                    &copy; 2025 <span class="text-white">Phichai Run</span>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
