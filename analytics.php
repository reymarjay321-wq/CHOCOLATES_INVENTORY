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
    background:#f5f1e8;
    color:#333;
}

.wrapper{
    display:flex;
    min-height:100vh;
}

/* SIDEBAR */

.sidebar{
    width:260px;
    background:white;
    padding:30px 20px;
    box-shadow:0 0 30px rgba(0,0,0,0.06);
    position:fixed;
    height:100vh;
}

.logo{
    text-align:center;
    margin-bottom:40px;
}

.logo h1{
    font-size:28px;
    color:#5d4037;
}

.logo span{
    color:#c89b3c;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:12px;
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
    background:#f7f1e3;
    color:#c89b3c;
}

/* MAIN */

.main{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:30px;
}

.header{
    margin-bottom:30px;
}

.header h1{
    font-size:36px;
    margin-bottom:8px;
}

.header p{
    color:#888;
}

/* CARDS */

.analytics-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
}

.card{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.card-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.icon{
    width:60px;
    height:60px;
    border-radius:18px;
    background:#f7f1e3;
    color:#c89b3c;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:22px;
}

.card h2{
    font-size:38px;
    margin-bottom:8px;
}

.card p{
    color:#888;
    font-size:14px;
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

        <div class="logo">
            <h1>🍫 R & G <span>Chocolate</span></h1>
        </div>

        <div class="menu">

            <a href="index.php">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </a>

            <a href="inventory.php">
                <i class="fas fa-box"></i>
                Inventory
            </a>

            <a href="create.php">
                <i class="fas fa-plus-circle"></i>
                Add Product
            </a>

            <a href="analytics.php" class="active">
                <i class="fas fa-chart-line"></i>
                Analytics
            </a>

        </div>

    </div>

    <!-- MAIN -->

    <div class="main">

        <div class="header">

            <h1>Analytics Dashboard</h1>

            <p>
                Overview of your chocolate inventory performance.
            </p>

        </div>

        <div class="analytics-grid">

            <!-- TOTAL PRODUCTS -->

            <div class="card">

                <div class="card-top">

                    <div>
                        <p>Total Products</p>
                        <h2 class="blue">
                            <?= $totalProducts ?>
                        </h2>
                    </div>

                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>

                </div>

            </div>

            <!-- TOTAL STOCK -->

            <div class="card">

                <div class="card-top">

                    <div>
                        <p>Total Stock</p>
                        <h2 class="green">
                            <?= $totalStock ?>
                        </h2>
                    </div>

                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>

                </div>

            </div>

            <!-- TOTAL VALUE -->

            <div class="card">

                <div class="card-top">

                    <div>
                        <p>Inventory Value</p>
                        <h2 class="orange">
                            ₱<?= number_format($totalValue,0) ?>
                        </h2>
                    </div>

                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>

                </div>

            </div>

            <!-- LOW STOCK -->

            <div class="card">

                <div class="card-top">

                    <div>
                        <p>Low Stock</p>
                        <h2 class="red">
                            <?= $lowStock ?>
                        </h2>
                    </div>

                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>

                </div>

            </div>

            <!-- EXPIRED -->

            <div class="card">

                <div class="card-top">

                    <div>
                        <p>Expired Products</p>
                        <h2 class="red">
                            <?= $expired ?>
                        </h2>
                    </div>

                    <div class="icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>