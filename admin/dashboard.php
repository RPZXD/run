<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../app/config/database.php';
require_once '../app/models/Registration.php';

$database = new Database();
$db = $database->connect();
$registration = new Registration($db);

$stmt = $registration->readAll();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats Calculation
$total_income = 0;
$by_category = [];
$by_gender = [];
$by_size = [];
$by_status = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

foreach ($registrations as $reg) {
    $status = $reg['status'] ?? 'pending';
    $cat = $reg['category'] ?? 'Unknown';
    $gender = $reg['gender'] ?? 'Unknown';
    $size = $reg['shirt_size'] ?? 'Unknown';
    
    // Status
    if (isset($by_status[$status])) {
        $by_status[$status]++;
    } else {
        $by_status[$status] = 1;
    }

    // Income (only approved)
    if ($status === 'approved') {
        $total_income += (float)($reg['payment_amount'] ?? 0);
    }

    // Category
    if (!isset($by_category[$cat])) $by_category[$cat] = 0;
    $by_category[$cat]++;

    // Gender
    if (!isset($by_gender[$gender])) $by_gender[$gender] = 0;
    $by_gender[$gender]++;

    // Size
    if (!isset($by_size[$size])) $by_size[$size] = 0;
    $by_size[$size]++;
}

ksort($by_size);
ksort($by_category);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Phichai Run 2026</title>
    <link rel="icon" type="image/png" href="../assets/images/logo01.JPG"> 
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
    </style>
</head>
<body class="bg-slate-50 text-slate-600 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwgMCwgMCwgMC4wNSkiLz48L3N2Zz4=')]">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="flex items-center justify-between mb-8 animate-fade-in-up">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
                <p class="text-slate-500 mt-1">ภาพรวมข้อมูลการสมัคร Phichai Run 2026</p>
            </div>
            <div class="text-sm text-slate-400 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">
                <i class="far fa-calendar-alt mr-2"></i> <?php echo date('d M Y'); ?>
            </div>
        </div>

        <!-- Top Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-100 group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">ผู้สมัครทั้งหมด</div>
                        <div class="text-3xl font-bold text-slate-800 mt-2 group-hover:text-blue-600 transition-colors"><?php echo number_format(count($registrations)); ?></div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-400">
                    <span class="text-green-500 font-bold mr-1"><i class="fas fa-arrow-up"></i> Updated</span>
                    <span>just now</span>
                </div>
            </div>

            <!-- Income -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-200 group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">รายได้รวม (อนุมัติแล้ว)</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">฿<?php echo number_format($total_income); ?></div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-400">
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: 70%"></div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">รอตรวจสอบ</div>
                        <div class="text-3xl font-bold text-yellow-500 mt-2"><?php echo number_format($by_status['pending'] ?? 0); ?></div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-xl text-yellow-500 group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-400">
                    <a href="index.php" class="text-yellow-600 hover:underline">ไปที่หน้าตรวจสอบ <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 animate-fade-in-up delay-300 group">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">อนุมัติแล้ว</div>
                        <div class="text-3xl font-bold text-blue-600 mt-2"><?php echo number_format($by_status['approved'] ?? 0); ?></div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-slate-400">
                    <span class="text-blue-500 font-bold"><?php echo number_format(($by_status['approved'] ?? 0) / max(1, count($registrations)) * 100, 1); ?>%</span>
                    <span class="ml-1">completion rate</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Category Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 animate-fade-in-up delay-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                        จำนวนผู้สมัครแยกตามประเภท
                    </h3>
                    <button class="text-slate-400 hover:text-blue-500 transition-colors"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Gender Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition-all duration-300 animate-fade-in-up delay-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-8 bg-pink-500 rounded-full"></span>
                        สัดส่วนเพศ
                    </h3>
                    <button class="text-slate-400 hover:text-pink-500 transition-colors"><i class="fas fa-ellipsis-h"></i></button>
                </div>
                <div class="h-64 flex justify-center relative">
                    <canvas id="genderChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <div class="text-xs text-slate-400 uppercase font-bold">Total</div>
                            <div class="text-2xl font-bold text-slate-700"><?php echo count($registrations); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shirt Sizes -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-8 animate-fade-in-up delay-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <i class="fas fa-tshirt text-xl"></i>
                </div>
                <h3 class="font-bold text-xl text-slate-800">จำนวนเสื้อแยกตามไซส์</h3>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($by_size as $size => $count): ?>
                <div class="group bg-slate-50 p-4 rounded-xl border border-slate-100 text-center hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 cursor-default relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-1 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-tshirt text-4xl text-indigo-600 transform rotate-12"></i>
                    </div>
                    <div class="text-sm font-bold text-slate-500 mb-1 group-hover:text-indigo-500 transition-colors"><?php echo $size; ?></div>
                    <div class="text-3xl font-bold text-slate-800 group-hover:text-indigo-700 transition-colors"><?php echo $count; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Chart Defaults
        Chart.defaults.font.family = "'Sarabun', sans-serif";
        Chart.defaults.color = '#64748b';
        
        // Category Chart
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        const gradientCat = ctxCat.createLinearGradient(0, 0, 0, 400);
        gradientCat.addColorStop(0, '#3b82f6');
        gradientCat.addColorStop(1, '#60a5fa');

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($by_category)); ?>,
                datasets: [{
                    label: 'จำนวนผู้สมัคร',
                    data: <?php echo json_encode(array_values($by_category)); ?>,
                    backgroundColor: gradientCat,
                    borderRadius: 6,
                    barThickness: 40,
                    hoverBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e2e8f0', drawBorder: false },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });

        // Gender Chart
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($by_gender)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($by_gender)); ?>,
                    backgroundColor: ['#3b82f6', '#ec4899', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                let value = context.raw;
                                let total = context.chart._metasets[context.datasetIndex].total;
                                let percentage = Math.round((value / total) * 100) + '%';
                                return label + value + ' (' + percentage + ')';
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
</body>
</html>