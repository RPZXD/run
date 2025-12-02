<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white/80 backdrop-blur-xl sticky top-0 z-40 border-b border-slate-200/80 font-['K2D'] shadow-sm">
    <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3 group">
            <div class="relative">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-red-500 via-orange-500 to-red-500 rounded-full blur-md opacity-75 group-hover:opacity-100 transition-opacity animate-pulse"></div>
                <img src="../assets/images/logo01.JPG" class="relative h-11 w-11 rounded-full border-2 border-white shadow-lg">
            </div>
            <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">Admin <span class="bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">Panel</span></span>
        </div>
        
        <div class="flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-2xl overflow-x-auto max-w-full border border-slate-200/50 shadow-inner">
            <a href="dashboard.php" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap flex items-center gap-2 <?php echo $current_page == 'dashboard.php' ? 'bg-white text-blue-600 shadow-md ring-1 ring-blue-100' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'; ?>">
                <i class="fas fa-chart-pie <?php echo $current_page == 'dashboard.php' ? 'text-blue-500' : 'text-slate-400'; ?>"></i> ภาพรวม
            </a>
            <a href="index.php" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap flex items-center gap-2 <?php echo $current_page == 'index.php' ? 'bg-white text-blue-600 shadow-md ring-1 ring-blue-100' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'; ?>">
                <i class="fas fa-list-check <?php echo $current_page == 'index.php' ? 'text-blue-500' : 'text-slate-400'; ?>"></i> ตรวจสอบสลิป
            </a>
            <a href="shirt_orders.php" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap flex items-center gap-2 <?php echo $current_page == 'shirt_orders.php' ? 'bg-white text-amber-600 shadow-md ring-1 ring-amber-100' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'; ?>">
                <i class="fas fa-tshirt <?php echo $current_page == 'shirt_orders.php' ? 'text-amber-500' : 'text-slate-400'; ?>"></i> ออเดอร์เสื้อ
            </a>
            <a href="shipping.php" class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 whitespace-nowrap flex items-center gap-2 <?php echo $current_page == 'shipping.php' ? 'bg-white text-emerald-600 shadow-md ring-1 ring-emerald-100' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'; ?>">
                <i class="fas fa-shipping-fast <?php echo $current_page == 'shipping.php' ? 'text-emerald-500' : 'text-slate-400'; ?>"></i> จัดส่ง
            </a>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden lg:flex items-center gap-2 text-sm text-slate-600 font-medium bg-gradient-to-r from-slate-100 to-slate-50 px-4 py-2 rounded-xl border border-slate-200/50 shadow-sm">
                <i class="fas fa-user-circle text-slate-400"></i> Admin
            </div>
            <a href="logout.php" class="flex items-center gap-2 px-3 py-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-300" title="Logout">
                <i class="fas fa-sign-out-alt text-lg"></i>
                <span class="hidden md:inline text-sm font-medium">ออกจากระบบ</span>
            </a>
        </div>
    </div>
</nav>