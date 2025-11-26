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
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-600">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">ภาพรวมข้อมูลการสมัคร</h1>

        <!-- Top Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-slate-400 text-xs font-bold uppercase">ผู้สมัครทั้งหมด</div>
                <div class="text-3xl font-bold text-slate-800 mt-2"><?php echo number_format(count($registrations)); ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-slate-400 text-xs font-bold uppercase">รายได้รวม (อนุมัติแล้ว)</div>
                <div class="text-3xl font-bold text-green-600 mt-2">฿<?php echo number_format($total_income); ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-slate-400 text-xs font-bold uppercase">รอตรวจสอบ</div>
                <div class="text-3xl font-bold text-yellow-500 mt-2"><?php echo number_format($by_status['pending'] ?? 0); ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-slate-400 text-xs font-bold uppercase">อนุมัติแล้ว</div>
                <div class="text-3xl font-bold text-blue-600 mt-2"><?php echo number_format($by_status['approved'] ?? 0); ?></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Category Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-lg mb-4 text-slate-800">จำนวนผู้สมัครแยกตามประเภท</h3>
                <canvas id="categoryChart"></canvas>
            </div>

            <!-- Gender Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h3 class="font-bold text-lg mb-4 text-slate-800">สัดส่วนเพศ</h3>
                <div class="h-64 flex justify-center">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Shirt Sizes -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
            <h3 class="font-bold text-lg mb-4 text-slate-800">จำนวนเสื้อแยกตามไซส์</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($by_size as $size => $count): ?>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-center">
                    <div class="text-sm font-bold text-slate-500 mb-1"><?php echo $size; ?></div>
                    <div class="text-2xl font-bold text-slate-800"><?php echo $count; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Category Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($by_category)); ?>,
                datasets: [{
                    label: 'จำนวนผู้สมัคร',
                    data: <?php echo json_encode(array_values($by_category)); ?>,
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
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
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>