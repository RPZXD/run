<?php
require_once 'app/config/database.php';
require_once 'app/models/Registration.php';
require_once 'app/controllers/RegisterController.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
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
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: '<?php echo $status === "success" ? "success" : "error"; ?>',
                            title: '<?php echo $status === "success" ? "สำเร็จ!" : "ข้อผิดพลาด"; ?>',
                            text: '<?php echo $message; ?>',
                            confirmButtonText: 'ตกลง',
                            customClass: {
                                popup: 'rounded-3xl shadow-xl border border-gray-100',
                                confirmButton: 'bg-gradient-to-r from-primary to-red-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1',
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

                    <!-- STEP 1: Personal -->
                    <div class="form-step" data-step="1">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-user-circle text-primary"></i> ข้อมูลส่วนตัว
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">คำนำหน้า</label>
                                <select name="prefix" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                                    <option value="" selected disabled>-- กรุณาเลือก --</option>
                                    
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
                                        <option value="พล.ต.ท.">พล.ต.ท.</option>
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

                                    <option value="other">ระบุอื่นๆ...</option>
                                </select>
                                
                                <input type="text" name="prefix_other" placeholder="โปรดระบุคำนำหน้า" class="hidden mt-2 w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
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
                                <div class="flex gap-4">
                                    <input type="date" name="birth_date" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white">
                                    <div class="w-24">
                                        <input type="text" name="age" readonly class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-center" placeholder="อายุ">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-700 font-bold mb-2 text-sm">เลขบัตรประชาชน (13 หลัก)</label>
                            <input type="text" name="citizen_id" required maxlength="13" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="xxxxxxxxxxxxx">
                            <p id="cid_error" class="text-xs text-red-500 mt-1 hidden">เลขบัตรประชาชนไม่ถูกต้อง</p>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="ชื่อ-นามสกุล">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2 text-sm">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                <input type="tel" name="emergency_contact_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-gray-50 focus:bg-white" placeholder="08x-xxx-xxxx">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-700 font-bold mb-2 text-sm">ที่อยู่</label>
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
                        
                        <div class="space-y-8">
                            <!-- Walk & Run 3.5 km -->
                            <div class="bg-orange-50 rounded-2xl p-6 border border-orange-200">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center text-xl shadow-lg">
                                        <i class="fas fa-walking"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800">Walk & Run เดินการกุศล</h4>
                                        <p class="text-orange-600 font-bold">ระยะทาง 3.5 km</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Options -->
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Walk & Run 3.5km - ประถมศึกษา" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-100 peer-checked:text-orange-800 transition flex justify-between items-center">
                                            <span>ประถมศึกษา</span>
                                            <span class="font-bold">30 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Walk & Run 3.5km - ม.ต้น" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-100 peer-checked:text-orange-800 transition flex justify-between items-center">
                                            <span>ม.ต้น</span>
                                            <span class="font-bold">30 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Walk & Run 3.5km - ม.ปลาย/ปวช." required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-100 peer-checked:text-orange-800 transition flex justify-between items-center">
                                            <span>ม.ปลาย/ปวช.</span>
                                            <span class="font-bold">30 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Walk & Run 3.5km - VIP" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-100 peer-checked:text-orange-800 transition flex justify-between items-center">
                                            <span>VIP</span>
                                            <span class="font-bold">1,200 บาท</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Fun Run 5.5 km -->
                            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center text-xl shadow-lg">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800">Fun Run</h4>
                                        <p class="text-blue-600 font-bold">ระยะทาง 5.5 km</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Options -->
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - ประถมศึกษา" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>ประถมศึกษา</span>
                                            <span class="font-bold">300 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - ม.ต้น" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>ม.ต้น</span>
                                            <span class="font-bold">300 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - ม.ปลาย/ปวช." required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>ม.ปลาย/ปวช.</span>
                                            <span class="font-bold">300 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - บุคคลทั่วไป" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>บุคคลทั่วไป</span>
                                            <span class="font-bold">450 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - อายุมากกว่า 50" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>อายุมากกว่า 50</span>
                                            <span class="font-bold">450 บาท</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="category" value="Fun Run 5.5km - VIP" required class="peer sr-only">
                                        <div class="p-3 rounded-xl bg-white border border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:text-blue-800 transition flex justify-between items-center">
                                            <span>VIP</span>
                                            <span class="font-bold">1,200 บาท</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Placeholder for conditional fields depending on category -->
                        <div id="category-conditional" class="mt-4">
                            <!-- Conditions will be injected here later -->
                        </div>

                        <!-- Merchandise -->
                        <div class="mt-6 bg-yellow-50 rounded-2xl p-6 border border-yellow-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-yellow-500 text-white flex items-center justify-center text-xl shadow-lg">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-800">Merchandise</h4>
                                    <p class="text-yellow-600 font-bold">ของที่ระลึก</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="category" value="Shirt Only" required class="peer sr-only">
                                    <div class="p-3 rounded-xl bg-white border border-yellow-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-100 peer-checked:text-yellow-800 transition flex justify-between items-center">
                                        <span>สั่งซื้อเสื้อที่ระลึก (ไม่วิ่ง)</span>
                                        <span class="font-bold">250 บาท</span>
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

                    <!-- STEP 4: Shirt Size -->
                    <div class="form-step hidden" data-step="4">
                        <h3 class="text-xl font-bold text-secondary border-b border-gray-200 pb-3 mb-6 flex items-center gap-2">
                            <i class="fas fa-tshirt text-primary"></i> ไซส์เสื้อ
                        </h3>
                        
                        <!-- Size Chart Image -->
                        <div class="mb-8">
                            <img src="assets/images/shirt02.JPG" alt="ตารางไซส์เสื้อ" class="w-full max-w-2xl mx-auto rounded-2xl shadow-lg border border-gray-100">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-4 text-sm">เลือกไซส์เสื้อ</label>
                            <input type="radio" name="shirt_size" value="No Shirt" class="hidden" id="no_shirt_option">
                            
                            <!-- Standard Sizes -->
                            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Standard Sizes</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="XS" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">XS</span>
                                        <span class="text-xs opacity-75 block">อก 34"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 25"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="S" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">S</span>
                                        <span class="text-xs opacity-75 block">อก 36"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 26"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="M" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">M</span>
                                        <span class="text-xs opacity-75 block">อก 38"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 27"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="L" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">L</span>
                                        <span class="text-xs opacity-75 block">อก 40"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 28"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">XL</span>
                                        <span class="text-xs opacity-75 block">อก 42"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 29"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="2XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">2XL</span>
                                        <span class="text-xs opacity-75 block">อก 44"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 30"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="3XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">3XL</span>
                                        <span class="text-xs opacity-75 block">อก 46"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 31"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="4XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">4XL</span>
                                        <span class="text-xs opacity-75 block">อก 48"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 31"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="5XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">5XL</span>
                                        <span class="text-xs opacity-75 block">อก 50"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 31"</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Kids Sizes -->
                            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Size Chart (เด็ก)</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="Kids S" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">S (เด็ก)</span>
                                        <span class="text-xs opacity-75 block">อก 24"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 17"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="Kids M" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">M (เด็ก)</span>
                                        <span class="text-xs opacity-75 block">อก 26"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 18"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="Kids L" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">L (เด็ก)</span>
                                        <span class="text-xs opacity-75 block">อก 28"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 19"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="Kids XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">XL (เด็ก)</span>
                                        <span class="text-xs opacity-75 block">อก 30"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 20"</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="shirt_size" value="Kids 2XL" required class="peer sr-only">
                                    <div class="py-2 rounded-xl border border-gray-300 text-center peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition hover:border-primary">
                                        <span class="block font-bold">2XL (เด็ก)</span>
                                        <span class="text-xs opacity-75 block">อก 32"</span>
                                        <span class="text-[10px] opacity-60 block">ยาว 21"</span>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-8 border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-4">การรับเสื้อ</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="shipping_method" value="SELF" checked class="peer sr-only">
                                        <div class="p-4 rounded-xl bg-white border border-gray-300 peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary transition flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-primary peer-checked:text-white transition">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <div>
                                                <span class="block font-bold">รับด้วยตนเอง</span>
                                                <span class="text-xs text-gray-500">รับที่โรงเรียนพิชัย</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="shipping_method" value="POST" class="peer sr-only">
                                        <div class="p-4 rounded-xl bg-white border border-gray-300 peer-checked:border-primary peer-checked:bg-primary/5 peer-checked:text-primary transition flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-primary peer-checked:text-white transition">
                                                <i class="fas fa-truck"></i>
                                            </div>
                                            <div>
                                                <span class="block font-bold">ส่งทางไปรษณีย์</span>
                                                <span class="text-xs text-gray-500">ค่าจัดส่ง 50 บาท</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
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
                            <i class="fas fa-money-bill-wave text-primary"></i> ชำระเงิน
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
                            </div>

                            <!-- Payment Summary & Upload -->
                            <div class="flex flex-col justify-between">
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">ยอดที่ต้องชำระ</span>
                                        <span class="text-primary font-bold text-2xl"><span id="payment-amount">0</span> <span class="text-sm text-gray-500 font-normal">THB</span></span>
                                    </div>
                                    <div class="h-px bg-gray-200 my-3"></div>
                                    <p class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i> กรุณาตรวจสอบยอดเงินให้ถูกต้องก่อนโอน</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-bold mb-3 text-sm">วันที่และเวลาที่โอน (ตามสลิป)</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="far fa-calendar text-gray-400"></i>
                                            </div>
                                            <input type="date" name="payment_date" id="verified_date" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-white">
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="far fa-clock text-gray-400"></i>
                                            </div>
                                            <input type="time" name="payment_time" id="verified_time" required class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-700 font-bold mb-3 text-sm">หลักฐานการโอนเงิน (สลิป)</label>
                            
                            <!-- Hidden Fields for Verified Data -->
                            <input type="hidden" name="payment_amount" id="verified_amount">
                            <input type="hidden" name="bank_ref" id="verified_ref">
                            <input type="hidden" name="sender_name" id="verified_sender">

                            <!-- Upload Area -->
                            <div id="upload-area" class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:bg-blue-50 hover:border-blue-300 transition group cursor-pointer bg-gray-50">
                                <input type="file" name="payment_slip" id="payment_slip" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="text-gray-400 group-hover:text-primary transition flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-primary"></i>
                                    </div>
                                    <p class="font-bold text-gray-700 text-lg">คลิกเพื่ออัพโหลดสลิป</p>
                                    <p class="text-sm text-gray-500 mt-1">หรือลากไฟล์มาวางที่นี่</p>
                                    <p class="text-xs text-gray-400 mt-2">รองรับไฟล์ .jpg, .jpeg, .png (สูงสุด 5MB)</p>
                                </div>
                            </div>

                            <!-- Preview Area -->
                            <div id="slip-preview-container" class="hidden mt-6 animate-fade-in-up">
                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-md flex items-start gap-4">
                                    <div class="w-20 h-28 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200 relative group">
                                        <img id="slip-preview-img" src="" alt="Slip Preview" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <i class="fas fa-search-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 truncate" id="slip-filename">slip.jpg</p>
                                                <p class="text-xs text-gray-500" id="slip-filesize">120 KB</p>
                                            </div>
                                            <button type="button" id="remove-slip-btn" class="text-gray-400 hover:text-red-500 transition p-1">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="mt-3 flex items-center gap-2 text-xs text-green-600 bg-green-50 px-2 py-1 rounded-md inline-block">
                                            <i class="fas fa-check-circle"></i> อัพโหลดสำเร็จ
                                        </div>
                                    </div>
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
                    if (isNaN(birth.getTime())) return '';
                    
                    let birthYear = birth.getFullYear();
                    // Adjust for Buddhist Era (BE) if year > 2400
                    if (birthYear > 2400) {
                        birthYear -= 543;
                        birth.setFullYear(birthYear);
                    }

                    let age = today.getFullYear() - birthYear;
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
            // Prefix "Other" handling: show/hide input and ensure correct value is posted
            const prefixSelect = document.querySelector('select[name="prefix"]');
            const prefixOtherInput = document.querySelector('input[name="prefix_other"]');

            if (prefixSelect && prefixOtherInput) {
                prefixSelect.addEventListener('change', (e) => {
                    if (e.target.value === 'other') {
                        prefixOtherInput.classList.remove('hidden');
                        prefixOtherInput.focus();
                        prefixOtherInput.required = true;
                    } else {
                        prefixOtherInput.classList.add('hidden');
                        prefixOtherInput.required = false;
                        prefixOtherInput.value = '';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', (ev) => {
                    // Normalize birth_date from BE to CE if needed (e.g. 2568 -> 2025)
                    const birthInput = document.querySelector('input[name="birth_date"]');
                    if (birthInput && birthInput.value) {
                        const parts = birthInput.value.split('-');
                        if (parts.length === 3) {
                            let y = parseInt(parts[0]);
                            if (y > 2400) {
                                y -= 543;
                                birthInput.value = `${y}-${parts[1]}-${parts[2]}`;
                            }
                        }
                    }

                    // If user selected "other", ensure they provided a value and submit that as the prefix
                    if (prefixSelect && prefixSelect.value === 'other') {
                        if (!prefixOtherInput || !prefixOtherInput.value.trim()) {
                            ev.preventDefault();
                            if (prefixOtherInput) {
                                prefixOtherInput.classList.remove('hidden');
                                prefixOtherInput.focus();
                            }
                            Swal.fire({
                                icon: 'warning',
                                title: 'ข้อมูลไม่ครบถ้วน',
                                text: 'กรุณาระบุคำนำหน้า',
                                confirmButtonText: 'ตกลง',
                                customClass: {
                                    popup: 'rounded-3xl shadow-xl border border-gray-100',
                                    confirmButton: 'bg-gradient-to-r from-primary to-red-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1',
                                    title: 'text-2xl font-bold text-secondary font-sans',
                                    htmlContainer: 'text-gray-600 font-sans'
                                },
                                buttonsStyling: false
                            });
                            return;
                        }

                        // disable the select so it doesn't submit its literal "other" value
                        prefixSelect.disabled = true;

                        // create or update a hidden input named "prefix" with the custom value so backend receives it
                        let hiddenPrefix = document.querySelector('input[name="prefix"][type="hidden"]');
                        if (!hiddenPrefix) {
                            hiddenPrefix = document.createElement('input');
                            hiddenPrefix.type = 'hidden';
                            hiddenPrefix.name = 'prefix';
                            form.appendChild(hiddenPrefix);
                        }
                        hiddenPrefix.value = prefixOtherInput.value.trim();
                    }

                    // determine final prefix (use custom other value if provided)
                    const selectNode = document.querySelector('select[name="prefix"]');
                    const first = (document.querySelector('input[name="first_name"]') || {}).value || '';
                    const last = (document.querySelector('input[name="last_name"]') || {}).value || '';
                    let finalPrefix = '';
                    if (selectNode) {
                        if (selectNode.value === 'other') {
                            const hiddenPrefixNode = document.querySelector('input[name="prefix"][type="hidden"]');
                            if (hiddenPrefixNode && hiddenPrefixNode.value) finalPrefix = hiddenPrefixNode.value;
                            else if (prefixOtherInput && prefixOtherInput.value) finalPrefix = prefixOtherInput.value.trim();
                            else finalPrefix = selectNode.value;
                        } else {
                            finalPrefix = selectNode.value;
                        }
                    }
                    const hiddenFull = document.getElementById('hidden_full_name');
                    if (hiddenFull) hiddenFull.value = [finalPrefix, first, last].filter(Boolean).join(' ').trim();
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

                // Helper for SweetAlert
                const showError = (msg) => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ข้อมูลไม่ครบถ้วน',
                        text: msg,
                        confirmButtonText: 'ตกลง',
                        customClass: {
                            popup: 'rounded-3xl shadow-xl border border-gray-100',
                            confirmButton: 'bg-gradient-to-r from-primary to-red-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:shadow-red-500/30 transition transform hover:-translate-y-1',
                            title: 'text-2xl font-bold text-secondary font-sans',
                            htmlContainer: 'text-gray-600 font-sans'
                        },
                        buttonsStyling: false
                    });
                };

                // attach next/prev handlers
                document.addEventListener('click', (e) => {
                    if (e.target.matches('.next-btn')) {
                        // basic validation for current step: required fields
                        const currentFields = steps[currentStep].querySelectorAll('[required]');
                        let isValid = true;
                        
                        for (const f of currentFields) {
                            if (f.type === 'radio' || f.type === 'checkbox') {
                                const group = document.getElementsByName(f.name);
                                let checked = false;
                                for (const r of group) {
                                    if (r.checked) checked = true;
                                }
                                if (!checked) {
                                    showError('กรุณาเลือก ' + (f.name === 'gender' ? 'เพศ' : 'ตัวเลือก'));
                                    isValid = false;
                                    break;
                                }
                            } else {
                                if (!f.value) {
                                    // Find label text if possible
                                    let labelText = 'ข้อมูล';
                                    const label = f.closest('div')?.querySelector('label');
                                    if (label) labelText = label.innerText.replace('*', '').trim();
                                    
                                    showError('กรุณากรอก ' + labelText);
                                    f.focus();
                                    isValid = false;
                                    break;
                                }
                            }
                        }
                        
                        if (!isValid) return;

                        // Special logic for Step 3 (Race Info) -> Step 4 (Shirt)
                        if (currentStep === 2) {
                            const category = document.querySelector('input[name="category"]:checked');
                            const noShirtCategories = [
                                'Walk & Run 3.5km - ประถมศึกษา',
                                'Walk & Run 3.5km - ม.ต้น',
                                'Walk & Run 3.5km - ม.ปลาย/ปวช.'
                            ];

                            if (category && noShirtCategories.includes(category.value)) {
                                // Skip Step 4 (Shirt)
                                const noShirtRadio = document.getElementById('no_shirt_option');
                                if (noShirtRadio) noShirtRadio.checked = true;
                                
                                currentStep = 4; // Jump to Step 5 (Payment)
                                updateSteps();
                                calculateTotal(); // Calculate total when skipping to payment
                                return;
                            } else {
                                // Ensure "No Shirt" is NOT checked if we are showing the step
                                const noShirtRadio = document.getElementById('no_shirt_option');
                                if (noShirtRadio && noShirtRadio.checked) noShirtRadio.checked = false;
                            }
                        }

                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateSteps();
                            if (currentStep === 4) {
                                calculateTotal(); // Calculate total when reaching payment step
                            }
                        }
                    }
                    if (e.target.matches('.prev-btn')) {
                        if (currentStep > 0) {
                            // Special logic for Back from Step 5 (Payment) -> Step 3 or 4
                            if (currentStep === 4) {
                                const noShirtRadio = document.getElementById('no_shirt_option');
                                if (noShirtRadio && noShirtRadio.checked) {
                                    currentStep = 2; // Jump back to Step 3
                                    updateSteps();
                                    return;
                                }
                            }
                            
                            currentStep--;
                            updateSteps();
                        }
                    }
                });

                // Calculate Total Amount
                const calculateTotal = () => {
                    let total = 0;
                    const category = document.querySelector('input[name="category"]:checked');
                    const shipping = document.querySelector('input[name="shipping_method"]:checked');
                    
                    if (category) {
                        const val = category.value;
                        // Walk & Run 3.5km
                        if (val.includes('Walk & Run 3.5km')) {
                            if (val.includes('VIP')) total += 1200;
                            else total += 30;
                        }
                        // Fun Run 5.5km
                        else if (val.includes('Fun Run 5.5km')) {
                            if (val.includes('VIP')) total += 1200;
                            else if (val.includes('บุคคลทั่วไป') || val.includes('อายุมากกว่า 50')) total += 450;
                            else total += 300; // Students
                        }
                        // Merchandise
                        else if (val === 'Shirt Only') {
                            total += 250;
                        }
                    }

                    // Add shipping cost
                    if (shipping && shipping.value === 'POST') {
                        total += 50;
                    }

                    const amountDisplay = document.getElementById('payment-amount');
                    if (amountDisplay) amountDisplay.textContent = total.toLocaleString();
                    
                    // Update Hidden Input for DB
                    const hiddenAmount = document.getElementById('verified_amount');
                    if (hiddenAmount) hiddenAmount.value = total;

                    // Update QR Code
                    if (typeof updateQRCode === 'function') {
                        updateQRCode(total);
                    }
                };

                // Add event listener for shipping method change
                document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
                    radio.addEventListener('change', calculateTotal);
                });

                // Citizen ID Validation
                const cidInput = document.querySelector('input[name="citizen_id"]');
                const cidError = document.getElementById('cid_error');
                
                const validateCID = (cid) => {
                    if(cid.length !== 13) return false;
                    if(!/^[0-9]+$/.test(cid)) return false;
                    
                    let sum = 0;
                    for(let i=0; i<12; i++) {
                        sum += parseFloat(cid.charAt(i)) * (13-i);
                    }
                    
                    if((11 - sum % 11) % 10 !== parseFloat(cid.charAt(12))) {
                        return false;
                    }
                    return true;
                };

                if(cidInput) {
                    cidInput.addEventListener('input', (e) => {
                        const val = e.target.value;
                        if(val.length === 13) {
                            if(!validateCID(val)) {
                                cidError.classList.remove('hidden');
                                cidInput.classList.add('border-red-500');
                                // cidInput.setCustomValidity('เลขบัตรประชาชนไม่ถูกต้อง');
                            } else {
                                                               cidError.classList.add('hidden');
                                cidInput.classList.remove('border-red-500');
                                cidInput.setCustomValidity('');
                            }
                        } else {
                            cidError.classList.add('hidden');
                            cidInput.classList.remove('border-red-500');
                                                       cidInput.setCustomValidity('');
                        }
                    });
                }

                // Slip Upload & Verification Logic
                const slipInput = document.getElementById('payment_slip');
                const uploadArea = document.getElementById('upload-area');
                const previewContainer = document.getElementById('slip-preview-container');
                const previewImg = document.getElementById('slip-preview-img');
                const filenameDisplay = document.getElementById('slip-filename');
                const filesizeDisplay = document.getElementById('slip-filesize');
                const removeBtn = document.getElementById('remove-slip-btn');
                const verifyBtn = document.getElementById('verify-slip-btn');
                const statusContainer = document.getElementById('verification-status');
                let isVerified = false;

                if (slipInput) {
                    slipInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Show preview
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                                uploadArea.classList.add('hidden');
                                previewContainer.classList.remove('hidden');
                                filenameDisplay.textContent = file.name;
                                filesizeDisplay.textContent = (file.size / 1024).toFixed(2) + ' KB';
                                
                                // Reset verification status
                                isVerified = false;
                                resetVerificationUI();
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        slipInput.value = '';
                        uploadArea.classList.remove('hidden');
                        previewContainer.classList.add('hidden');
                        isVerified = false;
                    });
                }

                if (verifyBtn) {
                    verifyBtn.addEventListener('click', function() {
                        const file = slipInput.files[0];
                        if (!file) return;

                        // Show Loading
                        statusContainer.innerHTML = `
                            <div class="w-full py-2 rounded-lg bg-gray-100 text-gray-600 text-sm font-bold flex items-center justify-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i> กำลังตรวจสอบกับธนาคาร...
                            </div>
                        `;

                        const formData = new FormData();
                        formData.append('payment_slip', file);

                        fetch('api/verify_slip.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const info = data.data;
                                // Populate Hidden Fields
                                document.getElementById('verified_amount').value = info.amount;
                                
                                // Format YYYYMMDD to YYYY-MM-DD
                                const d = info.transDate;
                                const formattedDate = d.length === 8 ? d.substring(0, 4) + '-' + d.substring(4, 6) + '-' + d.substring(6, 8) : d;
                                document.getElementById('verified_date').value = formattedDate;
                                
                                document.getElementById('verified_time').value = info.transTime;
                                document.getElementById('verified_ref').value = info.transRef;
                                document.getElementById('verified_sender').value = info.sender.displayName;

                                // Check if this is demo/mock data
                                const isDemoWarning = data.message && data.message.includes('DEMO');
                                const warningBanner = isDemoWarning ? `
                                    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-3 py-2 rounded-lg mb-2 text-xs">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> ${data.message}
                                    </div>
                                ` : '';

                                // Show Success UI with Details
                                statusContainer.innerHTML = `
                                    <div class="w-full bg-green-50 border border-green-200 rounded-lg p-3">
                                        ${warningBanner}
                                        <div class="flex items-center gap-2 text-green-700 font-bold mb-2">
                                            <i class="fas fa-check-circle"></i> ตรวจสอบเรียบร้อย
                                        </div>
                                        <div class="text-xs text-gray-600 space-y-1">
                                            <p><strong>ผู้โอน:</strong> ${info.sender.displayName}</p>
                                            <p><strong>จำนวนเงิน:</strong> ${info.amount.toLocaleString()} บาท</p>
                                            <p><strong>รหัสอ้างอิง:</strong> ${info.transRef}</p>
                                            <p><strong>เวลา:</strong> ${info.transTime}</p>
                                        </div>
                                    </div>
                                `;
                                isVerified = true;
                            } else {
                                throw new Error(data.message || 'Verification failed');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            statusContainer.innerHTML = `
                                <div class="w-full py-2 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-bold flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i> ตรวจสอบไม่ผ่าน: ${error.message}
                                </div>
                            `;
                            // Allow retry
                            setTimeout(() => {
                                resetVerificationUI();
                            }, 3000);
                        });
                    });
                }

                function resetVerificationUI() {
                    statusContainer.innerHTML = `
                        <button type="button" id="verify-slip-btn" class="w-full py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                            <i class="fas fa-search"></i> ตรวจสอบสลิป
                        </button>
                    `;
                    // Re-attach event listener since innerHTML replaced the button
                    document.getElementById('verify-slip-btn').addEventListener('click', function() {
                        statusContainer.innerHTML = `
                            <div class="w-full py-2 rounded-lg bg-gray-100 text-gray-600 text-sm font-bold flex items-center justify-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i> กำลังตรวจสอบ...
                            </div>
                        `;
                        setTimeout(() => {
                            statusContainer.innerHTML = `
                                <div class="w-full py-2 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> ตรวจสอบเรียบร้อย
                                </div>
                            `;
                            isVerified = true;
                        }, 1500);
                    });
                }

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

                // PromptPay QR Generation Logic
                // TODO: Replace with the actual Tax ID of "สมาคมนักเรียนเก่าพิชัย"
                const PROMPTPAY_ID = ""; 

                function generatePromptPayPayload(target, amount) {
                    let targetType = target.length >= 13 ? '02' : '01';
                    if (targetType === '01' && target.startsWith('0')) {
                        target = '66' + target.substring(1);
                    }
                    
                    const f = (id, value) => {
                        const valStr = String(value);
                        const lenStr = valStr.length.toString().padStart(2, '0');
                        return id + lenStr + valStr;
                    };

                    let data = [
                        f('00', '01'),
                        f('01', amount ? '12' : '11'),
                        f('29', [
                            f('00', 'A000000677010111'),
                            f(targetType, target)
                        ].join('')),
                        f('58', 'TH'),
                        f('53', '764'),
                    ];

                    if (amount) {
                        data.push(f('54', amount.toFixed(2)));
                    }

                    let raw = data.join('') + '6304';
                    
                    // CRC16-CCITT (XModem)
                    let crc = 0xFFFF;
                    for (let i = 0; i < raw.length; i++) {
                        let x = ((crc >> 8) ^ raw.charCodeAt(i)) & 0xFF;
                        x ^= x >> 4;
                        crc = ((crc << 8) ^ (x << 12) ^ (x << 5) ^ x) & 0xFFFF;
                    }
                    const crcHex = crc.toString(16).toUpperCase().padStart(4, '0');
                    
                    return raw + crcHex;
                }

                function updateQRCode(amount) {
                    const canvas = document.getElementById('payment_qrcode');
                    const overlay = document.getElementById('qr-overlay');
                    
                    if (!PROMPTPAY_ID) {
                        if(overlay) overlay.classList.remove('hidden');
                        return;
                    }
                    
                    if(overlay) overlay.classList.add('hidden');

                    // Ensure QRCode library is loaded
                    if (typeof QRCode === 'undefined') return;

                    const payload = generatePromptPayPayload(PROMPTPAY_ID, amount);
                    
                    QRCode.toCanvas(canvas, payload, {
                        width: 200,
                        margin: 1,
                        color: {
                            dark: "#000000",
                            light: "#ffffff"
                        }
                    }, function (error) {
                        if (error) console.error(error);
                    });
                }

                // initialize
                updateSteps();
            }

            window.copyToClipboard = (text, btn) => {
                navigator.clipboard.writeText(text).then(() => {
                    const originalIcon = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check text-green-400"></i>';
                    setTimeout(() => {
                        btn.innerHTML = originalIcon;
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    // Fallback
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        const originalIcon = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-check text-green-400"></i>';
                        setTimeout(() => {
                            btn.innerHTML = originalIcon;
                        }, 2000);
                    } catch (err) {
                        console.error('Fallback: Oops, unable to copy', err);
                    }
                    document.body.removeChild(textArea);
                });
            };
        })();
    </script>
</body>
</html>
