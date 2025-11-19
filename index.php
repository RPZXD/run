<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phichai Run 2026 - วิ่งพิชัยดาบหัก</title>
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
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 bg-transparent text-white py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold tracking-wider flex items-center gap-2">
                <img src="assets/images/logo-1.png" alt="Logo" class="h-12 w-12 rounded-full border-2 border-accent">
                PHICHAI RUN 2026
            </a>
            <div class="hidden md:flex space-x-8 font-medium">
                <a href="#home" class="hover:text-primary transition">หน้าแรก</a>
                <a href="#about" class="hover:text-primary transition">รายละเอียด</a>
                <a href="#categories" class="hover:text-primary transition">ระยะทาง</a>
                <a href="#schedule" class="hover:text-primary transition">กำหนดการ</a>
                <a href="#contact" class="hover:text-primary transition">ติดต่อเรา</a>
            </div>
            <a href="register.php" class="hidden md:block bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-full font-bold transition transform hover:scale-105 shadow-lg">
                สมัครวิ่ง
            </a>
            <!-- Mobile Menu Button -->
            <button class="md:hidden text-2xl">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg h-screen flex items-center justify-center text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-900 opacity-90"></div>
        <div class="relative z-10 px-6 fade-in-up">
            <h2 class="text-xl md:text-3xl font-light mb-4 text-accent tracking-widest">14 กุมภาพันธ์ 2569</h2>
            <h1 class="text-5xl md:text-8xl font-bold mb-6 text-shadow leading-tight">
                PHICHAI RUN <span class="text-primary">2026</span>
            </h1>
            <p class="text-lg md:text-2xl mb-10 max-w-2xl mx-auto text-gray-200">
                ปลุกตำนานความกล้า วิ่งท้าประวัติศาสตร์ ณ ดินแดนท่านพ่อพระยาพิชัยดาบหัก
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="register.php" class="bg-primary hover:bg-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-ticket-alt"></i> สมัครเข้าร่วม
                </a>
                <a href="#about" class="bg-transparent border-2 border-white hover:bg-white hover:text-secondary text-white px-8 py-4 rounded-full font-bold text-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-info-circle"></i> รายละเอียด
                </a>
            </div>
        </div>
    </section>

    <!-- Countdown Section -->
    <section class="py-10 bg-secondary text-white -mt-20 relative z-20 mx-4 md:mx-20 rounded-xl shadow-2xl fade-in-up">
        <div class="container mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold mb-6">นับถอยหลังสู่วันงาน</h3>
            <div id="countdown" class="flex flex-wrap justify-center gap-4 md:gap-10">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-lg w-24 md:w-32">
                    <span id="days" class="block text-3xl md:text-5xl font-bold text-accent">00</span>
                    <span class="text-sm md:text-base">วัน</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-lg w-24 md:w-32">
                    <span id="hours" class="block text-3xl md:text-5xl font-bold text-accent">00</span>
                    <span class="text-sm md:text-base">ชั่วโมง</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-lg w-24 md:w-32">
                    <span id="minutes" class="block text-3xl md:text-5xl font-bold text-accent">00</span>
                    <span class="text-sm md:text-base">นาที</span>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-lg w-24 md:w-32">
                    <span id="seconds" class="block text-3xl md:text-5xl font-bold text-accent">00</span>
                    <span class="text-sm md:text-base">วินาที</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 fade-in-up">
                    <img src="assets/images/shirt.jpeg" alt="Running Shirt" class="rounded-2xl shadow-2xl hover-scale w-full object-cover h-96">
                </div>
                <div class="md:w-1/2 fade-in-up">
                    <h4 class="text-primary font-bold text-lg mb-2">เกี่ยวกับงาน</h4>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">วิ่งเพื่อสุขภาพ และสืบสานตำนาน</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Phichai Run 2026 กลับมาอีกครั้งกับความยิ่งใหญ่กว่าเดิม! เชิญชวนนักวิ่งทุกท่านมาร่วมสัมผัสบรรยากาศการวิ่งในเส้นทางประวัติศาสตร์ ผ่านสถานที่สำคัญของอำเภอพิชัย จังหวัดอุตรดิตถ์
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        รายได้ส่วนหนึ่งหลังหักค่าใช้จ่าย จะนำไปสมทบทุนบูรณะศาลพระยาพิชัยดาบหัก และสนับสนุนอุปกรณ์กีฬาให้กับโรงเรียนในพื้นที่
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700">เสื้อวิ่งดีไซน์พิเศษ Limited Edition</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700">เหรียญรางวัลผู้พิชิตชัย</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <span class="text-gray-700">อาหารและเครื่องดื่มจัดเต็มตลอดงาน</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-20 bg-gray-100 relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">ประเภทการแข่งขัน</h2>
                <div class="w-20 h-1 bg-primary mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Fun Run -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300 fade-in-up transform hover:-translate-y-2">
                    <div class="bg-blue-500 h-2"></div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 text-blue-600 text-3xl">
                            <i class="fas fa-walking"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Fun Run</h3>
                        <p class="text-5xl font-bold text-blue-600 mb-4">5 <span class="text-xl text-gray-500">KM</span></p>
                        <p class="text-gray-500 mb-6">เดิน-วิ่ง เพื่อสุขภาพ</p>
                        <hr class="border-gray-200 mb-6">
                        <ul class="text-left space-y-3 mb-8 text-gray-600">
                            <li><i class="fas fa-tshirt w-6 text-center"></i> เสื้อที่ระลึก</li>
                            <li><i class="fas fa-medal w-6 text-center"></i> เหรียญรางวัล</li>
                            <li><i class="fas fa-clock w-6 text-center"></i> ปล่อยตัว 06:00 น.</li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-6">450 <span class="text-sm font-normal">บาท</span></div>
                        <a href="register.php" class="block w-full bg-gray-800 hover:bg-blue-600 text-white py-3 rounded-lg font-bold transition">สมัครระยะนี้</a>
                    </div>
                </div>

                <!-- Mini Marathon -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-300 fade-in-up transform hover:-translate-y-2 border-2 border-primary relative">
                    <div class="absolute top-0 right-0 bg-primary text-white text-xs font-bold px-3 py-1 rounded-bl-lg">POPULAR</div>
                    <div class="bg-primary h-2"></div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6 text-primary text-3xl">
                            <i class="fas fa-running"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Mini Marathon</h3>
                        <p class="text-5xl font-bold text-primary mb-4">10.5 <span class="text-xl text-gray-500">KM</span></p>
                        <p class="text-gray-500 mb-6">มินิมาราธอน</p>
                        <hr class="border-gray-200 mb-6">
                        <ul class="text-left space-y-3 mb-8 text-gray-600">
                            <li><i class="fas fa-tshirt w-6 text-center"></i> เสื้อที่ระลึก</li>
                            <li><i class="fas fa-medal w-6 text-center"></i> เหรียญรางวัล</li>
                            <li><i class="fas fa-stopwatch w-6 text-center"></i> ชิพจับเวลา</li>
                            <li><i class="fas fa-clock w-6 text-center"></i> ปล่อยตัว 05:30 น.</li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-6">550 <span class="text-sm font-normal">บาท</span></div>
                        <a href="register.php" class="block w-full bg-primary hover:bg-orange-600 text-white py-3 rounded-lg font-bold transition">สมัครระยะนี้</a>
                    </div>
                </div>

                <!-- Half Marathon -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300 fade-in-up transform hover:-translate-y-2">
                    <div class="bg-secondary h-2"></div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 text-secondary text-3xl">
                            <i class="fas fa-road"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Half Marathon</h3>
                        <p class="text-5xl font-bold text-secondary mb-4">21 <span class="text-xl text-gray-500">KM</span></p>
                        <p class="text-gray-500 mb-6">ฮาล์ฟมาราธอน</p>
                        <hr class="border-gray-200 mb-6">
                        <ul class="text-left space-y-3 mb-8 text-gray-600">
                            <li><i class="fas fa-tshirt w-6 text-center"></i> เสื้อ Finisher (เมื่อเข้าเส้นชัย)</li>
                            <li><i class="fas fa-medal w-6 text-center"></i> เหรียญรางวัล</li>
                            <li><i class="fas fa-stopwatch w-6 text-center"></i> ชิพจับเวลา</li>
                            <li><i class="fas fa-clock w-6 text-center"></i> ปล่อยตัว 05:00 น.</li>
                        </ul>
                        <div class="text-3xl font-bold text-gray-800 mb-6">750 <span class="text-sm font-normal">บาท</span></div>
                        <a href="register.php" class="block w-full bg-gray-800 hover:bg-secondary text-white py-3 rounded-lg font-bold transition">สมัครระยะนี้</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="py-20 bg-secondary text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold mb-4">กำหนดการ</h2>
                <p class="text-gray-300">วันอาทิตย์ที่ 14 กุมภาพันธ์ 2569</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="relative border-l-4 border-primary ml-3 md:ml-6 space-y-8">
                    <!-- Item 1 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">04:00</span>
                            <div>
                                <h4 class="text-xl font-bold">เปิดจุดรับฝากของ</h4>
                                <p class="text-gray-400">ณ บริเวณลานหน้าอนุสาวรีย์พระยาพิชัยดาบหัก</p>
                            </div>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">04:45</span>
                            <div>
                                <h4 class="text-xl font-bold">วอร์มอัพร่างกาย</h4>
                                <p class="text-gray-400">นำวอร์มโดยเทรนเนอร์มืออาชีพ</p>
                            </div>
                        </div>
                    </div>
                    <!-- Item 3 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">05:00</span>
                            <div>
                                <h4 class="text-xl font-bold">ปล่อยตัว Half Marathon 21 KM</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Item 4 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">05:30</span>
                            <div>
                                <h4 class="text-xl font-bold">ปล่อยตัว Mini Marathon 10.5 KM</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Item 5 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">06:00</span>
                            <div>
                                <h4 class="text-xl font-bold">ปล่อยตัว Fun Run 5 KM</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Item 6 -->
                    <div class="relative pl-8 fade-in-up">
                        <div class="absolute -left-3 top-0 bg-primary w-6 h-6 rounded-full border-4 border-secondary"></div>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8">
                            <span class="text-2xl font-bold text-accent w-24">07:30</span>
                            <div>
                                <h4 class="text-xl font-bold">พิธีมอบรางวัล</h4>
                                <p class="text-gray-400">และรับประทานอาหารเช้า</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration CTA -->
    <section id="register" class="py-20 bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-8">พร้อมที่จะท้าทายตัวเองหรือยัง?</h2>
            <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                อย่ารอช้า! จำนวนจำกัด สมัครวันนี้เพื่อรับราคา Early Bird และของที่ระลึกสุดพิเศษ
            </p>
            <a href="register.php" class="inline-block bg-white text-primary hover:bg-gray-100 text-xl font-bold py-4 px-12 rounded-full shadow-2xl transition transform hover:scale-105">
                สมัครออนไลน์ทันที
            </a>
            <p class="mt-6 text-white/80 text-sm">*ปิดรับสมัครวันที่ 31 ธันวาคม 2568 หรือจนกว่าจะเต็ม</p>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4 flex items-center gap-2">
                        <img src="assets/images/logo-1.jpeg" class="h-8 w-8 rounded-full"> PHICHAI RUN 2026
                    </h3>
                    <p class="text-gray-400 mb-6 max-w-md">
                        งานวิ่งการกุศลเพื่อสุขภาพและสังคม จัดโดยชมรมวิ่งอำเภอพิชัย ร่วมกับเทศบาลตำบลพิชัย มาร่วมเป็นส่วนหนึ่งของประวัติศาสตร์หน้าใหม่
                    </p>
                    <div class="flex gap-4 mb-6">
                        <img src="assets/images/logo-2.jpeg" class="h-16 w-auto rounded bg-white p-1 object-contain">
                        <img src="assets/images/logo-3.jpeg" class="h-16 w-auto rounded bg-white p-1 object-contain">
                        <img src="assets/images/logo-4.jpeg" class="h-16 w-auto rounded bg-white p-1 object-contain">
                    </div>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/phichairun2026/" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-facebook-f"></i></a>
                        <!-- <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-line"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-primary transition"><i class="fab fa-instagram"></i></a> -->
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4 text-gray-200">ลิงก์ด่วน</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-primary transition">หน้าแรก</a></li>
                        <li><a href="#about" class="hover:text-primary transition">รายละเอียด</a></li>
                        <li><a href="#categories" class="hover:text-primary transition">ประเภทการแข่งขัน</a></li>
                        <li><a href="#register" class="hover:text-primary transition">สมัครวิ่ง</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4 text-gray-200">ติดต่อเรา</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                            <span>โรงเรียนพิชัย<br>9/9 หมู่ 3 ต.ในเมือง อ.พิชัย จ.อุตรดิตถ์ 53120</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-primary"></i>
                            <span>โทรศัพท์ 055-421-402</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-fax text-primary"></i>
                            <span>โทรสาร 055-421-406</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <span>phichaischool@gmail.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2025 Phichai Run. All rights reserved. Designed by AI Assistant.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
