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
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:fixed;
    left:0;
    top:0;
    height:100vh;
    border-right:1px solid #eee;
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
    font-size:15px;
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
    padding:25px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:20px;
}

.search-box{
    flex:1;
    position:relative;
}

.search-box input{
    width:100%;
    height:55px;
    border:none;
    border-radius:16px;
    background:white;
    padding:0 20px 0 50px;
    outline:none;
    font-size:14px;
    border:1px solid #eee;
}

.search-box i{
    position:absolute;
    left:18px;
    top:18px;
    color:#999;
}

.add-btn{
    background:#c89b3c;
    color:white;
    border:none;
    padding:14px 22px;
    border-radius:14px;
    font-weight:600;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:10px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:white;
    border-radius:18px;
    padding:22px;
    display:flex;
    align-items:center;


    gap:18px;
    border:1px solid #f1f1f1;

}

.card-icon{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#f8efd9;
    color:#c89b3c;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.card h2{

    font-size:34px;

    font-size:30px;
    margin-top:5px;

}

.card p{
    font-size:13px;
    color:#888;
}

/* TABLE */

.table-container{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;

    flex-wrap:wrap;

}

.table-header h2{
    font-size:22px;

}

.delete-all{
    background:#ffefef;
    color:#ef5350;
    border:none;
    padding:12px 18px;
    border-radius:12px;
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
    text-align:left;

    color:#888;
    font-size:14px;
}

.inventory-table td{
    padding:18px;
    border-bottom:1px solid #f1f1f1;

    padding:15px;
    font-size:13px;
    color:#888;
    background:#faf8f4;
}

.inventory-table td{
    padding:15px;
    border-bottom:1px solid #f3f3f3;
    font-size:14px;

}

.product-cell{
    display:flex;
    align-items:center;
    gap:15px;
}

.product-img{
    width:55px;
    height:55px;
    border-radius:12px;
    overflow:hidden;
    background:#f3f3f3;
}

.product-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.no-image{
    display:flex;
    align-items:center;
    justify-content:center;
    width:100%;
    height:100%;
    font-size:11px;
    color:#aaa;
}

.product-name{
    font-weight:600;
}

.brand{
    color:#999;
    font-size:12px;
}

.category-badge{
    background:#f8efd9;
    color:#c89b3c;
    padding:8px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.qty-badge{
    background:#e8f5e9;
    color:#2e7d32;
    padding:8px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.price{
    font-weight:700;
    color:#c89b3c;
}

.actions{
    display:flex;
    gap:8px;
}

.action-btn{
    width:36px;
    height:36px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
}

.edit{
    background:#fff6e5;
    color:#c89b3c;
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
    background:#ffefef;
    color:#ef5350;

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
                <h1>🍫 R & G <br><span>Chocolate</span></h1>
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



    <!-- MAIN -->

    <div class="main">

        <div class="topbar">
        <div class="dashboard-container">

            <!-- TOPBAR -->

            <div class="topbar">

                <form method="GET" class="search-box">

                    <i class="fas fa-search"></i>

        </div>

        <?php if(isset($_GET['success'])): ?>

        <div class="alert">
            Action completed successfully!
        </div>
                    <input
                    type="text"
                    name="search"
                    placeholder="Search chocolates, brands, categories..."
                    value="<?= htmlspecialchars($search) ?>">

                </form>

                <a href="create.php" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add Product
                </a>

        <!-- STATS -->
            </div>

            <!-- STATS -->

            <div class="stats">

            <div class="card">
                <div class="card-top">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>

                    <div>
                        <p>Total Products</p>
                        <h2><?= $totalRecords ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-top">

                    <div>
                        <p>Total Stock</p>
                        <h2><?= $totalStock ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-top">

                    <div>
                        <p>Inventory Value</p>
                        <h2>₱<?= number_format($totalValue,0) ?></h2>
                    </div>
                </div>
            </div>

            </div>

            <!-- TABLE -->

            <div class="table-container">

                <div class="table-header">

                    <h2>All Chocolates</h2>

                    <?php if($totalRecords > 0): ?>

        <!-- TABLE -->
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

                <table class="inventory-table">

                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
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

            <?php if($totalRecords > 0): ?>
                    $imgStmt->bind_param("i", $row['id']);
                    $imgStmt->execute();

                    $img = $imgStmt->get_result()->fetch_assoc();

                    ?>

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
                    <tr>

                        <td>

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
                        </td>

                        <td>

                            <div class="product-name">
                                <?= htmlspecialchars($row['product_name']) ?>
                            </div>

                            <div class="brand">
                                <?= htmlspecialchars($row['brand']) ?>
                            </div>

                        </td>

                        <td>
                            <span class="category-badge">
                                <?= htmlspecialchars($row['category']) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['supplier_name']) ?>
                        </td>

                        <td>
                            <span class="qty-badge">
                                <?= $row['quantity'] ?>
                            </span>
                        </td>

                            <a
                            href="delete.php?id=<?= $row['id'] ?>"
                            class="action-btn delete"
                            onclick="return confirm('Delete this product?')">
                        <td class="price">
                            ₱<?= number_format($row['price'],2) ?>
                        </td>

                        <td>
                            <?= date('Y-m-d', strtotime($row['expiration_date'])) ?>
                        </td>

                        <td>

                            <div class="actions">

                                <a
                                href="update.php?id=<?= $row['id'] ?>"
                                class="action-btn edit">

                                    <i class="fas fa-pen"></i>

                                </a>

                                <a
                                href="delete.php?id=<?= $row['id'] ?>"
                                class="action-btn delete"
                                onclick="return confirm('Delete this item?')">

                                    <i class="fas fa-trash"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>