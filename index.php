<?php
include 'db.php';

// SEARCH
$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

    $stmt = $conn->prepare("
        SELECT * FROM chocolates
        WHERE product_name LIKE ?
        OR brand LIKE ?
        OR category LIKE ?
        OR supplier_name LIKE ?
        OR manufacturer LIKE ?
        ORDER BY date_added DESC
    ");

    $like = "%$search%";

    $stmt->bind_param(
        "sssss",
        $like,
        $like,
        $like,
        $like,
        $like
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT * FROM chocolates
        ORDER BY date_added DESC
    ");
}

$totalRecords = $result ? $result->num_rows : 0;

// TOTAL STOCK
$totalStockQuery = $conn->query("
SELECT SUM(quantity) AS total_stock 
FROM chocolates
");

$totalStock = $totalStockQuery->fetch_assoc()['total_stock'] ?? 0;

// TOTAL VALUE
$totalValueQuery = $conn->query("
SELECT SUM(quantity * price) AS total_value 
FROM chocolates
");

$totalValue = $totalValueQuery->fetch_assoc()['total_value'] ?? 0;

// DELETE ALL
if (isset($_POST['delete_all'])) {

    $conn->query("TRUNCATE TABLE chocolate_images");
    $conn->query("TRUNCATE TABLE chocolates");

    header("Location: index.php?success=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Chocolate Inventory Dashboard</title>

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
    background:#ffffff;
    padding:30px 20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    box-shadow:0 0 30px rgba(0,0,0,0.06);
    position:fixed;
    left:0;
    top:0;
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
    font-size:15px;
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

.main{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:30px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    gap:20px;
    flex-wrap:wrap;
}

.search-box{
    flex:1;
    position:relative;
    max-width:450px;
}

.search-box input{
    width:100%;
    height:55px;
    border:none;
    border-radius:18px;
    background:white;
    padding:0 60px 0 20px;
    outline:none;
    font-size:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.search-box button{
    position:absolute;
    right:8px;
    top:8px;
    width:40px;
    height:40px;
    border:none;
    border-radius:12px;
    background:#c89b3c;
    color:white;
    cursor:pointer;
}

.add-btn{
    background:#c89b3c;
    color:white;
    border:none;
    padding:15px 24px;
    border-radius:16px;
    font-weight:600;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:10px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    border-radius:24px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.card-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.card-icon{
    width:55px;
    height:55px;
    border-radius:18px;
    background:#f7f1e3;
    color:#c89b3c;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:20px;
}

.card h2{
    font-size:34px;
}

.card p{
    color:#888;
    font-size:14px;
}

/* TABLE */

.table-container{
    background:white;
    border-radius:28px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
    overflow:auto;
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.delete-all{
    background:#ef5350;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:14px;
    cursor:pointer;
    font-weight:600;
}

.inventory-table{
    width:100%;
    border-collapse:collapse;
    min-width:1300px;
}

.inventory-table thead{
    background:#faf7f1;
}

.inventory-table th{
    padding:18px;
    text-align:left;
    color:#888;
    font-size:14px;
}

.inventory-table td{
    padding:18px;
    border-bottom:1px solid #f1f1f1;
}

.product-cell{
    display:flex;
    align-items:center;
    gap:15px;
}

.product-img{
    width:65px;
    height:65px;
    border-radius:18px;
    overflow:hidden;
    background:#f3f3f3;
}

.product-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.no-image{
    width:100%;
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:11px;
    color:#aaa;
}

.product-name{
    font-weight:600;
}

.brand{
    color:#999;
    font-size:13px;
}

.category-badge{
    padding:8px 14px;
    border-radius:30px;
    background:#f7f1e3;
    color:#c89b3c;
    font-size:12px;
    font-weight:600;
}

.qty-badge{
    padding:8px 14px;
    border-radius:30px;
    background:#e8f5e9;
    color:#2e7d32;
    font-size:12px;
    font-weight:600;
}

.price{
    font-weight:700;
    color:#c89b3c;
}

.actions{
    display:flex;
    gap:10px;
}

.action-btn{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    text-decoration:none;
}

.edit{
    background:#42a5f5;
}

.delete{
    background:#ef5350;
}

.alert{
    background:#e8f5e9;
    color:#2e7d32;
    padding:16px 20px;
    border-radius:18px;
    margin-bottom:20px;
}

.empty{
    text-align:center;
    padding:80px 20px;
}

@media(max-width:1000px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .wrapper{
        flex-direction:column;
    }

    .main{
        width:100%;
        margin-left:0;
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
                <h1>🍫 R & G <span>Chocolate</span></h1>
            </div>

            <div class="menu">

                <a href="index.php" class="active">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>

                <a href="analytics.php">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>

                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>

            </div>

        </div>

    </div>

    <!-- MAIN -->

    <div class="main">

        <div class="topbar">

            <form method="GET" class="search-box">

                <input
                type="text"
                name="search"
                placeholder="Search chocolates..."
                value="<?= htmlspecialchars($search) ?>">

                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>

            </form>

            <a href="create.php" class="add-btn">
                <i class="fas fa-plus"></i>
                Add Product
            </a>

        </div>

        <?php if(isset($_GET['success'])): ?>

        <div class="alert">
            Action completed successfully!
        </div>

        <?php endif; ?>

        <!-- STATS -->

        <div class="stats">

            <div class="card">
                <div class="card-top">
                    <div>
                        <p>Total Products</p>
                        <h2><?= $totalRecords ?></h2>
                    </div>

                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-top">
                    <div>
                        <p>Total Stock</p>
                        <h2><?= $totalStock ?></h2>
                    </div>

                    <div class="card-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-top">
                    <div>
                        <p>Inventory Value</p>
                        <h2>₱<?= number_format($totalValue,0) ?></h2>
                    </div>

                    <div class="card-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE -->

        <div class="table-container">

            <div class="table-header">

                <h2>Chocolate Inventory</h2>

                <?php if($totalRecords > 0): ?>

                <form method="POST">

                    <button
                    type="submit"
                    name="delete_all"
                    class="delete-all"
                    onclick="return confirm('Delete all products?')">

                        <i class="fas fa-trash"></i>
                        Delete All

                    </button>

                </form>

                <?php endif; ?>

            </div>

            <?php if($totalRecords > 0): ?>

            <table class="inventory-table">

                <thead>

                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Expiration</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php while($row = $result->fetch_assoc()): ?>

                <?php

                $imgStmt = $conn->prepare("
                SELECT image FROM chocolate_images
                WHERE chocolate_id=?
                LIMIT 1
                ");

                $imgStmt->bind_param("i", $row['id']);
                $imgStmt->execute();

                $img = $imgStmt->get_result()->fetch_assoc();

                ?>

                <tr>

                    <td>

                        <div class="product-cell">

                            <div class="product-img">

                                <?php if($img): ?>

                                <img src="uploads/<?= htmlspecialchars($img['image']) ?>">

                                <?php else: ?>

                                <div class="no-image">
                                    No Image
                                </div>

                                <?php endif; ?>

                            </div>

                            <div>

                                <div class="product-name">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </div>

                                <div class="brand">
                                    <?= htmlspecialchars($row['brand']) ?>
                                </div>

                            </div>

                        </div>

                    </td>

                    <td>
                        <span class="category-badge">
                            <?= htmlspecialchars($row['category']) ?>
                        </span>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['manufacturer']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['supplier_name']) ?>
                    </td>

                    <td>
                        <span class="qty-badge">
                            <?= $row['quantity'] ?> pcs
                        </span>
                    </td>

                    <td class="price">
                        ₱<?= number_format($row['price'],2) ?>
                    </td>

                    <td>
                        <?= date('M j, Y', strtotime($row['expiration_date'])) ?>
                    </td>

                    <td>

                        <div class="actions">

                            <a
                            href="update.php?id=<?= $row['id'] ?>"
                            class="action-btn edit">

                                <i class="fas fa-edit"></i>

                            </a>

                            <a
                            href="delete.php?id=<?= $row['id'] ?>"
                            class="action-btn delete"
                            onclick="return confirm('Delete this product?')">

                                <i class="fas fa-trash"></i>

                            </a>

                        </div>

                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

            <?php else: ?>

            <div class="empty">

                <i class="fas fa-box-open"></i>

                <h2>No Products Found</h2>

                <p style="color:#999;margin-top:10px;">
                    Add your first chocolate product.
                </p>

            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>