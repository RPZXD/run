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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน - Phichai Run 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E30613',
                        secondary: '#8B0000',
                        accent: '#FFD700'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav id="navbar" class="fixed w-full z-50 bg-secondary text-white py-4 shadow-lg">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold tracking-wider flex items-center gap-2">
                <img src="assets/images/logo-1.jpeg" alt="Logo" class="h-10 w-10 rounded-full border-2 border-accent">
                PHICHAI RUN 2026
            </a>
            <a href="index.php" class="text-white hover:text-accent transition">
                <i class="fas fa-arrow-left"></i> กลับหน้าหลัก
            </a>
        </div>
    </nav>

    <div class="pt-24 pb-12 min-h-screen flex items-center justify-center bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
        <div class="container mx-auto px-4 max-w-3xl">
            
            <?php if ($message): ?>
                <div class="mb-8 p-4 rounded-lg <?php echo $status === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?> text-center">
                    <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl mr-2"></i>
                    <span class="font-bold"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-primary p-6 text-center text-white">
                    <h2 class="text-3xl font-bold">ลงทะเบียนเข้าร่วมงาน</h2>
                    <p class="opacity-90">กรอกข้อมูลให้ครบถ้วนเพื่อสมัครวิ่ง</p>
                </div>
                
                <form action="register.php" method="POST" class="p-8 space-y-6">
                    <!-- Personal Info -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2">ข้อมูลส่วนตัว</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">ชื่อ-นามสกุล (ภาษาไทย)</label>
                                <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="นาย รักดี มีชัย">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">ชื่อบน BIB (ภาษาอังกฤษ)</label>
                                <input type="text" name="bib_name" maxlength="10" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="RUKDEE (Max 10 chars)">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">เพศ</label>
                                <select name="gender" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                    <option value="">เลือกเพศ</option>
                                    <option value="Male">ชาย</option>
                                    <option value="Female">หญิง</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">วันเกิด</label>
                                <input type="date" name="birth_date" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2">ข้อมูลติดต่อ</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="08x-xxx-xxxx">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">อีเมล</label>
                                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary" placeholder="example@email.com">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">ที่อยู่จัดส่งเอกสาร</label>
                            <textarea name="address" rows="3" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary"></textarea>
                        </div>
                    </div>

                    <!-- Race Info -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-secondary border-b pb-2">ข้อมูลการแข่งขัน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">ประเภทการแข่งขัน</label>
                                <select name="category" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                    <option value="">เลือกระยะทาง</option>
                                    <option value="Fun Run 5KM">Fun Run 5 KM (450 บาท)</option>
                                    <option value="Mini Marathon 10.5KM">Mini Marathon 10.5 KM (550 บาท)</option>
                                    <option value="Half Marathon 21KM">Half Marathon 21 KM (750 บาท)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">ไซส์เสื้อ</label>
                                <select name="shirt_size" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                    <option value="">เลือกไซส์</option>
                                    <option value="XS">XS (34")</option>
                                    <option value="S">S (36")</option>
                                    <option value="M">M (38")</option>
                                    <option value="L">L (40")</option>
                                    <option value="XL">XL (42")</option>
                                    <option value="2XL">2XL (44")</option>
                                    <option value="3XL">3XL (46")</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-primary hover:bg-red-700 text-white font-bold py-4 rounded-lg shadow-lg transition transform hover:scale-105 text-xl">
                            ยืนยันการสมัคร
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900 text-white py-6 text-center">
        <p>&copy; 2025 Phichai Run. All rights reserved.</p>
    </footer>

</body>
</html>
