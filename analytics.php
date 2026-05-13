<?php
include 'db.php';

// TOTAL PRODUCTS
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM chocolates")
->fetch_assoc()['total'];

// TOTAL STOCK
$totalStock = $conn->query("SELECT SUM(quantity) AS stock FROM chocolates")
->fetch_assoc()['stock'] ?? 0;

// TOTAL VALUE
$totalValue = $conn->query("SELECT SUM(quantity * price) AS value FROM chocolates")
->fetch_assoc()['value'] ?? 0;

// LOW STOCK
$lowStock = $conn->query("SELECT COUNT(*) AS lowstock FROM chocolates WHERE quantity <= 5")
->fetch_assoc()['lowstock'];

// EXPIRED PRODUCTS
$expired = $conn->query("
SELECT COUNT(*) AS expired 
FROM chocolates 
WHERE expiration_date < CURDATE()
")->fetch_assoc()['expired'];

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Analytics Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
}

.logo{
    margin-bottom:40px;
}

.logo h1{
    font-size:30px;
    color:#5d4037;
    line-height:1.3;
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
    border-top:1px solid #eee;
    padding-top:20px;
}

.user-box{
    display:flex;
    align-items:center;
    gap:12px;
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
}

/* HEADER */

.header{
    margin-bottom:30px;
}

.header h1{
    font-size:34px;
    margin-bottom:10px;
}

.header p{
    color:#888;
}

/* CARDS */

.analytics-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
}

.card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
    display:flex;
    align-items:center;
    gap:18px;
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
}

.card h2{
    font-size:32px;
    margin-top:5px;
}

.card p{
    color:#888;
    font-size:13px;
}

.green{
    color:#2e7d32;
}

.red{
    color:#ef5350;
}

.orange{
    color:#ff9800;
}

.blue{
    color:#42a5f5;
}

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

        <div class="sidebar-footer">

            <div class="user-box">

                <div class="user-avatar">
                    A
                </div>

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

            <div class="header">

                <h1>Analytics Dashboard</h1>

                <p>
                    Overview of your chocolate inventory performance.
                </p>

            </div>

            <div class="analytics-grid">

                <!-- TOTAL PRODUCTS -->

                <div class="card">

                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>

                    <div>
                        <p>Total Products</p>
                        <h2 class="blue">
                            <?= $totalProducts ?>
                        </h2>
                    </div>

                </div>

                <!-- TOTAL STOCK -->

                <div class="card">

                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>

                    <div>
                        <p>Total Stock</p>
                        <h2 class="green">
                            <?= $totalStock ?>
                        </h2>
                    </div>

                </div>

                <!-- TOTAL VALUE -->

                <div class="card">

                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>

                    <div>
                        <p>Inventory Value</p>
                        <h2 class="orange">
                            ₱<?= number_format($totalValue,0) ?>
                        </h2>
                    </div>

                </div>

                <!-- LOW STOCK -->

                <div class="card">

                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>

                    <div>
                        <p>Low Stock</p>
                        <h2 class="red">
                            <?= $lowStock ?>
                        </h2>
                    </div>

                </div>

                <!-- EXPIRED -->

                <div class="card">

                    <div class="icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>

                    <div>
                        <p>Expired Products</p>
                        <h2 class="red">
                            <?= $expired ?>
                        </h2>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>