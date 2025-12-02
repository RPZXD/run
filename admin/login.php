<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple hardcoded credentials
    if ($username === 'adminphichairun' && $password === 'v8GOllNF08D4Ep4FbqZ#') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'k2d': ['K2D', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'K2D', sans-serif; }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .float-animation-delayed { animation: float 8s ease-in-out infinite; animation-delay: -2s; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-slate-50 to-orange-50 h-screen flex items-center justify-center overflow-hidden relative font-k2d">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-gradient-to-br from-red-500/20 to-orange-500/10 rounded-full blur-3xl float-animation"></div>
        <div class="absolute top-1/2 -right-24 w-80 h-80 bg-gradient-to-br from-orange-500/15 to-yellow-500/10 rounded-full blur-3xl float-animation-delayed"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-gradient-to-br from-red-400/10 to-pink-500/10 rounded-full blur-3xl float-animation" style="animation-delay: -3s;"></div>
    </div>

    <div class="w-full max-w-md p-6 relative z-10">
        <div class="bg-white/70 backdrop-blur-2xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-white/80 overflow-hidden">
            <div class="p-8 md:p-10">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-red-500 via-red-600 to-orange-500 shadow-xl shadow-red-500/40 mb-5 rotate-3 hover:rotate-0 transition-transform duration-300">
                        <i class="fas fa-running text-4xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">Admin Login</h2>
                    <p class="text-slate-500 mt-2 font-medium">เข้าสู่ระบบจัดการ Phichai Run 2026</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 text-red-600 px-4 py-3 rounded-2xl mb-6 text-sm text-center flex items-center justify-center gap-2 animate-pulse">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-user-circle text-slate-400 mr-1"></i> Username
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors duration-300">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="username" class="w-full pl-12 pr-4 py-4 bg-slate-50/80 border-2 border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500 focus:bg-white transition-all duration-300 text-slate-700 font-medium placeholder:text-slate-400" placeholder="Enter username" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-key text-slate-400 mr-1"></i> Password
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors duration-300">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" class="w-full pl-12 pr-4 py-4 bg-slate-50/80 border-2 border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500 focus:bg-white transition-all duration-300 text-slate-700 font-medium placeholder:text-slate-400" placeholder="Enter password" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 via-red-500 to-orange-500 text-white font-bold py-4 rounded-2xl shadow-xl shadow-red-500/30 hover:shadow-red-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 mt-8 group">
                        <span class="text-lg">เข้าสู่ระบบ</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
            </div>
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-8 py-5 text-center border-t border-slate-200">
                <a href="../index.php" class="text-sm text-slate-500 hover:text-red-600 font-semibold transition-all duration-300 flex items-center justify-center gap-2 group">
                    <i class="fas fa-home group-hover:-translate-x-1 transition-transform"></i> กลับหน้าหลัก
                </a>
            </div>
        </div>
        
        <p class="text-center text-slate-400 text-xs mt-8 font-medium">
            &copy; 2025 Phichai Run. All rights reserved.
        </p>
    </div>
</body>
</html>
