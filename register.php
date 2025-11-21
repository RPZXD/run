<?php
require_once 'app/config/database.php';
require_once 'app/models/Registration.php';
require_once 'app/controllers/RegisterController.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();
    
    if ($db) {
        $controller = new RegisterController($db);
        $result = $controller->register();
        $message = $result['message'];
        $status = $result['status'];
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
    <title>ลงทะเบียน - Phichai Run 2026</title>
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
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'floating 3s ease-in-out infinite',
                    },
                    keyframes: {
                        floating: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <!-- jQuery + Select2 for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Tidy Select2 results as compact card-like rows using Tailwind-compatible classes */
        .select2-container .select2-results__option .select2-result-item { padding: .5rem; border-radius: .375rem; }
        .select2-container--open .select2-results__option.select2-results__option--highlighted .select2-result-item { background: rgba(227,6,19,0.06); }
        .select2-container .select2-selection__rendered { line-height: 1.2; }
        .select2-container .select2-selection__choice { background: #fee2e2; border-color: #fecaca; }
        .select2-container .select2-results__option .text-sm { font-size: .85rem; color: #6b7280; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></style>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="antialiased text-gray-800 bg-light overflow-x-hidden">

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

    <div class="relative min-h-screen pt-32 pb-20 flex items-center justify-center overflow-hidden hero-gradient">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-accent/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
        </div>

        <div class="container mx-auto px-4 max-w-3xl relative z-10">
            
            <?php if ($message): ?>
                <div class="mb-8 p-4 rounded-2xl shadow-lg backdrop-blur-md <?php echo $status === 'success' ? 'bg-green-100/90 text-green-800 border border-green-200' : 'bg-red-100/90 text-red-800 border border-red-200'; ?> text-center animate-fade-in-up visible">
                    <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl mr-2"></i>
                    <span class="font-bold"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up visible">
                <div class="bg-gradient-to-r from-primary to-secondary p-8 text-center text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-2 relative z-10">ลงทะเบียนเข้าร่วมงาน</h2>
                    <p class="opacity-90 text-lg relative z-10">กรอกข้อมูลให้ครบถ้วนเพื่อสมัครวิ่ง</p>
                </div>
                
                <form id="regForm" action="register.php" method="POST" enctype="multipart/form-data" class="p-8 md:p-10">
                    <!-- Progress / Steps indicator -->
                    <div class="mb-10">
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                            <div id="progressBar" class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-all duration-500 ease-out" style="width:20%"></div>
                        </div>
                        <div class="flex justify-between text-sm mt-3 text-gray-500 font-medium">
                            <div class="text-primary">ข้อมูลส่วนตัว</div>
                            <div>ติดต่อ</div>
                            <div>การสมัคร</div>
                            <div>เพิ่มเติม</div>
                            <div>ชำระเงิน</div>
                        </div>
                    </div>

                    <!-- Hidden compatibility fields -->
                    <input type="hidden" name="full_name" id="hidden_full_name" value="">
                    <input type="hidden" name="bib_name" id="hidden_bib_name" value="">
                    <input type="hidden" name="emergency_contact_name" id="hidden_emg_name" value="">
                    <input type="hidden" name="emergency_contact_phone" id="hidden_emg_phone" value="">

                    <!-- STEP 1: Personal -->
                    <div class="form-step" data-step="1">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-user-circle text-primary"></i> ข้อมูลส่วนตัว
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">คำนำหน้า</label>
                                <select name="prefix" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="ด.ช.">ด.ช.</option>
                                    <option value="ด.ญ.">ด.ญ.</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">ชื่อ (ภาษาไทย)</label>
                                <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="รักดี">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">นามสกุล (ภาษาไทย)</label>
                                <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="มีชัย">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">เพศ</label>
                                <div class="flex gap-4">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="gender" value="Male" required class="peer sr-only">
                                        <div class="text-center py-3 px-4 rounded-xl border border-gray-300 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary transition hover:bg-gray-50">
                                            <i class="fas fa-male mr-2"></i> ชาย
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="gender" value="Female" required class="peer sr-only">
                                        <div class="text-center py-3 px-4 rounded-xl border border-gray-300 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary transition hover:bg-gray-50">
                                            <i class="fas fa-female mr-2"></i> หญิง
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">วันเกิด</label>
                                <input type="date" name="birth_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="button" class="next-btn bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-secondary text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Contact/Address -->
                    <div class="form-step hidden" data-step="2">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-address-card text-primary"></i> ข้อมูลติดต่อ
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="08x-xxx-xxxx">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">อีเมล</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="example@email.com">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-700 font-bold mb-2 text-sm">ที่อยู่จัดส่งเอกสาร</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <input type="text" id="postal_code" name="postal_code" placeholder="รหัสไปรษณีย์" maxlength="5" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                                <input type="text" id="subdistrict" name="subdistrict" placeholder="ตำบล/แขวง" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                                <input type="text" id="district_province" name="district_province" placeholder="อำเภอ / จังหวัด" readonly class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed">
                            </div>
                            <p id="postal_help" class="text-xs text-primary mt-1 mb-3"></p>
                            <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="บ้านเลขที่ หมู่บ้าน ซอย ถนน..."></textarea>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ก่อนหน้า
                            </button>
                            <button type="button" class="next-btn bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-secondary text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>



                    <!-- STEP 3: Race Info -->
                    <div class="form-step hidden" data-step="3">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-running text-primary"></i> ข้อมูลการแข่งขัน
                        </h3>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-4 text-sm">ประเภทการสมัคร</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="category" value="Student Run 3.5KM" required class="peer sr-only">
                                    <div class="p-6 rounded-2xl border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition h-full flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3 text-xl">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <span class="font-bold text-lg text-gray-800">Student Run</span>
                                        <span class="text-sm text-gray-500">3.5 km</span>
                                    </div>
                                    <div class="absolute top-3 right-3 text-primary opacity-0 peer-checked:opacity-100 transition">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="category" value="Charity Walk 3.5KM" required class="peer sr-only">
                                    <div class="p-6 rounded-2xl border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition h-full flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-3 text-xl">
                                            <i class="fas fa-hand-holding-heart"></i>
                                        </div>
                                        <span class="font-bold text-lg text-gray-800">Charity Walk</span>
                                        <span class="text-sm text-gray-500">3.5 km</span>
                                    </div>
                                    <div class="absolute top-3 right-3 text-primary opacity-0 peer-checked:opacity-100 transition">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="category" value="Fun Run 5KM" required class="peer sr-only">
                                    <div class="p-6 rounded-2xl border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition h-full flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-3 text-xl">
                                            <i class="fas fa-smile"></i>
                                        </div>
                                        <span class="font-bold text-lg text-gray-800">Fun Run</span>
                                        <span class="text-sm text-gray-500">5 km</span>
                                    </div>
                                    <div class="absolute top-3 right-3 text-primary opacity-0 peer-checked:opacity-100 transition">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="category" value="Mini Marathon 11.5KM" required class="peer sr-only">
                                    <div class="p-6 rounded-2xl border-2 border-gray-200 peer-checked:border-primary peer-checked:bg-primary/5 hover:border-primary/50 transition h-full flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-3 text-xl">
                                            <i class="fas fa-fire"></i>
                                        </div>
                                        <span class="font-bold text-lg text-gray-800">Mini Marathon</span>
                                        <span class="text-sm text-gray-500">11.5 km</span>
                                    </div>
                                    <div class="absolute top-3 right-3 text-primary opacity-0 peer-checked:opacity-100 transition">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Placeholder for conditional fields depending on category -->
                        <div id="category-conditional" class="mt-4">
                            <!-- Conditions will be injected here later -->
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ก่อนหน้า
                            </button>
                            <button type="button" class="next-btn bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-secondary text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: Shirt / Extras -->
                    <div class="form-step hidden" data-step="4">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-tshirt text-primary"></i> ไซส์เสื้อ / รายการพิเศษ
                        </h3>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-4 text-sm">ไซส์เสื้อ</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="XS" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">XS</span>
                                        <span class="text-xs opacity-75">34"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="S" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">S</span>
                                        <span class="text-xs opacity-75">36"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="M" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">M</span>
                                        <span class="text-xs opacity-75">38"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="L" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">L</span>
                                        <span class="text-xs opacity-75">40"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="XL" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">XL</span>
                                        <span class="text-xs opacity-75">42"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="2XL" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">2XL</span>
                                        <span class="text-xs opacity-75">44"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="3XL" required class="peer sr-only">
                                    <div class="py-3 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">3XL</span>
                                        <span class="text-xs opacity-75">46"</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ก่อนหน้า
                            </button>
                            <button type="button" class="next-btn bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-secondary text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1">
                                ถัดไป <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 5: Payment -->
                    <div class="form-step hidden" data-step="5">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-primary"></i> หลักฐานการชำระเงิน
                        </h3>
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 mb-6 flex flex-col md:flex-row items-center gap-6">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl flex-shrink-0">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-lg">โอนเงินเข้าบัญชี: ธนาคารกสิกรไทย</p>
                                <p class="text-gray-600">ชื่อบัญชี: <span class="font-semibold text-primary">Phichai Run 2026</span></p>
                                <p class="text-gray-600">เลขที่บัญชี: <span class="font-mono bg-white px-2 py-1 rounded border border-gray-200 ml-1">123-4-56789-0</span></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2 text-sm">แนบสลิปโอนเงิน</label>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:bg-gray-50 transition group">
                                <input type="file" name="payment_slip" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="text-gray-400 group-hover:text-primary transition">
                                    <i class="fas fa-cloud-upload-alt text-4xl mb-3"></i>
                                    <p class="font-medium">คลิกเพื่ออัพโหลดสลิป</p>
                                    <p class="text-xs mt-1">รองรับไฟล์ .jpg, .jpeg, .png</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 font-bold py-3 px-8 rounded-full transition">
                                <i class="fas fa-arrow-left mr-2"></i> ก่อนหน้า
                            </button>
                            <button type="submit" class="bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-secondary text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1">
                                <i class="fas fa-check-circle mr-2"></i> ยืนยันการสมัคร
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="contact" class="bg-dark text-white pt-20 pb-10 relative overflow-hidden mt-20">
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-4 mb-6">
                        <img src="assets/images/logo-1.png" class="h-16 w-16 rounded-full border-2 border-white/20">
                        <div>
                            <h3 class="text-2xl font-bold text-white">PHICHAI RUN 2026</h3>
                            <p class="text-gray-400 text-sm">วิ่งพิชัยดาบหัก</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-8 max-w-md leading-relaxed">
                        งานวิ่งการกุศลเพื่อสุขภาพและสังคม จัดโดยชมรมวิ่งอำเภอพิชัย ร่วมกับเทศบาลตำบลพิชัย มาร่วมเป็นส่วนหนึ่งของประวัติศาสตร์หน้าใหม่
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
                        <li><a href="index.php#home" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> หน้าแรก</a></li>
                        <li><a href="index.php#about" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> รายละเอียด</a></li>
                        <li><a href="index.php#categories" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> ประเภทการแข่งขัน</a></li>
                        <li><a href="index.php#schedule" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> กำหนดการ</a></li>
                        <li><a href="register.php" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> สมัครวิ่ง</a></li>
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

            <!-- Sponsors -->
            <div class="border-t border-white/10 pt-8 pb-8">
                <p class="text-center text-gray-500 text-sm mb-6">ผู้สนับสนุนหลัก</p>
                <div class="flex flex-wrap justify-center gap-8 opacity-50 grayscale hover:grayscale-0 transition duration-500">
                    <img src="assets/images/logo-2.jpeg" class="h-12 object-contain">
                    <img src="assets/images/logo-3.jpeg" class="h-12 object-contain">
                    <img src="assets/images/logo-4.jpeg" class="h-12 object-contain">
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

    <script>
        // Simple postal code lookup: tries zippopotam.us and handles failures gracefully.
        // If you prefer a different Thai postal API, replace the URL below.
        (function() {
            const postalInput = document.getElementById('postal_code');
            let subdistrictInput = document.getElementById('subdistrict'); // may remain input or be used alongside a dynamic select
            const districtProvinceInput = document.getElementById('district_province');
            const postalHelp = document.getElementById('postal_help');

            if (!postalInput) return;

            // lazy-load local Thai postal code map (used first)
            // the JSON file in assets/data is an array of objects; we'll fetch it only on first lookup and convert into a map keyed by 5-digit zipcode
            let thaiPostalMap = null; // will be { '10100': [ { subdistrict, district, province }, ... ], ... }
            async function loadLocalPostal() {
                if (thaiPostalMap !== null) return; // already loaded or attempted
                try {
                    const r = await fetch('assets/data/th_postal_codes.json');
                    if (!r.ok) throw new Error('fetch-failed');
                    const data = await r.json();
                    const map = {};
                    if (Array.isArray(data)) {
                        // build arrays of entries per zipcode
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

                        // dedupe and sort each list: unique by subdistrict|district|province, sort by province->district->subdistrict
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
                    } else if (typeof data === 'object' && data !== null) {
                        // if already a map of arrays, normalize values to arrays
                        Object.keys(data).forEach(k => {
                            const v = data[k];
                            if (Array.isArray(v)) map[k] = v.map(it => ({ subdistrict: (it.subdistrict||'').trim(), district: (it.district||'').trim(), province: (it.province||'').trim() }));
                        });
                    }
                    thaiPostalMap = map;
                } catch (e) {
                    console.warn('Failed to load local Thai postal map', e);
                    thaiPostalMap = {}; // avoid retrying repeatedly in same session
                }
            }

            async function lookupPostal(postal) {
                // basic validation
                if (!/^[0-9]{5}$/.test(postal)) {
                    if (postalHelp) postalHelp.textContent = 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก';
                    subdistrictInput.value = '';
                    districtProvinceInput.value = '';
                    // allow manual input
                    // if a dynamic select exists, remove it and restore input
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

                // 1) Try local Thai postal map (lazy load if needed)
                // first check sessionStorage cache for this postal specifically
                let entries = null;
                try {
                    const cached = sessionStorage.getItem('postal_' + postal);
                    if (cached) entries = JSON.parse(cached);
                } catch (e) {
                    // ignore storage errors
                }

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
                    // entries is an array of { subdistrict, district, province }
                    if (Array.isArray(entries) && entries.length > 1) {
                        // create a select so user can choose correct subdistrict
                        // remove any previous select
                        const existingSelect = document.getElementById('subdistrict_select');
                        if (existingSelect) existingSelect.remove();

                        // create select element
                        const sel = document.createElement('select');
                        sel.id = 'subdistrict_select';
                        sel.name = 'subdistrict';
                        sel.className = subdistrictInput.className + ' mt-1';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = 'เลือกตำบล (ตำแหน่งที่พบ ' + entries.length + ' รายการ)';
                        sel.appendChild(placeholder);

                        // keep a Set to avoid duplicates
                        const seen = new Set();
                        entries.forEach((it) => {
                            const name = (it.subdistrict || '').trim();
                            if (!name || seen.has(name)) return;
                            seen.add(name);
                            const opt = document.createElement('option');
                            opt.value = name;
                            opt.textContent = name + ' — ' + (it.district || '') + (it.province ? (' / ' + it.province) : '');
                            // encode amphoe/province data on option for later
                            opt.dataset.amphoe = it.district || '';
                            opt.dataset.province = it.province || '';
                            sel.appendChild(opt);
                        });

                        // container: insert after the original input; we'll initialize Select2 for searchable dropdown
                        const container = document.createElement('div');
                        container.id = 'subdistrict_container';
                        container.className = 'mt-1';
                        container.appendChild(sel);
                        subdistrictInput.parentNode.insertBefore(container, subdistrictInput.nextSibling);
                        // remove name from original input so form posts the select value
                        subdistrictInput.name = '';
                        // hide original input visually but keep it in DOM
                        subdistrictInput.classList.add('hidden');

                        // initialize Select2 if available
                        if (window.jQuery && typeof jQuery().select2 === 'function') {
                            // destroy previous instance if any
                            if (window.jQuery && jQuery('#subdistrict_select').data('select2')) {
                                jQuery('#subdistrict_select').select2('destroy');
                            }
                            jQuery(sel).select2({
                                placeholder: 'เลือกตำบล (พิมพ์เพื่อค้น)',
                                width: '100%',
                                allowClear: true,
                                language: {
                                    noResults: function() { return 'ไม่พบผลลัพธ์'; },
                                    searching: function() { return 'กำลังค้นหา...'; }
                                },
                                escapeMarkup: function(markup) { return markup; }, // allow custom HTML
                                templateResult: function(data) {
                                    if (!data.id) return data.text; // placeholder or empty
                                    const el = data.element;
                                    const amphoe = el ? (el.dataset.amphoe || '') : '';
                                    const province = el ? (el.dataset.province || '') : '';
                                    const sub = data.text || '';
                                    const extra = (amphoe || province) ? ('<div class="text-sm text-gray-500">' + (amphoe ? amphoe : '') + (province ? (' / ' + province) : '') + '</div>') : '';
                                    return '<div class="select2-result-item"><div class="font-medium">' + sub + '</div>' + extra + '</div>';
                                },
                                templateSelection: function(data) {
                                    if (!data.id) return data.text;
                                    // show subdistrict only (keeps selection compact), append amphoe/province lightly
                                    const el = data.element;
                                    const amphoe = el ? (el.dataset.amphoe || '') : '';
                                    const province = el ? (el.dataset.province || '') : '';
                                    const sub = data.text || '';
                                    if (amphoe || province) return sub + ' — ' + amphoe + (province ? (' / ' + province) : '');
                                    return sub;
                                }
                            });

                            // when selection changes via select2, update district/province
                            jQuery(sel).on('change', function() {
                                const o = this.options[this.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        } else {
                            // fallback: basic select change handler
                            sel.addEventListener('change', (e) => {
                                const o = sel.options[sel.selectedIndex];
                                const amphoe = o && o.dataset ? o.dataset.amphoe : '';
                                const province = o && o.dataset ? o.dataset.province : '';
                                districtProvinceInput.value = ((amphoe || '') + (province ? (' / ' + province) : '')).trim();
                                districtProvinceInput.readOnly = true;
                                districtProvinceInput.classList.add('bg-gray-100');
                            });
                        }

                        if (postalHelp) postalHelp.textContent = 'พบหลายตำบลสำหรับรหัสนี้ — โปรดเลือกตำบลที่ถูกต้อง (พิมพ์เพื่อค้น)';
                        return;
                    }

                    // single entry: use the input field and populate read-only
                    const p = Array.isArray(entries) ? entries[0] : entries;
                    // remove any dynamic select if present
                    const existingSelect2 = document.getElementById('subdistrict_select');
                    const existingContainer = document.getElementById('subdistrict_container');
                    if (existingSelect2) {
                        // if select2 instance exists, destroy it first
                        try {
                            if (window.jQuery && jQuery(existingSelect2).data('select2')) {
                                jQuery(existingSelect2).select2('destroy');
                            }
                        } catch (e) {}
                        if (existingContainer) existingContainer.remove();
                        subdistrictInput.classList.remove('hidden');
                        subdistrictInput.name = 'subdistrict';
                    }

                    subdistrictInput.value = p.subdistrict || '';
                    districtProvinceInput.value = ((p.district || '') + (p.province ? (' / ' + p.province) : '')).trim();
                    subdistrictInput.readOnly = true;
                    districtProvinceInput.readOnly = true;
                    subdistrictInput.classList.add('bg-gray-100');
                    districtProvinceInput.classList.add('bg-gray-100');
                    if (postalHelp) postalHelp.textContent = 'พบข้อมูลจากฐานข้อมูลภายใน — ตรวจสอบความถูกต้องก่อนส่ง';
                    return;
                }

                // 2) Fallback to public API (may not cover all Thai codes) - keep for extra coverage
                try {
                    const resp = await fetch('https://api.zippopotam.us/th/' + postal);
                    if (!resp.ok) throw new Error('not-found');
                    const data = await resp.json();
                    console.debug('postal lookup data (external):', data);
                    if (data && data.places && data.places.length) {
                        const place = data.places[0];
                        subdistrictInput.value = place['place name'] || '';
                        districtProvinceInput.value = (place['state'] ? place['state'] : '') + (place['county'] ? (' / ' + place['county']) : '');
                        subdistrictInput.readOnly = true;
                        districtProvinceInput.readOnly = true;
                        subdistrictInput.classList.add('bg-gray-100');
                        districtProvinceInput.classList.add('bg-gray-100');
                        if (postalHelp) postalHelp.textContent = 'พบข้อมูลจากบริการภายนอก — ตรวจสอบความถูกต้องก่อนส่ง';
                        return;
                    }
                    throw new Error('no-data');
                } catch (err) {
                    console.warn('postal lookup failed:', err);
                    // fallback: allow manual input and show message
                    subdistrictInput.value = '';
                    districtProvinceInput.value = '';
                    subdistrictInput.readOnly = false;
                    districtProvinceInput.readOnly = false;
                    subdistrictInput.classList.remove('bg-gray-100');
                    districtProvinceInput.classList.remove('bg-gray-100');
                    if (postalHelp) postalHelp.textContent = 'ไม่พบข้อมูลอัตโนมัติสำหรับรหัสนี้ — กรุณากรอก ตำบล/อำเภอ/จังหวัด ด้วยตนเอง';
                }
            }

            let timeout = null;
            postalInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const v = e.target.value.trim();
                    if (v.length === 5) {
                        lookupPostal(v);
                    } else {
                        subdistrictInput.value = '';
                        districtProvinceInput.value = '';
                        postalHelp.textContent = 'รหัสไปรษณีย์ต้องเป็นตัวเลข 5 หลัก';
                    }
                }, 500);
            });

            // calculate age from birth_date and compose hidden full_name before submit to keep backend compatibility
            const birthInput = document.querySelector('input[name="birth_date"]');
            const ageInput = document.querySelector('input[name="age"]');
            if (birthInput && ageInput) {
                const calcAge = (dob) => {
                    const today = new Date();
                    const birth = new Date(dob);
                    if (isNaN(birth)) return '';
                    let age = today.getFullYear() - birth.getFullYear();
                    const m = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                        age--;
                    }
                    return age;
                };

                birthInput.addEventListener('change', (e) => {
                    const v = e.target.value;
                    const a = calcAge(v);
                    if (a !== '' && !isNaN(a)) {
                        ageInput.value = a;
                    }
                });
            }

            // compose hidden full_name before submit to keep backend compatibility
            const form = document.getElementById('regForm');
            if (form) {
                form.addEventListener('submit', (ev) => {
                    const prefix = (document.querySelector('select[name="prefix"]') || {}).value || '';
                    const first = (document.querySelector('input[name="first_name"]') || {}).value || '';
                    const last = (document.querySelector('input[name="last_name"]') || {}).value || '';
                    const hiddenFull = document.getElementById('hidden_full_name');
                    if (hiddenFull) hiddenFull.value = [prefix, first, last].filter(Boolean).join(' ').trim();

                    // ensure hidden bib_name exists (left empty)
                    const hiddenBib = document.getElementById('hidden_bib_name');
                    if (hiddenBib && !hiddenBib.value) hiddenBib.value = '';

                    // also set hidden emergency contact fields (kept empty)
                    const hiddenEmgName = document.getElementById('hidden_emg_name');
                    const hiddenEmgPhone = document.getElementById('hidden_emg_phone');
                    if (hiddenEmgName) hiddenEmgName.value = '';
                    if (hiddenEmgPhone) hiddenEmgPhone.value = '';
                });

                // Stepper logic
                const steps = Array.from(document.querySelectorAll('.form-step'));
                let currentStep = 0; // 0-based index

                const updateSteps = () => {
                    steps.forEach((s, idx) => {
                        if (idx === currentStep) {
                            s.classList.remove('hidden');
                        } else {
                            s.classList.add('hidden');
                        }
                    });
                    const progress = Math.round(((currentStep + 1) / steps.length) * 100);
                    const progressBar = document.getElementById('progressBar');
                    if (progressBar) progressBar.style.width = progress + '%';
                };

                // attach next/prev handlers
                document.addEventListener('click', (e) => {
                    if (e.target.matches('.next-btn')) {
                        // basic validation for current step: required fields
                        const currentFields = steps[currentStep].querySelectorAll('[required]');
                        for (const f of currentFields) {
                            if (!f.value) {
                                f.focus();
                                return; // stop advancing
                            }
                        }
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateSteps();
                        }
                    }
                    if (e.target.matches('.prev-btn')) {
                        if (currentStep > 0) {
                            currentStep--;
                            updateSteps();
                        }
                    }
                });

                // category change placeholder for conditional fields
                const categorySelect = document.querySelector('select[name="category"]');
                const conditionalDiv = document.getElementById('category-conditional');
                if (categorySelect && conditionalDiv) {
                    categorySelect.addEventListener('change', (ev) => {
                        // clear
                        conditionalDiv.innerHTML = '';
                        const v = ev.target.value;
                        // placeholder logic: inject example fields depending on selection
                        if (v === 'Student Run 3.5KM') {
                            conditionalDiv.innerHTML = '<p class="text-sm text-gray-600">กรุณาอัพโหลดสำเนาบัตรนักเรียน (ถ้ามี)</p><input type="file" name="student_id_file" accept="image/*" class="mt-2">';
                        } else if (v === 'Merch Shirt 250THB') {
                            conditionalDiv.innerHTML = '<p class="text-sm text-gray-600">คุณกำลังสั่งซื้อเสื้อที่ระลึก</p>';
                        }
                    });
                }

                // initialize
                updateSteps();
            }
        })();
    </script>
</body>
</html>
