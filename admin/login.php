<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple hardcoded credentials
    if ($username === 'admin' && $password === 'admin123') {
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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center overflow-hidden relative">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-24 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="w-full max-w-md p-6 relative z-10">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <div class="p-8 md:p-10">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-red-500 to-orange-500 shadow-lg mb-4">
                        <i class="fas fa-user-shield text-3xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-800">Admin Login</h2>
                    <p class="text-slate-500 mt-2">เข้าสู่ระบบจัดการ Phichai Run 2026</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm text-center flex items-center justify-center gap-2 animate-bounce">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">Username</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="username" class="w-full pl-11 px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-slate-700 font-medium" placeholder="Enter username" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-slate-700 text-sm font-bold mb-2 ml-1">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-red-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" class="w-full pl-11 px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all text-slate-700 font-medium" placeholder="Enter password" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-red-500/30 hover:shadow-red-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2">
                        <span>เข้าสู่ระบบ</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>
            <div class="bg-slate-50 px-8 py-4 text-center border-t border-slate-100">
                <a href="../index.php" class="text-sm text-slate-500 hover:text-red-600 font-medium transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i> กลับหน้าหลัก
                </a>
            </div>
        </div>
        
        <p class="text-center text-slate-400 text-xs mt-8">
            &copy; 2025 Phichai Run. All rights reserved.
        </p>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>
