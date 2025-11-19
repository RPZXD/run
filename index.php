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
            <a href="#" class="text-2xl font-bold tracking-wider flex items-center gap-3 group">
                <div class="relative">
                    <img src="assets/images/logo-1.png" alt="Logo" class="h-14 w-14 rounded-full border-3 border-accent shadow-lg group-hover:scale-110 transition">
                    <div class="absolute -inset-1 bg-accent/30 rounded-full blur group-hover:bg-accent/50 transition"></div>
                </div>
                <span class="gradient-text group-hover:scale-105 transition">PHICHAI RUN 2026</span>
            </a>
            <div class="hidden md:flex space-x-8 font-medium text-lg">
                <a href="#home" class="hover:text-accent transition relative group">
                    หน้าแรก
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent group-hover:w-full transition-all"></span>
                </a>
                <a href="#about" class="hover:text-accent transition relative group">
                    รายละเอียด
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent group-hover:w-full transition-all"></span>
                </a>
                <a href="#categories" class="hover:text-accent transition relative group">
                    ระยะทาง
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent group-hover:w-full transition-all"></span>
                </a>
                <a href="#schedule" class="hover:text-accent transition relative group">
                    กำหนดการ
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent group-hover:w-full transition-all"></span>
                </a>
                <a href="#contact" class="hover:text-accent transition relative group">
                    ติดต่อเรา
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-accent group-hover:w-full transition-all"></span>
                </a>
            </div>
            <a href="register.php" class="hidden md:block bg-gradient-to-r from-primary to-orange-600 hover:from-orange-600 hover:to-primary text-white px-6 py-3 rounded-full font-bold transition transform hover:scale-110 shadow-lg sparkle-btn">
                <i class="fas fa-rocket mr-2"></i>สมัครวิ่ง
            </a>
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-3xl hover:text-accent transition">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden absolute right-6 top-20 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl w-64 p-6 border-2 border-primary/20">
                <nav class="flex flex-col">
                    <a href="#home" class="py-3 px-4 rounded-lg hover:bg-primary/10 text-gray-800 font-semibold transition">🏠 หน้าแรก</a>
                    <a href="#about" class="py-3 px-4 rounded-lg hover:bg-primary/10 text-gray-800 font-semibold transition">📖 รายละเอียด</a>
                    <a href="#categories" class="py-3 px-4 rounded-lg hover:bg-primary/10 text-gray-800 font-semibold transition">🏃 ระยะทาง</a>
                    <a href="#schedule" class="py-3 px-4 rounded-lg hover:bg-primary/10 text-gray-800 font-semibold transition">📅 กำหนดการ</a>
                    <a href="#contact" class="py-3 px-4 rounded-lg hover:bg-primary/10 text-gray-800 font-semibold transition">📞 ติดต่อเรา</a>
                    <a href="register.php" class="mt-4 bg-gradient-to-r from-primary to-orange-600 text-white py-3 rounded-full text-center font-bold sparkle-btn shadow-lg hover:scale-105 transition">
                        <i class="fas fa-rocket mr-2"></i>สมัครวิ่ง
                    </a>
                </nav>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg h-screen flex items-center justify-center text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-gray-900/90"></div>
        
        <!-- Animated Circles Background -->
        <div class="absolute inset-0 overflow-hidden opacity-20">
            <div class="absolute w-96 h-96 bg-primary rounded-full blur-3xl -top-48 -left-48 animate-pulse"></div>
            <div class="absolute w-96 h-96 bg-accent rounded-full blur-3xl top-1/2 right-0 floating"></div>
            <div class="absolute w-96 h-96 bg-secondary rounded-full blur-3xl bottom-0 left-1/2 animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative z-10 px-6 fade-in-up">
            <div class="mb-6 inline-block">
                <i class="fas fa-running text-6xl text-accent running-icon"></i>
            </div>
            <h2 class="text-xl md:text-3xl font-light mb-4 text-accent tracking-widest bounce">
                📅 14 กุมภาพันธ์ 2569
            </h2>
            <h1 class="text-5xl md:text-8xl font-bold mb-6 leading-tight">
                <span class="gradient-text">PHICHAI RUN</span> <span class="text-primary text-shadow">2026</span>
            </h1>
            <p class="text-lg md:text-2xl mb-10 max-w-2xl mx-auto text-gray-200">
                ✨ ปลุกตำนานความกล้า วิ่งท้าประวัติศาสตร์ ✨<br>
                <span class="text-accent font-semibold">ณ ดินแดนท่านพ่อพระยาพิชัยดาบหัก</span>
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="register.php" class="bg-gradient-to-r from-primary to-orange-600 hover:from-orange-600 hover:to-primary text-white px-8 py-4 rounded-full font-bold text-lg transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2 sparkle-btn pulse-animate">
                    <i class="fas fa-rocket"></i> สมัครเข้าร่วม
                </a>
                <a href="#about" class="bg-transparent border-2 border-white hover:bg-white hover:text-secondary text-white px-8 py-4 rounded-full font-bold text-lg transition flex items-center justify-center gap-2 shimmer">
                    <i class="fas fa-info-circle"></i> รายละเอียด
                </a>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 bounce">
            <a href="#about" class="text-white text-3xl opacity-70 hover:opacity-100 transition">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Countdown Section -->
    <section class="py-10 bg-gradient-to-r from-secondary via-primary to-secondary text-white -mt-20 relative z-20 mx-4 md:mx-20 rounded-xl shadow-2xl fade-in-up overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMiIgZmlsbD0iI0ZGRDcwMCIvPjwvc3ZnPg==')] animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-6 text-center relative z-10">
            <h3 class="text-2xl md:text-3xl font-bold mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-stopwatch text-accent text-4xl bounce"></i>
                นับถอยหลังสู่วันงาน
            </h3>
            <p class="text-sm md:text-base mb-6 text-gray-200">⏰ เตรียมตัวให้พร้อม!</p>
            <div id="countdown" class="flex flex-wrap justify-center gap-4 md:gap-10">
                <div class="bg-white/70 backdrop-blur-md p-4 md:p-6 rounded-xl w-24 md:w-32 border-2 border-accent/30 hover:scale-110 transition transform">
                    <span id="days" class="block text-4xl md:text-6xl font-bold text-accent gradient-text">00</span>
                    <span class="text-sm md:text-base font-semibold text-gray-600">วัน</span>
                </div>
                <div class="bg-white/70 backdrop-blur-md p-4 md:p-6 rounded-xl w-24 md:w-32 border-2 border-accent/30 hover:scale-110 transition transform">
                    <span id="hours" class="block text-4xl md:text-6xl font-bold text-accent gradient-text">00</span>
                    <span class="text-sm md:text-base font-semibold text-gray-600">ชั่วโมง</span>
                </div>
                <div class="bg-white/70 backdrop-blur-md p-4 md:p-6 rounded-xl w-24 md:w-32 border-2 border-accent/30 hover:scale-110 transition transform">
                    <span id="minutes" class="block text-4xl md:text-6xl font-bold text-accent gradient-text">00</span>
                    <span class="text-sm md:text-base font-semibold text-gray-600">นาที</span>
                </div>
                <div class="bg-white/70 backdrop-blur-md p-4 md:p-6 rounded-xl w-24 md:w-32 border-2 border-accent/30 hover:scale-110 transition transform">
                    <span id="seconds" class="block text-4xl md:text-6xl font-bold text-accent gradient-text">00</span>
                    <span class="text-sm md:text-base font-semibold text-gray-600">วินาที</span>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-center gap-2 text-accent">
                <i class="fas fa-fire bounce"></i>
                <span class="font-bold">ที่นั่งจำกัด! รีบสมัครเลย</span>
                <i class="fas fa-fire bounce" style="animation-delay: 0.5s;"></i>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-accent/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 fade-in-up">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-primary to-accent rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-1000"></div>
                        <img src="assets/images/shirt.jpeg" alt="Running Shirt" class="relative rounded-2xl shadow-2xl hover-scale w-full object-cover h-96 border-4 border-white">
                        <div class="absolute top-4 right-4 bg-primary text-white px-4 py-2 rounded-full font-bold shadow-lg animate-pulse">
                            ✨ Limited Edition
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 fade-in-up">
                    <div class="inline-block mb-4">
                        <span class="bg-gradient-to-r from-primary to-orange-600 text-white px-4 py-2 rounded-full font-bold text-sm">
                            🏃‍♂️ เกี่ยวกับงาน
                        </span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">
                        <span class="gradient-text">วิ่งเพื่อสุขภาพ</span><br>
                        <span class="text-gray-900">และสืบสานตำนาน</span>
                    </h2>
                    <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                        <span class="font-bold text-primary">Phichai Run 2026</span> กลับมาอีกครั้งกับความยิ่งใหญ่กว่าเดิม! 🎉 เชิญชวนนักวิ่งทุกท่านมาร่วมสัมผัสบรรยากาศการวิ่งในเส้นทางประวัติศาสตร์ ผ่านสถานที่สำคัญของอำเภอพิชัย จังหวัดอุตรดิตถ์
                    </p>
                    <div class="bg-accent/10 border-l-4 border-accent p-4 rounded-r-lg mb-8">
                        <p class="text-gray-700 leading-relaxed">
                            💝 <span class="font-semibold text-secondary">รายได้ส่วนหนึ่ง</span>หลังหักค่าใช้จ่าย จะนำไปสมทบทุนบูรณะศาลพระยาพิชัยดาบหัก และสนับสนุนอุปกรณ์กีฬาให้กับโรงเรียนในพื้นที่
                        </p>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white group-hover:scale-125 transition">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <span class="text-gray-700 text-lg">
                                <span class="font-bold text-primary">เสื้อวิ่ง</span>ดีไซน์พิเศษ Limited Edition ✨
                            </span>
                        </li>
                        <li class="flex items-start gap-3 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white group-hover:scale-125 transition">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <span class="text-gray-700 text-lg">
                                <span class="font-bold text-primary">เหรียญรางวัล</span>ผู้พิชิตชัย 🏅
                            </span>
                        </li>
                        <li class="flex items-start gap-3 group">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white group-hover:scale-125 transition">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <span class="text-gray-700 text-lg">
                                <span class="font-bold text-primary">อาหารและเครื่องดื่ม</span>จัดเต็มตลอดงาน 🍽️
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-20 left-10 text-9xl">🏃</div>
            <div class="absolute top-40 right-20 text-9xl">🏅</div>
            <div class="absolute bottom-20 left-1/4 text-9xl">⚡</div>
            <div class="absolute bottom-40 right-1/3 text-9xl">🎯</div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16 fade-in-up">
                <div class="inline-block mb-4">
                    <span class="bg-gradient-to-r from-primary to-orange-600 text-white px-6 py-3 rounded-full font-bold text-lg shadow-lg">
                        🏆 ประเภทการแข่งขัน
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    <span class="gradient-text">เลือกระยะ</span>ที่เหมาะกับคุณ
                </h2>
                <div class="w-32 h-1.5 bg-gradient-to-r from-primary via-accent to-primary mx-auto rounded-full"></div>
                <p class="mt-4 text-gray-600 text-lg">🎯 มี 5 ระยะให้เลือก เหมาะกับนักวิ่งทุกระดับ</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 md:gap-8">
                <!-- Student Run 3.5 km -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 fade-in-up transform hover:-translate-y-3 border-t-4 border-accent">
                    <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 h-2"></div>
                    <div class="p-6 text-center relative">
                        <div class="absolute top-0 right-0 bg-accent text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                            STUDENT
                        </div>
                        <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center mx-auto mb-4 text-yellow-600 text-3xl medal-shine shadow-lg">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">Student Run</h3>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-600 to-yellow-800 mb-2">3.5 km</p>
                        <p class="text-sm text-gray-500 mb-4">เหมาะสำหรับ<br>นักเรียน/นักศึกษา 🎓</p>
                        <a href="register.php" class="inline-block bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white py-2 px-6 rounded-full font-bold transition transform hover:scale-105 shadow-md">
                            สมัครเลย!
                        </a>
                    </div>
                </div>

                <!-- Charity Walk 3.5 km -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 fade-in-up transform hover:-translate-y-3 border-t-4 border-green-500">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 h-2"></div>
                    <div class="p-6 text-center relative">
                        <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                            CHARITY
                        </div>
                        <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600 text-3xl medal-shine shadow-lg">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">เดินการกุศล</h3>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-green-800 mb-2">3.5 km</p>
                        <p class="text-sm text-gray-500 mb-4">ร่วมสมทบทุน<br>บูรณะและสนับสนุน 💝</p>
                        <a href="register.php" class="inline-block bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-2 px-6 rounded-full font-bold transition transform hover:scale-105 shadow-md">
                            ร่วมบุญ!
                        </a>
                    </div>
                </div>

                <!-- Fun Run 5 km -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 fade-in-up transform hover:-translate-y-3 border-t-4 border-blue-500">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 h-2"></div>
                    <div class="p-6 text-center relative">
                        <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                            FUN RUN
                        </div>
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600 text-3xl medal-shine shadow-lg">
                            <i class="fas fa-running"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">Fun Run</h3>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800 mb-2">5 km</p>
                        <p class="text-sm text-gray-500 mb-4">เดิน-วิ่ง<br>เพื่อสุขภาพ 🏃‍♂️</p>
                        <a href="register.php" class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-2 px-6 rounded-full font-bold transition transform hover:scale-105 shadow-md">
                            วิ่งกัน!
                        </a>
                    </div>
                </div>

                <!-- Mini Marathon 11.5 km -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 fade-in-up transform hover:-translate-y-3 border-4 border-primary relative scale-105">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-primary to-orange-600 text-white text-xs font-bold px-6 py-2 rounded-full shadow-lg z-10 animate-pulse">
                        ⭐ POPULAR ⭐
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 via-primary to-red-600 h-2"></div>
                    <div class="p-6 text-center relative bg-gradient-to-b from-orange-50 to-white">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-200 to-red-200 rounded-full flex items-center justify-center mx-auto mb-4 text-primary text-3xl medal-shine shadow-lg glow">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">Mini Marathon</h3>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-orange-600 mb-2">11.5 km</p>
                        <p class="text-sm text-gray-600 mb-4 font-semibold">การแข่งขัน<br>มินิมาราธอน 🔥</p>
                        <a href="register.php" class="inline-block bg-gradient-to-r from-primary to-orange-600 hover:from-orange-600 hover:to-primary text-white py-3 px-6 rounded-full font-bold transition transform hover:scale-110 shadow-lg sparkle-btn">
                            ท้าทายเลย!
                        </a>
                    </div>
                </div>

                <!-- Merch Shirt 250 THB -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 fade-in-up transform hover:-translate-y-3 border-t-4 border-yellow-400">
                    <div class="bg-gradient-to-br from-yellow-300 to-yellow-500 h-2"></div>
                    <div class="p-6 text-center relative">
                        <div class="absolute top-0 right-0 bg-yellow-400 text-gray-900 text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                            MERCH
                        </div>
                        <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center mx-auto mb-4 text-yellow-600 text-3xl medal-shine shadow-lg">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">เสื้อที่ระลึก</h3>
                        <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-600 to-orange-600 mb-2">250 บาท</p>
                        <p class="text-sm text-gray-500 mb-4">รับเสื้อดีไซน์พิเศษ<br>Limited Edition 👕</p>
                        <a href="register.php" class="inline-block bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-gray-900 py-2 px-6 rounded-full font-bold transition transform hover:scale-105 shadow-md">
                            สั่งซื้อ!
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 text-center fade-in-up">
                <div class="inline-block bg-gradient-to-r from-primary/10 to-accent/10 px-8 py-4 rounded-2xl border-2 border-primary/20">
                    <p class="text-gray-700 text-lg">
                        💡 <span class="font-bold text-primary">ทุกระยะ</span>ได้รับ: เสื้อวิ่ง + เหรียญ + อาหาร + ประกันอุบัติเหตุ + ของที่ระลึก
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="py-20 bg-gradient-to-br from-secondary via-gray-900 to-secondary text-white relative overflow-hidden">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIgZmlsbD0iI0ZGRDcwMCIvPjwvc3ZnPg==')]"></div>
        </div>

        <!-- Decorative Gradient Circles -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-accent/20 rounded-full blur-3xl"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16 fade-in-up">
                <div class="inline-block mb-4">
                    <span class="bg-gradient-to-r from-accent to-yellow-500 text-gray-900 px-6 py-3 rounded-full font-bold text-lg shadow-lg">
                        📋 กำหนดการ
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    <span class="gradient-text">ตารางงาน</span> วันแข่งขัน
                </h2>
                <p class="text-xl text-gray-300">📅 วันอาทิตย์ที่ <span class="text-accent font-bold">14 กุมภาพันธ์ 2569</span></p>
                <div class="mt-4 inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full">
                    <i class="fas fa-map-marker-alt text-primary"></i>
                    <span class="text-sm">จุดเริ่มต้น: อนุสาวรีย์พระยาพิชัยดาบหัก</span>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="relative border-l-4 border-gradient-to-b from-accent via-primary to-accent ml-3 md:ml-6 space-y-8">
                    <!-- Timeline Items -->
                    <!-- Item 1 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-accent to-yellow-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg glow">
                            <i class="fas fa-box text-xs text-gray-900"></i>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-6 rounded-xl border border-white/20 hover:bg-white/20 transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>04:00
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2 flex items-center gap-2">
                                        เปิดจุดรับฝากของ 
                                        <span class="text-xs bg-accent text-gray-900 px-2 py-1 rounded-full">START</span>
                                    </h4>
                                    <p class="text-gray-300">📦 ณ บริเวณลานหน้าอนุสาวรีย์พระยาพิชัยดาบหัก</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-blue-400 to-blue-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg">
                            <i class="fas fa-dumbbell text-xs text-white"></i>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-6 rounded-xl border border-white/20 hover:bg-white/20 transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>04:45
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2">วอร์มอัพร่างกาย 🤸‍♂️</h4>
                                    <p class="text-gray-300">💪 นำวอร์มโดยเทรนเนอร์มืออาชีพ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-red-500 to-orange-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg animate-pulse">
                            <i class="fas fa-running text-xs text-white"></i>
                        </div>
                        <div class="bg-gradient-to-r from-red-500/20 to-orange-500/20 backdrop-blur-sm p-6 rounded-xl border-2 border-primary hover:border-accent transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>05:00
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2 flex items-center gap-2">
                                        ปล่อยตัว Half Marathon 21 KM 
                                        <span class="text-xs bg-primary text-white px-2 py-1 rounded-full">🔥</span>
                                    </h4>
                                    <p class="text-gray-200">🏃‍♂️ สำหรับนักวิ่งระดับมาราธอน</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-orange-400 to-red-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg">
                            <i class="fas fa-medal text-xs text-white"></i>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-6 rounded-xl border border-primary hover:bg-white/20 transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>05:30
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2">ปล่อยตัว Mini Marathon 10.5 KM 🏅</h4>
                                    <p class="text-gray-300">⭐ ระยะยอดนิยม!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-blue-400 to-blue-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg">
                            <i class="fas fa-running text-xs text-white"></i>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-6 rounded-xl border border-white/20 hover:bg-white/20 transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>06:00
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2">ปล่อยตัว Fun Run 5 KM 🎉</h4>
                                    <p class="text-gray-300">🌟 เหมาะกับทุกคน!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="relative pl-8 md:pl-12 fade-in-up group">
                        <div class="absolute -left-4 top-0 bg-gradient-to-br from-accent to-yellow-600 w-8 h-8 rounded-full border-4 border-secondary flex items-center justify-center shadow-lg glow">
                            <i class="fas fa-trophy text-xs text-gray-900"></i>
                        </div>
                        <div class="bg-gradient-to-r from-accent/20 to-yellow-500/20 backdrop-blur-sm p-6 rounded-xl border-2 border-accent hover:border-yellow-400 transition group-hover:scale-105 duration-300">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                <span class="text-3xl font-bold text-accent w-32 flex items-center gap-2">
                                    <i class="fas fa-clock text-2xl"></i>07:30
                                </span>
                                <div class="flex-1">
                                    <h4 class="text-xl md:text-2xl font-bold mb-2 flex items-center gap-2">
                                        พิธีมอบรางวัล 🏆
                                        <span class="text-xs bg-accent text-gray-900 px-2 py-1 rounded-full">FINISH</span>
                                    </h4>
                                    <p class="text-gray-200">🍽️ และรับประทานอาหารเช้า</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 text-center fade-in-up">
                <div class="inline-block bg-white/10 backdrop-blur-sm px-8 py-4 rounded-2xl border border-white/20">
                    <p class="text-lg">
                        ⚠️ <span class="font-bold text-accent">สำคัญ:</span> กรุณามาถึงก่อนเวลาเริ่มแต่ละระยะ 30 นาที เพื่อรับของและเตรียมตัว
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration CTA -->
    <section id="register" class="py-20 bg-gradient-to-br from-primary via-orange-600 to-red-700 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <!-- Animated Shapes -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-accent/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-yellow-300/20 rounded-full blur-3xl floating"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-white/10 rounded-full blur-2xl floating" style="animation-delay: 1s;"></div>

        <!-- Running Icons -->
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute top-20 left-1/4 text-6xl rotate-12">🏃‍♂️</div>
            <div class="absolute bottom-20 right-1/4 text-6xl -rotate-12">🏃‍♀️</div>
            <div class="absolute top-1/3 right-1/3 text-5xl">🏅</div>
            <div class="absolute bottom-1/3 left-1/3 text-5xl">⚡</div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="mb-6 fade-in-up">
                <i class="fas fa-fire text-6xl text-accent bounce"></i>
            </div>
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 fade-in-up text-shadow">
                พร้อมที่จะ<span class="text-accent">ท้าทาย</span>ตัวเองหรือยัง?
            </h2>
            <p class="text-xl md:text-2xl text-white mb-10 max-w-3xl mx-auto fade-in-up leading-relaxed">
                🎯 อย่ารอช้า! จำนวนจำกัด สมัครวันนี้เพื่อรับราคา <span class="font-bold text-accent">Early Bird</span> และของที่ระลึกสุดพิเศษ ✨
            </p>

            <!-- CTA Button with Extra Effects -->
            <div class="fade-in-up mb-8">
                <a href="register.php" class="inline-block bg-white text-primary hover:bg-accent hover:text-gray-900 text-xl md:text-2xl font-bold py-5 px-12 md:px-16 rounded-full shadow-2xl transition transform hover:scale-110 sparkle-btn relative overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-3">
                        <i class="fas fa-rocket"></i>
                        สมัครออนไลน์ทันที
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition"></i>
                    </span>
                </a>
            </div>

            <!-- Additional Benefits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-8 fade-in-up">
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl border border-white/20 hover:bg-white/20 transition">
                    <i class="fas fa-tshirt text-4xl text-accent mb-3"></i>
                    <h3 class="font-bold text-white text-lg mb-2">เสื้อพิเศษ</h3>
                    <p class="text-white/80 text-sm">Limited Edition Design</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl border border-white/20 hover:bg-white/20 transition">
                    <i class="fas fa-medal text-4xl text-accent mb-3"></i>
                    <h3 class="font-bold text-white text-lg mb-2">เหรียญรางวัล</h3>
                    <p class="text-white/80 text-sm">สำหรับผู้เข้าร่วมทุกคน</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl border border-white/20 hover:bg-white/20 transition">
                    <i class="fas fa-gift text-4xl text-accent mb-3"></i>
                    <h3 class="font-bold text-white text-lg mb-2">ของที่ระลึก</h3>
                    <p class="text-white/80 text-sm">และอาหารฟรี!</p>
                </div>
            </div>

            <div class="fade-in-up">
                <p class="text-white/90 text-sm md:text-base font-semibold bg-white/10 backdrop-blur-sm inline-block px-6 py-3 rounded-full border border-white/20">
                    ⏰ *ปิดรับสมัครวันที่ <span class="text-accent font-bold">31 ธันวาคม 2568</span> หรือจนกว่าจะเต็ม
                </p>
            </div>

            <!-- Counter Stats -->
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto fade-in-up">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-accent mb-2">1000+</div>
                    <div class="text-white/80 text-sm">นักวิ่งเข้าร่วม</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-accent mb-2">5</div>
                    <div class="text-white/80 text-sm">ระยะการแข่งขัน</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-accent mb-2">21</div>
                    <div class="text-white/80 text-sm">กิโลเมตร (สูงสุด)</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-accent mb-2">100%</div>
                    <div class="text-white/80 text-sm">ความสนุก!</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white pt-16 pb-8 relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl md:text-3xl font-bold mb-4 flex items-center gap-3">
                        <img src="assets/images/logo-1.jpeg" class="h-10 w-10 rounded-full shadow-lg glow"> 
                        <span class="gradient-text">PHICHAI RUN 2026</span>
                    </h3>
                    <p class="text-gray-400 mb-6 max-w-md leading-relaxed">
                        🏃‍♂️ งานวิ่งการกุศลเพื่อสุขภาพและสังคม จัดโดย<span class="text-accent font-semibold">ชมรมวิ่งอำเภอพิชัย</span> ร่วมกับเทศบาลตำบลพิชัย มาร่วมเป็นส่วนหนึ่งของประวัติศาสตร์หน้าใหม่
                    </p>
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 mb-3">🤝 ผู้สนับสนุนหลัก:</p>
                        <div class="flex gap-4 flex-wrap">
                            <img src="assets/images/logo-2.jpeg" class="h-16 w-auto rounded-lg bg-white p-2 object-contain hover:scale-110 transition shadow-lg">
                            <img src="assets/images/logo-3.jpeg" class="h-16 w-auto rounded-lg bg-white p-2 object-contain hover:scale-110 transition shadow-lg">
                            <img src="assets/images/logo-4.jpeg" class="h-16 w-auto rounded-lg bg-white p-2 object-contain hover:scale-110 transition shadow-lg">
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/phichairun2026/" target="_blank" class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center hover:scale-110 transition shadow-lg glow">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center hover:scale-110 transition shadow-lg">
                            <i class="fab fa-line text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-600 to-purple-600 flex items-center justify-center hover:scale-110 transition shadow-lg">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-6 text-gray-200 flex items-center gap-2">
                        <i class="fas fa-link text-accent"></i> ลิงก์ด่วน
                    </h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#home" class="hover:text-accent transition flex items-center gap-2 group">
                            <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition"></i>หน้าแรก
                        </a></li>
                        <li><a href="#about" class="hover:text-accent transition flex items-center gap-2 group">
                            <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition"></i>รายละเอียด
                        </a></li>
                        <li><a href="#categories" class="hover:text-accent transition flex items-center gap-2 group">
                            <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition"></i>ประเภทการแข่งขัน
                        </a></li>
                        <li><a href="#register" class="hover:text-accent transition flex items-center gap-2 group">
                            <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition"></i>สมัครวิ่ง
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-6 text-gray-200 flex items-center gap-2">
                        <i class="fas fa-envelope text-accent"></i> ติดต่อเรา
                    </h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start gap-3 group">
                            <i class="fas fa-map-marker-alt mt-1 text-primary group-hover:text-accent transition"></i>
                            <span class="text-sm leading-relaxed">โรงเรียนพิชัย<br>9/9 หมู่ 3 ต.ในเมือง อ.พิชัย<br>จ.อุตรดิตถ์ 53120</span>
                        </li>
                        <li class="flex items-center gap-3 group">
                            <i class="fas fa-phone text-primary group-hover:text-accent transition"></i>
                            <a href="tel:055-421-402" class="hover:text-accent transition">055-421-402</a>
                        </li>
                        <li class="flex items-center gap-3 group">
                            <i class="fas fa-fax text-primary group-hover:text-accent transition"></i>
                            <span>055-421-406</span>
                        </li>
                        <li class="flex items-center gap-3 group">
                            <i class="fas fa-envelope text-primary group-hover:text-accent transition"></i>
                            <a href="mailto:phichaischool@gmail.com" class="hover:text-accent transition">phichaischool@gmail.com</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
                    <p class="text-gray-500 text-sm">
                        &copy; 2025 <span class="text-accent font-semibold">Phichai Run</span>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-2 text-gray-500 text-sm">
                        <span>Made with</span>
                        <i class="fas fa-heart text-red-500 animate-pulse"></i>
                        <span>for runners</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
