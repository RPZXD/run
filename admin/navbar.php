<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 font-['Sarabun']">
    <div class="container mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full blur opacity-75"></div>
                <img src="../assets/images/logo-1.png" class="relative h-10 w-10 rounded-full border-2 border-white">
            </div>
            <span class="font-bold text-xl tracking-tight text-slate-800">Admin <span class="text-red-500">Panel</span></span>
        </div>
        
        <div class="flex items-center gap-1 bg-slate-100/50 p-1 rounded-xl overflow-x-auto max-w-full">
            <a href="dashboard.php" class="px-4 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap <?php echo $current_page == 'dashboard.php' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'; ?>">
                <i class="fas fa-chart-pie mr-1"></i> ภาพรวม
            </a>
            <a href="index.php" class="px-4 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap <?php echo $current_page == 'index.php' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'; ?>">
                <i class="fas fa-list-check mr-1"></i> ตรวจสอบสลิป
            </a>
            <a href="shipping.php" class="px-4 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap <?php echo $current_page == 'shipping.php' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'; ?>">
                <i class="fas fa-shipping-fast mr-1"></i> จัดส่ง
            </a>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden lg:flex items-center gap-2 text-sm text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                <i class="fas fa-user-circle"></i> Admin
            </div>
            <a href="logout.php" class="text-slate-500 hover:text-red-600 transition-colors duration-200" title="Logout">
                <i class="fas fa-sign-out-alt text-lg"></i>
            </a>
        </div>
    </div>
</nav>