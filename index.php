<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phichai Run 2026 - วิ่งพิชัยดาบหัก</title>
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
            <a href="#" class="flex items-center gap-3 group">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary to-accent rounded-full blur opacity-75 group-hover:opacity-100 transition duration-200"></div>
                    <img src="assets/images/logo-1.png" alt="Logo" class="relative h-10 w-10 rounded-full border-2 border-white object-cover">
                </div>
                <span class="font-bold text-xl tracking-wide text-secondary group-hover:text-primary transition">PHICHAI RUN <span class="text-primary">2026</span></span>
            </a>
            
            <div class="hidden md:flex items-center space-x-8 font-medium text-gray-600">
                <a href="#home" class="hover:text-primary transition relative group py-2">
                    หน้าแรก
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#about" class="hover:text-primary transition relative group py-2">
                    รายละเอียด
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#categories" class="hover:text-primary transition relative group py-2">
                    ระยะทาง
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#schedule" class="hover:text-primary transition relative group py-2">
                    กำหนดการ
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#contact" class="hover:text-primary transition relative group py-2">
                    ติดต่อเรา
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <a href="register.php" class="bg-primary hover:bg-red-600 text-white px-6 py-2.5 rounded-full font-bold transition transform hover:scale-105 shadow-lg hover:shadow-red-500/30 flex items-center gap-2">
                    <i class="fas fa-running"></i> สมัครวิ่ง
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl text-secondary hover:text-primary transition">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden absolute right-6 top-24 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl w-72 p-6 border border-gray-100 transform origin-top-right transition-all duration-300">
            <nav class="flex flex-col space-y-2">
                <a href="#home" class="flex items-center gap-3 py-3 px-4 rounded-xl hover:bg-red-50 text-gray-700 font-medium transition">
                    <i class="fas fa-home text-primary w-6"></i> หน้าแรก
                </a>
                <a href="#about" class="flex items-center gap-3 py-3 px-4 rounded-xl hover:bg-red-50 text-gray-700 font-medium transition">
                    <i class="fas fa-info-circle text-primary w-6"></i> รายละเอียด
                </a>
                <a href="#categories" class="flex items-center gap-3 py-3 px-4 rounded-xl hover:bg-red-50 text-gray-700 font-medium transition">
                    <i class="fas fa-route text-primary w-6"></i> ระยะทาง
                </a>
                <a href="#schedule" class="flex items-center gap-3 py-3 px-4 rounded-xl hover:bg-red-50 text-gray-700 font-medium transition">
                    <i class="fas fa-calendar-alt text-primary w-6"></i> กำหนดการ
                </a>
                <a href="#contact" class="flex items-center gap-3 py-3 px-4 rounded-xl hover:bg-red-50 text-gray-700 font-medium transition">
                    <i class="fas fa-envelope text-primary w-6"></i> ติดต่อเรา
                </a>
                <hr class="border-gray-100 my-2">
                <a href="register.php" class="bg-gradient-to-r from-primary to-red-600 text-white py-3 rounded-xl text-center font-bold shadow-lg hover:shadow-red-500/30 transition">
                    สมัครวิ่งทันที
                </a>
            </nav>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden hero-gradient clip-path-slant pb-20">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-accent/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-0 left-1/3 w-full h-64 bg-gradient-to-t from-black/50 to-transparent"></div>
            <!-- Pattern Overlay -->
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 pt-20 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-accent mb-8 animate-fade-in-up">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-accent"></span>
                </span>
                <span class="font-medium tracking-wide text-sm md:text-base">14 กุมภาพันธ์ 2569</span>
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold text-white mb-6 leading-tight tracking-tight drop-shadow-2xl">
                PHICHAI RUN <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-red-400 to-accent">2026</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-3xl mx-auto font-light leading-relaxed">
                วิ่งท้าประวัติศาสตร์ ณ ดินแดนท่านพ่อพระยาพิชัยดาบหัก <br class="hidden md:block">
                <span class="text-accent font-medium">"กล้าหาญ เสียสละ รักสามัคคี"</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="register.php" class="group relative px-8 py-4 bg-primary text-white rounded-full font-bold text-lg shadow-xl hover:shadow-red-600/40 transition-all hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <span class="relative flex items-center gap-2">
                        สมัครเข้าร่วม <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </a>
                <a href="#about" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/30 text-white rounded-full font-bold text-lg hover:bg-white/20 transition-all hover:-translate-y-1">
                    รายละเอียดงาน
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#about" class="text-white/50 hover:text-white transition">
                <i class="fas fa-chevron-down text-3xl"></i>
            </a>
        </div>
    </section>

    <!-- Countdown Section -->
    <section class="relative z-20 -mt-24 pb-20 px-4">
        <div class="container mx-auto">
            <div class="glass-card rounded-3xl p-8 md:p-12 max-w-5xl mx-auto text-center relative overflow-hidden">
                <!-- Decorative Glow -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-2/3 h-20 bg-primary/20 blur-3xl rounded-full"></div>

                <h3 class="text-2xl md:text-3xl font-bold mb-8 flex items-center justify-center gap-3 text-secondary">
                    <i class="fas fa-stopwatch text-primary text-4xl animate-pulse-slow"></i>
                    นับถอยหลังสู่วันงาน
                </h3>
                
                <div id="countdown" class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                    <div class="bg-white/50 rounded-2xl p-4 border border-white/60 shadow-sm">
                        <span id="days" class="block text-4xl md:text-6xl font-bold text-primary mb-1">00</span>
                        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">วัน</span>
                    </div>
                    <div class="bg-white/50 rounded-2xl p-4 border border-white/60 shadow-sm">
                        <span id="hours" class="block text-4xl md:text-6xl font-bold text-primary mb-1">00</span>
                        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">ชั่วโมง</span>
                    </div>
                    <div class="bg-white/50 rounded-2xl p-4 border border-white/60 shadow-sm">
                        <span id="minutes" class="block text-4xl md:text-6xl font-bold text-primary mb-1">00</span>
                        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">นาที</span>
                    </div>
                    <div class="bg-white/50 rounded-2xl p-4 border border-white/60 shadow-sm">
                        <span id="seconds" class="block text-4xl md:text-6xl font-bold text-primary mb-1">00</span>
                        <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">วินาที</span>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-center gap-2 text-red-600 font-medium bg-red-50 inline-block px-6 py-2 rounded-full">
                    <i class="fas fa-fire"></i> ที่นั่งจำกัด! รีบสมัครเลย
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <!-- Image Side -->
                <div class="lg:w-1/2 relative group">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary to-accent rounded-[2rem] blur-lg opacity-30 group-hover:opacity-50 transition duration-500"></div>
                    <div class="relative rounded-[2rem] overflow-hidden shadow-2xl transform group-hover:scale-[1.02] transition duration-500">
                        <img src="assets/images/shirt.jpeg" alt="Running Shirt" class="w-full h-auto object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-8">
                            <span class="bg-accent text-white text-xs font-bold px-3 py-1 rounded-full mb-2 inline-block">LIMITED EDITION</span>
                            <p class="text-white font-medium">เสื้อวิ่งดีไซน์พิเศษสำหรับปี 2026</p>
                        </div>
                    </div>
                    <!-- Floating Badge -->
                    <div class="absolute -top-6 -right-6 bg-white p-4 rounded-2xl shadow-xl animate-float hidden md:block">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-3 rounded-full text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase">Status</p>
                                <p class="font-bold text-gray-800">Open for Reg.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Side -->
                <div class="lg:w-1/2">
                    <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">About The Event</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-secondary mb-6 leading-tight">
                        วิ่งเพื่อสุขภาพ <br>
                        <span class="text-primary relative inline-block">
                            สืบสานตำนาน
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-accent opacity-50" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="8" fill="none" />
                            </svg>
                        </span>
                    </h2>
                    
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Phichai Run 2026 กลับมาอีกครั้งกับความยิ่งใหญ่กว่าเดิม! เชิญชวนนักวิ่งทุกท่านมาร่วมสัมผัสบรรยากาศการวิ่งในเส้นทางประวัติศาสตร์ ผ่านสถานที่สำคัญของอำเภอพิชัย จังหวัดอุตรดิตถ์
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition duration-300">
                                <i class="fas fa-heart text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-800 mb-1">รายได้เพื่อการกุศล</h4>
                                <p class="text-gray-500">สมทบทุนบูรณะศาลพระยาพิชัยดาบหัก และสนับสนุนอุปกรณ์กีฬา</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition duration-300">
                                <i class="fas fa-tshirt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-800 mb-1">ของที่ระลึกสุดพิเศษ</h4>
                                <p class="text-gray-500">เสื้อวิ่ง Limited Edition และเหรียญรางวัลผู้พิชิตชัย</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition duration-300">
                                <i class="fas fa-utensils text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-800 mb-1">อาหารและเครื่องดื่ม</h4>
                                <p class="text-gray-500">จัดเต็มตลอดงานสำหรับนักวิ่งทุกท่าน</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-20 bg-gray-50 relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">Race Categories</span>
                <h2 class="text-4xl md:text-5xl font-bold text-secondary mb-4">
                    ประเภทการแข่งขัน
                </h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-primary to-accent mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Student Run -->
                <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 bg-accent text-white text-xs font-bold px-3 py-1 rounded-bl-xl">STUDENT</div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Student Run</h3>
                    <p class="text-4xl font-bold text-primary mb-4">3.5 <span class="text-lg text-gray-500 font-normal">km</span></p>
                    <p class="text-gray-500 mb-6 text-sm">เหมาะสำหรับนักเรียน/นักศึกษา วิ่งระยะสั้นเพื่อสุขภาพ</p>
                    <a href="register.php" class="block w-full py-3 rounded-xl border-2 border-yellow-500 text-yellow-600 font-bold text-center hover:bg-yellow-500 hover:text-white transition">
                        สมัครเลย
                    </a>
                </div>

                <!-- Charity Walk -->
                <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl">CHARITY</div>
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 text-2xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">เดินการกุศล</h3>
                    <p class="text-4xl font-bold text-primary mb-4">3.5 <span class="text-lg text-gray-500 font-normal">km</span></p>
                    <p class="text-gray-500 mb-6 text-sm">ร่วมสมทบทุนบูรณะและสนับสนุนกิจกรรมสาธารณกุศล</p>
                    <a href="register.php" class="block w-full py-3 rounded-xl border-2 border-green-500 text-green-600 font-bold text-center hover:bg-green-500 hover:text-white transition">
                        ร่วมบุญ
                    </a>
                </div>

                <!-- Fun Run -->
                <div class="bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl">FUN RUN</div>
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Fun Run</h3>
                    <p class="text-4xl font-bold text-primary mb-4">5 <span class="text-lg text-gray-500 font-normal">km</span></p>
                    <p class="text-gray-500 mb-6 text-sm">เดิน-วิ่งเพื่อสุขภาพ ระยะกำลังดี สนุกสนานได้ทุกคน</p>
                    <a href="register.php" class="block w-full py-3 rounded-xl border-2 border-blue-500 text-blue-600 font-bold text-center hover:bg-blue-500 hover:text-white transition">
                        วิ่งกัน!
                    </a>
                </div>

                <!-- Mini Marathon -->
                <div class="bg-gradient-to-br from-primary to-red-700 rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden group text-white scale-105 z-10">
                    <div class="absolute top-0 right-0 bg-accent text-gray-900 text-xs font-bold px-3 py-1 rounded-bl-xl">POPULAR</div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Mini Marathon</h3>
                    <p class="text-4xl font-bold mb-4">11.5 <span class="text-lg opacity-80 font-normal">km</span></p>
                    <p class="opacity-90 mb-6 text-sm">ท้าทายขีดจำกัดกับการวิ่งมินิมาราธอน เส้นทางสวยงาม</p>
                    <a href="register.php" class="block w-full py-3 rounded-xl bg-white text-primary font-bold text-center hover:bg-gray-100 transition shadow-lg">
                        สมัครเลย
                    </a>
                </div>
            </div>

            <!-- Merch Item -->
            <div class="mt-12 max-w-md mx-auto bg-white rounded-3xl p-6 shadow-lg border border-yellow-200 flex items-center gap-6 hover:shadow-xl transition">
                <div class="w-20 h-20 bg-yellow-100 rounded-2xl flex items-center justify-center text-yellow-600 text-3xl flex-shrink-0">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800">เสื้อที่ระลึก (Merch)</h3>
                    <p class="text-gray-500 text-sm">Limited Edition Design</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-primary">250฿</p>
                    <a href="register.php" class="text-sm font-bold text-yellow-600 hover:underline">สั่งซื้อ ></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="py-20 bg-secondary text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')]"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <span class="text-accent font-bold tracking-wider uppercase text-sm mb-2 block">Event Schedule</span>
                <h2 class="text-4xl md:text-5xl font-bold mb-4">กำหนดการวันแข่งขัน</h2>
                <p class="text-xl text-gray-300">📅 14 กุมภาพันธ์ 2569 ณ อนุสาวรีย์พระยาพิชัยดาบหัก</p>
            </div>

            <div class="max-w-4xl mx-auto relative">
                <!-- Vertical Line -->
                <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-white/20 -translate-x-1/2"></div>

                <!-- Timeline Items -->
                <div class="space-y-12">
                    <!-- Item 1 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 md:text-right pl-12 md:pl-0">
                            <h4 class="text-2xl font-bold text-accent mb-1">04:00</h4>
                            <h5 class="text-xl font-bold mb-2">เปิดจุดรับฝากของ</h5>
                            <p class="text-gray-400 text-sm">ณ บริเวณลานหน้าอนุสาวรีย์พระยาพิชัยดาบหัก</p>
                        </div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-primary rounded-full border-4 border-secondary z-10 group-hover:scale-150 transition duration-300"></div>
                        <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-accent rounded-full border-4 border-secondary z-10 group-hover:scale-150 transition duration-300"></div>
                        <div class="md:w-1/2 md:pl-12 pl-12 md:text-left">
                            <h4 class="text-2xl font-bold text-accent mb-1">04:45</h4>
                            <h5 class="text-xl font-bold mb-2">วอร์มอัพร่างกาย</h5>
                            <p class="text-gray-400 text-sm">นำวอร์มโดยเทรนเนอร์มืออาชีพ เตรียมความพร้อมก่อนวิ่ง</p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 md:text-right pl-12 md:pl-0">
                            <h4 class="text-2xl font-bold text-primary mb-1">05:00</h4>
                            <h5 class="text-xl font-bold mb-2">ปล่อยตัว Half Marathon 21 KM</h5>
                            <span class="inline-block bg-primary/20 text-primary text-xs font-bold px-2 py-1 rounded border border-primary/50">ระยะไกล</span>
                        </div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-6 h-6 bg-primary rounded-full border-4 border-secondary z-10 animate-pulse"></div>
                        <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
                    </div>

                    <!-- Item 4 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-white rounded-full border-4 border-secondary z-10 group-hover:scale-150 transition duration-300"></div>
                        <div class="md:w-1/2 md:pl-12 pl-12 md:text-left">
                            <h4 class="text-2xl font-bold text-accent mb-1">05:30</h4>
                            <h5 class="text-xl font-bold mb-2">ปล่อยตัว Mini Marathon 10.5 KM</h5>
                            <p class="text-gray-400 text-sm">ระยะยอดนิยมสำหรับนักวิ่ง</p>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 md:text-right pl-12 md:pl-0">
                            <h4 class="text-2xl font-bold text-accent mb-1">06:00</h4>
                            <h5 class="text-xl font-bold mb-2">ปล่อยตัว Fun Run 5 KM</h5>
                            <p class="text-gray-400 text-sm">เดิน-วิ่ง เพื่อสุขภาพ สนุกสนาน</p>
                        </div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-white rounded-full border-4 border-secondary z-10 group-hover:scale-150 transition duration-300"></div>
                        <div class="md:w-1/2 md:pl-12 hidden md:block"></div>
                    </div>

                    <!-- Item 6 -->
                    <div class="relative flex flex-col md:flex-row items-center justify-between group">
                        <div class="md:w-1/2 md:pr-12 hidden md:block"></div>
                        <div class="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 bg-accent rounded-full border-4 border-secondary z-10 group-hover:scale-150 transition duration-300"></div>
                        <div class="md:w-1/2 md:pl-12 pl-12 md:text-left">
                            <h4 class="text-2xl font-bold text-accent mb-1">07:30</h4>
                            <h5 class="text-xl font-bold mb-2">พิธีมอบรางวัล</h5>
                            <p class="text-gray-400 text-sm">และรับประทานอาหารเช้า</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration CTA -->
    <section id="register" class="py-20 relative overflow-hidden bg-primary">
        <!-- Background -->
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-primary via-red-600 to-secondary opacity-90"></div>
        
        <!-- Animated Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-accent/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="mb-8">
                <i class="fas fa-fire text-6xl text-accent animate-bounce"></i>
            </div>
            
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
                พร้อมที่จะ<span class="text-accent relative inline-block px-2">
                    ท้าทาย
                    <div class="absolute inset-0 bg-white/10 -skew-x-12 -z-10 rounded"></div>
                </span>ตัวเองหรือยัง?
            </h2>
            
            <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto font-light">
                อย่ารอช้า! จำนวนจำกัด สมัครวันนี้เพื่อรับราคา <span class="font-bold text-accent">Early Bird</span> และของที่ระลึกสุดพิเศษ
            </p>

            <div class="flex flex-col md:flex-row justify-center items-center gap-6 mb-16">
                <a href="register.php" class="group relative bg-white text-primary text-xl md:text-2xl font-bold py-5 px-12 rounded-full shadow-2xl hover:shadow-white/20 transition transform hover:-translate-y-1 overflow-hidden">
                    <span class="relative z-10 flex items-center gap-3">
                        <i class="fas fa-rocket"></i> สมัครออนไลน์ทันที
                    </span>
                    <div class="absolute inset-0 bg-gray-100 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-5xl mx-auto border-t border-white/20 pt-12">
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">1000+</div>
                    <div class="text-white/70 text-sm uppercase tracking-wider">Runners</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">4</div>
                    <div class="text-white/70 text-sm uppercase tracking-wider">Distances</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">21</div>
                    <div class="text-white/70 text-sm uppercase tracking-wider">Max KM</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-white mb-2">100%</div>
                    <div class="text-white/70 text-sm uppercase tracking-wider">Fun</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-dark text-white pt-20 pb-10 relative overflow-hidden">
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
                        <li><a href="#home" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> หน้าแรก</a></li>
                        <li><a href="#about" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> รายละเอียด</a></li>
                        <li><a href="#categories" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> ประเภทการแข่งขัน</a></li>
                        <li><a href="#schedule" class="hover:text-primary transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> กำหนดการ</a></li>
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

    <script src="assets/js/script.js"></script>
</body>
</html>
