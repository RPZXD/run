<?php
require_once 'app/config/database.php';
require_once 'app/models/ShirtOrder.php';
require_once 'app/controllers/ShirtOrderController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
$status = '';
$orderNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();
    
    if ($db) {
        $controller = new ShirtOrderController($db);
        $result = $controller->create();
        $message = $result['message'];
        $status = $result['status'];
        $orderNumber = isset($result['order_number']) ? $result['order_number'] : '';
    } else {
        $message = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อเสื้อ - Phichai Run 2026</title>
    
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
                    }
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="antialiased text-gray-800 bg-light overflow-x-hidden">

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 py-4 px-6">
        <div class="container mx-auto bg-white/90 backdrop-blur-md rounded-full shadow-lg border border-white/50 px-6 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary to-accent rounded-full blur opacity-75 group-hover:opacity-100 transition duration-200"></div>
                    <img src="assets/images/logo01.JPG" alt="Logo" class="relative h-10 w-10 rounded-full border-2 border-white object-cover">
                </div>
                <span class="font-bold text-xl tracking-wide text-secondary group-hover:text-primary transition">PHICHAI RUN <span class="text-primary">2026</span></span>
            </a>
            
            <a href="index.php" class="text-gray-600 hover:text-primary transition font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
            </a>
        </div>
    </nav>

    <div class="relative min-h-screen pt-32 pb-20 flex items-center justify-center overflow-hidden hero-gradient">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-yellow-400/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-accent/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 max-w-3xl relative z-10">
            
            <?php if ($message): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: '<?php echo $status === "success" ? "success" : "error"; ?>',
                            title: '<?php echo $status === "success" ? "สำเร็จ!" : "ข้อผิดพลาด"; ?>',
                            html: '<?php echo $message; ?><?php if($orderNumber): ?><br><br><strong class="text-lg">กรุณาจดหมายเลขนี้ไว้!</strong><?php endif; ?>',
                            confirmButtonText: 'ตกลง',
                            customClass: {
                                popup: 'rounded-3xl shadow-xl border border-gray-100',
                                confirmButton: 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-bold py-3 px-8 rounded-full shadow-lg',
                                title: 'text-2xl font-bold text-secondary font-sans',
                                htmlContainer: 'text-gray-600 font-sans'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            <?php if ($status === 'success'): ?>
                                window.location.href = 'index.php';
                            <?php endif; ?>
                        });
                    });
                </script>
            <?php endif; ?>

            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up visible">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-8 text-center text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 relative z-10">
                        <i class="fas fa-tshirt mr-2"></i> สั่งซื้อเสื้อที่ระลึก
                    </h2>
                    <p class="opacity-90 text-lg relative z-10">เสื้อ Phichai Run 2026 ราคาตัวละ 250 บาท</p>
                </div>
                
                <form id="shirtOrderForm" action="order_shirt.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-10">
                    
                    <!-- Progress Bar -->
                    <div class="mb-10">
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                            <div id="progressBar" class="h-full bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full transition-all duration-500 ease-out" style="width:33%"></div>
                        </div>
                        <div class="flex justify-between text-sm mt-3 text-gray-500 font-medium">
                            <div class="text-yellow-600">ข้อมูลผู้สั่ง</div>
                            <div>เลือกเสื้อ</div>
                            <div>ชำระเงิน</div>
                        </div>
                    </div>

                    <!-- STEP 1: Customer Info -->
                    <div class="form-step" data-step="1">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-user-circle text-yellow-500"></i> ข้อมูลผู้สั่งซื้อ
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">คำนำหน้า <span class="text-red-500">*</span></label>
                                <select name="prefix" id="prefix_select" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white">
                                    <option value="" selected disabled>-- เลือก --</option>
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
                                    </optgroup>
                                    <optgroup label="วิชาชีพแพทย์">
                                        <option value="นพ.">นพ.</option>
                                        <option value="พญ.">พญ.</option>
                                    </optgroup>
                                    <option value="OTHER">อื่นๆ (กรอกเอง)</option>
                                </select>
                                <input type="text" name="prefix_custom" id="prefix_custom" class="hidden mt-2 w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="กรอกคำนำหน้า...">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-bold mb-2 text-sm">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name_input" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="ชื่อ นามสกุล">
                                <input type="hidden" name="full_name" id="hidden_full_name">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="0812345678">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-bold mb-2 text-sm">อีเมล <span class="text-gray-400 font-normal">(ไม่บังคับ)</span></label>
                                <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-700 font-bold mb-2 text-sm">ที่อยู่จัดส่ง <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <input type="text" name="postal_code" id="postal_code" maxlength="5" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="รหัสไปรษณีย์">
                                <input type="text" name="subdistrict" id="subdistrict" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="ตำบล">
                                <input type="text" name="district_province" id="district_province" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="อำเภอ / จังหวัด">
                            </div>
                            <p id="postal_help" class="text-xs text-yellow-600 mt-1 mb-3"></p>
                            <textarea name="address_detail" rows="2" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white" placeholder="บ้านเลขที่ หมู่บ้าน ซอย ถนน..."></textarea>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="button" class="next-btn bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-yellow-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Shirt Selection -->
                    <div class="form-step hidden" data-step="2">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-tshirt text-yellow-500"></i> เลือกไซส์และจำนวนเสื้อ
                        </h3>
                        
                        <!-- Size Chart -->
                        <div class="mb-8">
                            <img src="assets/images/shirt02.JPG" alt="ตารางไซส์เสื้อ" class="w-full max-w-2xl mx-auto rounded-2xl shadow-lg border border-gray-100">
                        </div>

                        <div class="mb-6">
                            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Standard Sizes (ผู้ใหญ่)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                                <!-- XS -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">XS</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('XS', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="XS" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('XS', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- S -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">S</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('S', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="S" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('S', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- M -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">M</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('M', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="M" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('M', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- L -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">L</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('L', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="L" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('L', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">XL</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- 2XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">2XL</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('2XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="2XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('2XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- 3XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">3XL</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('3XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="3XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('3XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- 4XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">4XL</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('4XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="4XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('4XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- 5XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">5XL</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('5XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="5XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('5XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Kids Sizes (เด็ก)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <!-- Kids S -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">S (เด็ก)</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('KS', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="KS" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('KS', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- Kids M -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">M (เด็ก)</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('KM', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="KM" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('KM', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- Kids L -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">L (เด็ก)</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('KL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="KL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('KL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- Kids XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">XL (เด็ก)</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('KXL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="KXL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('KXL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                                <!-- Kids 2XL -->
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 hover:border-yellow-400 transition">
                                    <span class="font-bold text-gray-700">2XL (เด็ก)</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="adjustQty('K2XL', -1)" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                        <input type="number" min="0" value="0" data-size="K2XL" class="shirt-qty-input w-12 text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-yellow-500" readonly>
                                        <button type="button" onclick="adjustQty('K2XL', 1)" class="w-8 h-8 rounded-full bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <label class="block text-gray-700 font-bold mb-4 text-sm">วิธีการรับเสื้อ</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="shipping_method" value="SELF" class="peer hidden" checked>
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-store text-2xl text-yellow-600"></i>
                                            <div>
                                                <p class="font-bold text-gray-800">รับเอง (ฟรี)</p>
                                                <p class="text-sm text-gray-500">รับที่โรงเรียนพิชัย</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="shipping_method" value="POST" class="peer hidden">
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-truck text-2xl text-yellow-600"></i>
                                            <div>
                                                <p class="font-bold text-gray-800">จัดส่งไปรษณีย์ (+50 บาท)</p>
                                                <p class="text-sm text-gray-500">จัดส่งทั่วประเทศ</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="mt-6 bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-600">รายการที่เลือก:</p>
                                    <p id="selected-sizes" class="font-bold text-gray-800">-</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600">รวมทั้งหมด:</p>
                                    <p class="text-2xl font-bold text-yellow-600"><span id="total-amount">0</span> บาท</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="shirt_sizes" id="hidden_shirt_sizes">
                        <input type="hidden" name="shirt_quantity" id="hidden_shirt_quantity">

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ
                            </button>
                            <button type="button" class="next-btn bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-yellow-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Payment -->
                    <div class="form-step hidden" data-step="3">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-yellow-500"></i> ชำระเงิน
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                            <!-- Bank Account Card -->
                            <div>
                                <label class="block text-gray-700 font-bold mb-3 text-sm">โอนเงินเข้าบัญชี</label>
                                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group transform transition hover:scale-[1.02]">
                                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
                                    
                                    <div class="flex justify-between items-start mb-6 relative z-10">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-university text-2xl opacity-80"></i>
                                            <span class="font-bold text-lg tracking-wide">Krungthai Bank</span>
                                        </div>
                                        <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-medium">ออมทรัพย์</span>
                                    </div>
                                    
                                    <div class="mb-6 relative z-10">
                                        <p class="text-blue-100 text-xs mb-1">เลขที่บัญชี Account No.</p>
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono text-2xl md:text-3xl font-bold tracking-widest" id="acc-no">517-0-12428-7</span>
                                            <button type="button" onclick="copyToClipboard('5170124287', this)" class="text-white/70 hover:text-white transition p-2 rounded-full hover:bg-white/10" title="คัดลอก">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="relative z-10">
                                        <p class="text-blue-100 text-xs mb-1">ชื่อบัญชี Account Name</p>
                                        <p class="font-medium text-lg truncate">สมาคมนักเรียนเก่าพิชัย</p>
                                    </div>
                                </div>

                            <!-- Payment Summary -->
                            <div class="flex flex-col justify-between">
                                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                                    <h4 class="font-bold text-gray-700 mb-4">สรุปรายการ</h4>
                                    <div id="payment-summary" class="space-y-2 text-sm">
                                        <!-- Will be filled by JS -->
                                    </div>
                                    <div class="border-t border-gray-200 mt-4 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-lg text-gray-800">รวมทั้งหมด</span>
                                            <span class="font-bold text-2xl text-yellow-600"><span id="payment-amount">0</span> บาท</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-700 font-bold mb-3 text-sm">หลักฐานการโอนเงิน (สลิป) <span class="text-red-500">*</span></label>
                            
                            <input type="hidden" name="payment_amount" id="verified_amount">

                            <!-- Payment Date/Time -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2 text-sm">วันที่โอน <span class="text-red-500">*</span></label>
                                    <input type="date" name="payment_date" id="payment_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2 text-sm">เวลาที่โอน <span class="text-red-500">*</span></label>
                                    <input type="time" name="payment_time" id="payment_time" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition bg-gray-50 focus:bg-white">
                                </div>
                            </div>

                            <div id="upload-area" class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:bg-yellow-50 hover:border-yellow-300 transition group cursor-pointer bg-gray-50">
                                <input type="file" name="payment_slip" id="payment_slip" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                <i class="fas fa-cloud-upload-alt text-5xl text-gray-300 group-hover:text-yellow-400 transition mb-4"></i>
                                <p class="text-gray-500 group-hover:text-gray-700 transition">คลิกหรือลากไฟล์มาวางที่นี่</p>
                                <p class="text-sm text-gray-400 mt-2">รองรับ JPG, PNG (ไม่เกิน 5MB)</p>
                            </div>

                            <div id="slip-preview-container" class="hidden mt-6">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-start gap-4">
                                        <img id="slip-preview-img" src="" alt="Preview" class="w-32 h-auto rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-800" id="slip-filename">-</p>
                                            <p class="text-sm text-gray-500" id="slip-filesize">-</p>
                                            <button type="button" id="remove-slip-btn" class="mt-2 text-red-500 hover:text-red-600 text-sm font-bold">
                                                <i class="fas fa-trash mr-1"></i> ลบ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับ
                            </button>
                            <button type="submit" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-yellow-500/30 transition transform hover:-translate-y-1">
                                <i class="fas fa-check mr-2"></i> ยืนยันสั่งซื้อ
                            </button>
                        </div>
                    </div>

                    <!-- Hidden fields for form data (outside of step divs) -->
                    <input type="hidden" name="address" id="full_address">
                </form>
            </div>
        </div>
    </div>

    <!-- Footer (simplified) -->
    <footer class="bg-dark text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400 text-sm">&copy; 2025 Phichai Run. All rights reserved.</p>
        </div>
    </footer>

    <script>
    (function() {
        const form = document.getElementById('shirtOrderForm');
        const steps = Array.from(document.querySelectorAll('.form-step'));
        let currentStep = 0;

        const updateSteps = () => {
            steps.forEach((s, idx) => {
                if (idx === currentStep) {
                    s.classList.remove('hidden');
                } else {
                    s.classList.add('hidden');
                }
            });
            const progress = Math.round(((currentStep + 1) / steps.length) * 100);
            document.getElementById('progressBar').style.width = progress + '%';
        };

        const showError = (msg) => {
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ครบถ้วน',
                text: msg,
                confirmButtonText: 'ตกลง',
                customClass: {
                    popup: 'rounded-3xl shadow-xl border border-gray-100',
                    confirmButton: 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-bold py-3 px-8 rounded-full shadow-lg',
                    title: 'text-2xl font-bold text-secondary font-sans',
                    htmlContainer: 'text-gray-600 font-sans'
                },
                buttonsStyling: false
            });
        };

        const calculateTotal = () => {
            let total = 0;
            let totalQty = 0;
            let sizeString = '';
            let summaryHtml = '';

            // Map size codes to display names
            const sizeDisplayNames = {
                'KS': 'S (เด็ก)',
                'KM': 'M (เด็ก)',
                'KL': 'L (เด็ก)',
                'KXL': 'XL (เด็ก)',
                'K2XL': '2XL (เด็ก)'
            };

            document.querySelectorAll('.shirt-qty-input').forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    const size = input.getAttribute('data-size');
                    const displayName = sizeDisplayNames[size] || size;
                    total += qty * 250;
                    totalQty += qty;
                    sizeString += `${displayName}: ${qty}, `;
                    summaryHtml += `<div class="flex justify-between"><span>${displayName} x ${qty}</span><span>${(qty * 250).toLocaleString()} บาท</span></div>`;
                }
            });

            if (sizeString.length > 0) {
                sizeString = sizeString.slice(0, -2);
            }

            // Add shipping
            const shipping = document.querySelector('input[name="shipping_method"]:checked');
            if (shipping && shipping.value === 'POST') {
                total += 50;
                summaryHtml += `<div class="flex justify-between text-gray-500"><span>ค่าจัดส่ง</span><span>50 บาท</span></div>`;
            }

            document.getElementById('selected-sizes').textContent = sizeString || '-';
            document.getElementById('total-amount').textContent = total.toLocaleString();
            document.getElementById('payment-amount').textContent = total.toLocaleString();
            document.getElementById('verified_amount').value = total;
            document.getElementById('hidden_shirt_sizes').value = sizeString;
            document.getElementById('hidden_shirt_quantity').value = totalQty;
            
            const summaryEl = document.getElementById('payment-summary');
            if (summaryEl) summaryEl.innerHTML = summaryHtml || '<p class="text-gray-400">ยังไม่ได้เลือกเสื้อ</p>';
        };

        // Event listeners
        document.querySelectorAll('.shirt-qty-input').forEach(input => {
            input.addEventListener('change', calculateTotal);
        });

        document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
            radio.addEventListener('change', calculateTotal);
        });

        // Function to build and set full address
        const buildFullAddress = () => {
            const prefixSelect = document.getElementById('prefix_select');
            const prefixCustom = document.getElementById('prefix_custom');
            let prefix = '';
            
            if (prefixSelect.value === 'OTHER') {
                prefix = prefixCustom.value.trim();
            } else {
                prefix = prefixSelect.value || '';
            }
            
            const name = document.getElementById('name_input').value || '';
            document.getElementById('hidden_full_name').value = prefix + name;

            const postal = document.querySelector('input[name="postal_code"]').value || '';
            const subdistrictSelect = document.getElementById('subdistrict_select');
            const subdistrictInput = document.querySelector('input[name="subdistrict"]');
            const subdistrict = subdistrictSelect ? subdistrictSelect.value : (subdistrictInput ? subdistrictInput.value : '');
            const districtProvince = document.querySelector('input[name="district_province"]').value || '';
            const detail = document.querySelector('textarea[name="address_detail"]').value || '';

            let fullAddress = detail;
            if (subdistrict) fullAddress += ' ต.' + subdistrict;
            if (districtProvince) fullAddress += ' ' + districtProvince;
            if (postal) fullAddress += ' ' + postal;

            document.getElementById('full_address').value = fullAddress.trim();
            
            return fullAddress.trim();
        };

        document.addEventListener('click', (e) => {
            if (e.target.matches('.next-btn')) {
                const currentFields = steps[currentStep].querySelectorAll('[required]');
                let isValid = true;

                for (const f of currentFields) {
                    if (!f.value) {
                        let labelText = 'ข้อมูล';
                        const label = f.closest('div')?.querySelector('label');
                        if (label) labelText = label.innerText.replace('*', '').trim();
                        showError('กรุณากรอก ' + labelText);
                        f.focus();
                        isValid = false;
                        break;
                    }
                }

                if (!isValid) return;

                // Step 1 completed - build address before moving to step 2
                if (currentStep === 0) {
                    buildFullAddress();
                }

                // Step 2 validation: at least one shirt
                if (currentStep === 1) {
                    let totalQty = 0;
                    document.querySelectorAll('.shirt-qty-input').forEach(input => {
                        totalQty += parseInt(input.value) || 0;
                    });
                    if (totalQty === 0) {
                        showError('กรุณาเลือกจำนวนเสื้ออย่างน้อย 1 ตัว');
                        return;
                    }
                }

                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateSteps();
                    calculateTotal();
                }
            }

            if (e.target.matches('.prev-btn')) {
                if (currentStep > 0) {
                    currentStep--;
                    updateSteps();
                }
            }
        });

        // Compose full address before submit
        let isSubmitting = false;
        form.addEventListener('submit', (e) => {
            if (isSubmitting) return; // Already submitting, don't prevent
            
            e.preventDefault(); // Prevent immediate submit
            
            // Ensure address is built before submission
            buildFullAddress();
            
            // Submit form
            isSubmitting = true;
            form.submit();
        });

        // Handle prefix "อื่นๆ" selection
        (function() {
            const prefixSelect = document.getElementById('prefix_select');
            const prefixCustom = document.getElementById('prefix_custom');
            
            if (prefixSelect && prefixCustom) {
                prefixSelect.addEventListener('change', function() {
                    if (this.value === 'OTHER') {
                        prefixCustom.classList.remove('hidden');
                        prefixCustom.required = true;
                        prefixCustom.focus();
                    } else {
                        prefixCustom.classList.add('hidden');
                        prefixCustom.required = false;
                        prefixCustom.value = '';
                    }
                });
            }
        })();

        // ===== POSTAL CODE AUTOFILL =====
        (function() {
            const postalInput = document.getElementById('postal_code');
            let subdistrictInput = document.getElementById('subdistrict');
            const districtProvinceInput = document.getElementById('district_province');
            const postalHelp = document.getElementById('postal_help');

            if (!postalInput) return;

            let thaiPostalMap = null;

            async function loadLocalPostal() {
                if (thaiPostalMap !== null) return;
                try {
                    const r = await fetch('assets/data/th_postal_codes.json');
                    if (!r.ok) throw new Error('fetch-failed');
                    const data = await r.json();
                    const map = {};
                    if (Array.isArray(data)) {
                        data.forEach(item => {
                            if (!item || !item.zipcode) return;
                            const code = String(item.zipcode).padStart(5, '0');
                            const entry = {
                                subdistrict: (item.district || '').trim(),
                                district: (item.amphoe || '').trim(),
                                province: (item.province || '').trim()
                            };
                            if (!map[code]) map[code] = [];
                            map[code].push(entry);
                        });

                        Object.keys(map).forEach(code => {
                            const seen = new Set();
                            const list = [];
                            map[code].forEach(it => {
                                const key = [it.subdistrict, it.district, it.province].join('|');
                                if (!seen.has(key)) {
                                    seen.add(key);
                                    list.push(it);
                                }
                            });
                            list.sort((a,b) => {
                                if (a.province !== b.province) return a.province.localeCompare(b.province);
                                if (a.district !== b.district) return a.district.localeCompare(b.district);
                                return a.subdistrict.localeCompare(b.subdistrict);
                            });
                            map[code] = list;
                        });
                    }
                    thaiPostalMap = map;
                } catch (e) {
                    console.warn('Failed to load local Thai postal map', e);
                    thaiPostalMap = {};
                }
            }

            async function lookupPostal(postal) {
                if (!/^[0-9]{5}$/.test(postal)) {
                    if (postalHelp) postalHelp.textContent = 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก';
                    subdistrictInput.value = '';
                    districtProvinceInput.value = '';
                    const existingSelect = document.getElementById('subdistrict_select');
                    if (existingSelect) {
                        existingSelect.remove();
                        subdistrictInput.name = 'subdistrict';
                        subdistrictInput.classList.remove('hidden');
                    }
                    subdistrictInput.readOnly = false;
                    districtProvinceInput.readOnly = false;
                    subdistrictInput.classList.remove('bg-gray-100');
                    districtProvinceInput.classList.remove('bg-gray-100');
                    return;
                }

                if (postalHelp) postalHelp.textContent = 'กำลังค้นหาข้อมูลที่อยู่...';

                let entries = null;
                try {
                    const cached = sessionStorage.getItem('postal_' + postal);
                    if (cached) entries = JSON.parse(cached);
                } catch (e) {}

                if (!entries) {
                    if (thaiPostalMap === null) {
                        await loadLocalPostal();
                    }
                    if (thaiPostalMap && thaiPostalMap[postal]) {
                        entries = thaiPostalMap[postal];
                        try { sessionStorage.setItem('postal_' + postal, JSON.stringify(entries)); } catch(e){}
                    }
                }

                if (entries) {
                    if (Array.isArray(entries) && entries.length > 1) {
                        const existingSelect = document.getElementById('subdistrict_select');
                        if (existingSelect) existingSelect.remove();
                        const existingContainer = document.getElementById('subdistrict_container');
                        if (existingContainer) existingContainer.remove();

                        const sel = document.createElement('select');
                        sel.id = 'subdistrict_select';
                        sel.name = 'subdistrict';
                        sel.className = subdistrictInput.className + ' mt-1';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = 'เลือกตำบล (พบ ' + entries.length + ' รายการ)';
                        sel.appendChild(placeholder);

                        const seen = new Set();
                        entries.forEach((it) => {
                            const name = (it.subdistrict || '').trim();
                            if (!name || seen.has(name)) return;
                            seen.add(name);
                            const opt = document.createElement('option');
                            opt.value = name;
                            opt.textContent = name + ' — ' + (it.district || '') + (it.province ? (' / ' + it.province) : '');
                            opt.dataset.amphoe = it.district || '';
                            opt.dataset.province = it.province || '';
                            sel.appendChild(opt);
                        });

                        const container = document.createElement('div');
                        container.id = 'subdistrict_container';
                        container.className = 'mt-1';
                        container.appendChild(sel);
                        subdistrictInput.parentNode.insertBefore(container, subdistrictInput.nextSibling);
                        subdistrictInput.name = '';
                        subdistrictInput.classList.add('hidden');

                        if (window.jQuery && typeof jQuery().select2 === 'function') {
                            if (jQuery('#subdistrict_select').data('select2')) {
                                jQuery('#subdistrict_select').select2('destroy');
                            }
                            jQuery(sel).select2({
                                placeholder: 'เลือกตำบล (พิมพ์เพื่อค้น)',
                                width: '100%',
                                allowClear: true,
                                language: {
                                    noResults: function() { return 'ไม่พบผลลัพธ์'; },
                                    searching: function() { return 'กำลังค้นหา...'; }
                                }
                            });

                            jQuery(sel).on('change', function() {
                                const o = this.options[this.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        } else {
                            sel.addEventListener('change', (e) => {
                                const o = sel.options[sel.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        }

                        if (postalHelp) postalHelp.textContent = 'พบหลายตำบล — โปรดเลือกตำบลที่ถูกต้อง';
                        return;
                    }

                    const p = Array.isArray(entries) ? entries[0] : entries;
                    const existingSelect2 = document.getElementById('subdistrict_select');
                    const existingContainer2 = document.getElementById('subdistrict_container');
                    if (existingSelect2) {
                        try {
                            if (window.jQuery && jQuery(existingSelect2).data('select2')) {
                                jQuery(existingSelect2).select2('destroy');
                            }
                        } catch (e) {}
                        if (existingContainer2) existingContainer2.remove();
                        subdistrictInput.classList.remove('hidden');
                        subdistrictInput.name = 'subdistrict';
                    }

                    subdistrictInput.value = p.subdistrict || '';
                    districtProvinceInput.value = ((p.district || '') + (p.province ? (' / ' + p.province) : '')).trim();
                    subdistrictInput.readOnly = true;
                    districtProvinceInput.readOnly = true;
                    subdistrictInput.classList.add('bg-gray-100');
                    districtProvinceInput.classList.add('bg-gray-100');
                    if (postalHelp) postalHelp.textContent = 'พบข้อมูลที่อยู่ — ตรวจสอบความถูกต้อง';
                    return;
                }

                // Not found - allow manual entry
                if (postalHelp) postalHelp.textContent = 'ไม่พบข้อมูล — กรุณากรอกที่อยู่ด้วยตนเอง';
                subdistrictInput.readOnly = false;
                districtProvinceInput.readOnly = false;
                subdistrictInput.classList.remove('bg-gray-100');
                districtProvinceInput.classList.remove('bg-gray-100');
            }

            let debounceTimer = null;
            postalInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    lookupPostal(this.value.trim());
                }, 400);
            });

            // Initial check if value exists
            if (postalInput.value.trim().length === 5) {
                lookupPostal(postalInput.value.trim());
            }
        })();

        // File upload preview
        const slipInput = document.getElementById('payment_slip');
        const uploadArea = document.getElementById('upload-area');
        const previewContainer = document.getElementById('slip-preview-container');
        const previewImg = document.getElementById('slip-preview-img');
        const filenameDisplay = document.getElementById('slip-filename');
        const filesizeDisplay = document.getElementById('slip-filesize');
        const removeBtn = document.getElementById('remove-slip-btn');

        if (slipInput) {
            slipInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        previewImg.src = ev.target.result;
                        filenameDisplay.textContent = file.name;
                        filesizeDisplay.textContent = (file.size / 1024).toFixed(1) + ' KB';
                        uploadArea.classList.add('hidden');
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                slipInput.value = '';
                uploadArea.classList.remove('hidden');
                previewContainer.classList.add('hidden');
            });
        }

        updateSteps();
        calculateTotal();

        // Set default payment date/time to now
        (function() {
            const now = new Date();
            const dateInput = document.getElementById('payment_date');
            const timeInput = document.getElementById('payment_time');
            
            if (dateInput && !dateInput.value) {
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                dateInput.value = `${year}-${month}-${day}`;
            }
            
            if (timeInput && !timeInput.value) {
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                timeInput.value = `${hours}:${minutes}`;
            }
        })();

        // Adjust quantity function
        window.adjustQty = (size, delta) => {
            const input = document.querySelector(`input[data-size="${size}"]`);
            if (input) {
                let val = parseInt(input.value) || 0;
                val += delta;
                if (val < 0) val = 0;
                input.value = val;
                input.dispatchEvent(new Event('change'));
            }
        };

        window.copyToClipboard = (text, btn) => {
            navigator.clipboard.writeText(text).then(() => {
                const originalIcon = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-green-300"></i>';
                setTimeout(() => { btn.innerHTML = originalIcon; }, 2000);
            });
        };
    })();
    </script>
</body>
</html>
