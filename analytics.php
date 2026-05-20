<?php
include 'db.php';

// TOTAL PRODUCTS
$totalProducts = $conn->query("
SELECT COUNT(*) AS total 
FROM chocolates
")->fetch_assoc()['total'];

// TOTAL STOCK
$totalStock = $conn->query("
SELECT SUM(quantity) AS stock 
FROM chocolates
")->fetch_assoc()['stock'] ?? 0;

// TOTAL VALUE
$totalValue = $conn->query("
SELECT SUM(quantity * price) AS value 
FROM chocolates
")->fetch_assoc()['value'] ?? 0;

// LOW STOCK
$lowStock = $conn->query("
SELECT COUNT(*) AS lowstock 
FROM chocolates 
WHERE quantity <= 5
")->fetch_assoc()['lowstock'];

// EXPIRED PRODUCTS
$expired = $conn->query("
SELECT COUNT(*) AS expired 
FROM chocolates 
WHERE expiration_date < CURDATE()
")->fetch_assoc()['expired'];

// CATEGORY GRAPH
$categoryData = $conn->query("
SELECT category, COUNT(*) as total
FROM chocolates
GROUP BY category
");

$categories = [];
$totals = [];

while($row = $categoryData->fetch_assoc()){
    $categories[] = $row['category'];
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Analytics Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    background:#f6f2ea;
    color:#333;
    transition: background 0.3s, color 0.3s;
}

.wrapper{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */

.sidebar{
    width:250px;
    background:#fff;
    padding:30px 20px;
    position:fixed;
    height:100vh;
    border-right:1px solid #eee;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    overflow:hidden;
    transition: background 0.3s, border-color 0.3s;
}

.logo{
    margin-bottom:40px;
}

.logo h1{
    font-size:30px;
    color:#5d4037;
    line-height:1.3;
    transition: color 0.3s;
}

.logo span{
    color:#c89b3c;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.menu a{
    text-decoration:none;
    color:#777;
    padding:15px 18px;
    border-radius:14px;
    font-weight:500;
    transition:0.3s;
    display:flex;
    align-items:center;
    gap:12px;
}

.menu a:hover,
.menu a.active{
    background:#f6efe2;
    color:#c89b3c;
}

/* USER */

.sidebar-footer{
    margin-top:auto;
    border-top:1px solid #eee;
    padding-top:20px;
    transition: border-color 0.3s;
}

.user-box{
    display:flex;
    align-items:center;
    gap:12px;
    background:white;
    border-radius:16px;
    padding:12px;
    transition: background 0.3s;
}

.user-avatar{
    width:45px;
    height:45px;
    border-radius:50%;
    background:#c89b3c;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:700;
}

.user-name{
    font-weight:600;
    font-size:14px;
    transition: color 0.3s;
}

.user-role{
    font-size:12px;
    color:#999;
}

/* MAIN */

.main{
    margin-left:250px;
    width:calc(100% - 250px);
    padding:30px;
}

.dashboard-container{
    background:#fbf8f3;
    border-radius:25px;
    padding:30px;
    transition: background 0.3s;
}

/* HEADER */

.header{
    margin-bottom:30px;
}

.header h1{
    font-size:34px;
    margin-bottom:10px;
    transition: color 0.3s;
}

.header p{
    color:#888;
}

/* CARDS */

.analytics-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
    display:flex;
    align-items:center;
    gap:18px;
    transition: background 0.3s, border-color 0.3s;
}

.icon{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#f8efd9;
    color:#c89b3c;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:22px;
    flex-shrink:0;
}

.card h2{
    font-size:32px;
    margin-top:5px;
}

.card p{
    color:#888;
    font-size:13px;
}

.green  { color:#2e7d32; }
.red    { color:#ef5350; }
.orange { color:#ff9800; }
.blue   { color:#42a5f5; }

/* CHART */

.chart-container{
    background:white;
    padding:30px;
    border-radius:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    transition: background 0.3s, box-shadow 0.3s;
}

.chart-container h2{
    margin-bottom:20px;
    color:#5d4037;
    transition: color 0.3s;
}

canvas{
    max-height:400px;
}

/* =====================
   DARK MODE
   ===================== */

body.dark{
    background:#1a1410;
    color:#e8ddd0;
}

body.dark .sidebar{
    background:#211a14;
    border-color:#2e2318;
}

body.dark .logo h1{
    color:#e8c97a;
}

body.dark .menu a{
    color:#9e8a78;
}

body.dark .menu a:hover,
body.dark .menu a.active{
    background:#2e2318;
    color:#c89b3c;
}

body.dark .sidebar-footer{
    border-color:#2e2318;
}

body.dark .user-box{
    background:#2a1f17;
}

body.dark .user-name{
    color:#e8ddd0;
}

body.dark .dashboard-container{
    background:#201811;
}

body.dark .header h1{
    color:#e8c97a;
}

body.dark .header p{
    color:#6e5a48;
}

body.dark .card{
    background:#2a1f17;
    border-color:#3a2a1e;
}

body.dark .card p{
    color:#9e8a78;
}

body.dark .icon{
    background:#3a2a1e;
}

body.dark .chart-container{
    background:#2a1f17;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

body.dark .chart-container h2{
    color:#e8c97a;
}

/* RESPONSIVE */

@media(max-width:900px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .wrapper{
        flex-direction:column;
    }

    .main{
        margin-left:0;
        width:100%;
    }
}

</style>
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div>

            <div class="logo">
                <h1>🍫 R & G <br><span>Chocolate</span></h1>
            </div>

            <div class="menu">

                <a href="index.php">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>

                <a href="analytics.php" class="active">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>

                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>

            </div>

        </div>

        <!-- USER -->

        <div class="sidebar-footer">

            <div class="user-box">

                <div class="user-avatar">A</div>

                <div>
                    <div class="user-name">Administrator</div>
                    <div class="user-role">Admin</div>
                </div>

            </div>

        </div>

    </div>

    <!-- MAIN -->

    <div class="main">

        <div class="dashboard-container">

            <!-- HEADER -->

            <div class="header">
                <h1>Analytics Dashboard</h1>
                <p>Overview of your chocolate inventory performance.</p>
            </div>

            <!-- CARDS -->

            <div class="analytics-grid">

                <div class="card">
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <p>Total Products</p>
                        <h2 class="blue"><?= $totalProducts ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div>
                        <p>Total Stock</p>
                        <h2 class="green"><?= $totalStock ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <p>Inventory Value</p>
                        <h2 class="orange">₱<?= number_format($totalValue,0) ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <p>Low Stock</p>
                        <h2 class="red"><?= $lowStock ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <p>Expired Products</p>
                        <h2 class="red"><?= $expired ?></h2>
                    </div>
                </div>

            </div>

            <!-- CHART -->

            <div class="chart-container">
                <h2>Chocolate Categories Graph</h2>
                <canvas id="categoryChart"></canvas>
            </div>

        </div>

    </div>

</div>

<script>
// Apply saved theme on load — must run before chart renders
(function() {
    const saved = localStorage.getItem('rg_theme') || 'light';
    if (saved === 'dark') document.body.classList.add('dark');
})();

// Determine chart colors based on active theme
function isDark() {
    return document.body.classList.contains('dark');
}

const chartTextColor  = () => isDark() ? '#9e8a78' : '#666';
const chartGridColor  = () => isDark() ? '#3a2a1e' : '#e0e0e0';

const ctx = document.getElementById('categoryChart');

const categoryChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
            label: 'Products Per Category',
            data: <?= json_encode($totals) ?>,
            backgroundColor: [
                '#c89b3c',
                '#8d6e63',
                '#ff9800',
                '#5d4037',
                '#42a5f5',
                '#ef5350'
            ],
            borderRadius: 12
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                ticks: {
                    color: chartTextColor()
                },
                grid: {
                    color: chartGridColor()
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: chartTextColor()
                },
                grid: {
                    color: chartGridColor()
                }
            }
        }
    }
});
</script>

</body>
</html>