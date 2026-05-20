<?php
include 'db.php';

// SEARCH
$search = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {

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

    $like = "%{$search}%";

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
$totalStockQuery = $conn->query("SELECT SUM(quantity) AS total_stock FROM chocolates");
$totalStock = $totalStockQuery->fetch_assoc()['total_stock'] ?? 0;

// TOTAL VALUE
$totalValueQuery = $conn->query("SELECT SUM(quantity * price) AS total_value FROM chocolates");
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

<!-- NO-FLASH DARK MODE: must be first script in head -->
<script>
(function(){
    if(localStorage.getItem('rg_theme')==='dark'){
        document.documentElement.classList.add('dark');
    }
})();
</script>

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
    transition:background 0.3s,color 0.3s;
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
    transition:background 0.3s,border-color 0.3s;
}

.logo{
    margin-bottom:40px;
}

.logo h1{
    font-size:30px;
    color:#5d4037;
    line-height:1.3;
    transition:color 0.3s;
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
    transition:border-color 0.3s;
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
    transition:color 0.3s;
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
    transition:background 0.3s;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:20px;
}

/* SEARCH */

.search-box{
    flex:1;
}

.search-wrapper{
    position:relative;
    width:100%;
}

.search-wrapper input{
    width:100%;
    height:58px;
    border-radius:18px;
    background:white;
    padding:0 90px 0 52px;
    outline:none;
    font-size:15px;
    border:1px solid #eee;
    transition:0.3s;
    box-shadow:0 4px 12px rgba(0,0,0,0.03);
    font-family:'Poppins',sans-serif;
    color:#333;
}

.search-wrapper input:focus{
    border-color:#c89b3c;
    box-shadow:0 8px 20px rgba(200,155,60,0.12);
}

.search-icon{
    position:absolute;
    left:18px;
    top:50%;
    transform:translateY(-50%);
    color:#999;
    font-size:15px;
}

.clear-search{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    text-decoration:none;
    color:#c89b3c;
    font-size:14px;
    font-weight:600;
    transition:0.3s;
}

.clear-search:hover{
    color:#8d6e63;
}

.hidden-search-btn{
    display:none;
}

/* ADD BUTTON */

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
    white-space:nowrap;
    flex-shrink:0;
    font-size:14px;
}

/* STATS */

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
    transition:background 0.3s,border-color 0.3s;
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
    transition:background 0.3s;
}

.card h2{
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
    overflow:auto;
    transition:background 0.3s,border-color 0.3s;
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
    transition:color 0.3s;
}

.delete-all{
    background:#ffefef;
    color:#ef5350;
    border:none;
    padding:12px 18px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    font-family:'Poppins',sans-serif;
}

.inventory-table{
    width:100%;
    border-collapse:collapse;
    min-width:1300px;
}

.inventory-table thead{
    background:#faf7f1;
    transition:background 0.3s;
}

.inventory-table th{
    text-align:left;
    color:#888;
    font-size:14px;
    padding:15px;
}

.inventory-table td{
    padding:15px;
    border-bottom:1px solid #f3f3f3;
    font-size:14px;
    transition:border-color 0.3s;
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
    transition:background 0.3s;
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
    transition:color 0.3s;
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
    border:none;
    cursor:pointer;
    font-size:14px;
}

.view{ background:#e8f0fe; color:#1a73e8; }
.edit{ background:#fff6e5; color:#c89b3c; }
.delete{ background:#ffefef; color:#ef5350; }

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

/* MODAL */

.modal-overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    z-index:999;
    align-items:center;
    justify-content:center;
}

.modal-overlay.open{
    display:flex;
}

.modal{
    background:white;
    border-radius:20px;
    padding:30px;
    width:90%;
    max-width:520px;
    position:relative;
    box-shadow:0 20px 60px rgba(0,0,0,0.2);
    transition:background 0.3s;
}

.modal-close{
    position:absolute;
    top:18px;
    right:18px;
    background:#f3f3f3;
    border:none;
    width:36px;
    height:36px;
    border-radius:50%;
    cursor:pointer;
    font-size:16px;
    color:#555;
    display:flex;
    align-items:center;
    justify-content:center;
}

.modal-close:hover{ background:#eee; }

.modal h3{
    font-size:20px;
    margin-bottom:20px;
    color:#5d4037;
    padding-right:40px;
    transition:color 0.3s;
}

.modal-img{
    width:100%;
    height:180px;
    border-radius:14px;
    overflow:hidden;
    background:#f3f3f3;
    margin-bottom:20px;
    transition:background 0.3s;
}

.modal-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.modal-no-image{
    display:flex;
    align-items:center;
    justify-content:center;
    height:100%;
    color:#aaa;
    font-size:13px;
}

.modal-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.modal-field label{
    font-size:11px;
    color:#aaa;
    text-transform:uppercase;
    letter-spacing:0.5px;
}

.modal-field p{
    font-size:14px;
    font-weight:600;
    color:#333;
    margin-top:3px;
    transition:color 0.3s;
}

/* =====================
   DARK MODE
   ===================== */

html.dark body{ background:#1a1410; color:#e8ddd0; }

html.dark .sidebar{ background:#211a14; border-color:#2e2318; }
html.dark .logo h1{ color:#e8c97a; }
html.dark .menu a{ color:#9e8a78; }
html.dark .menu a:hover,
html.dark .menu a.active{ background:#2e2318; color:#c89b3c; }
html.dark .sidebar-footer{ border-color:#2e2318; }
html.dark .user-name{ color:#e8ddd0; }

html.dark .dashboard-container{ background:#201811; }

html.dark .search-wrapper input{ background:#2a1f17; border-color:#3a2a1e; color:#e8ddd0; }
html.dark .search-wrapper input::placeholder{ color:#6e5a48; }
html.dark .search-wrapper input:focus{ background:#211a14; border-color:#c89b3c; }

html.dark .card{ background:#2a1f17; border-color:#3a2a1e; }
html.dark .card p{ color:#9e8a78; }
html.dark .card h2{ color:#e8ddd0; }
html.dark .card-icon{ background:#3a2a1e; }

html.dark .table-container{ background:#2a1f17; border-color:#3a2a1e; }
html.dark .table-header h2{ color:#e8c97a; }
html.dark .inventory-table thead{ background:#1a1410; }
html.dark .inventory-table th{ color:#9e8a78; }
html.dark .inventory-table td{ border-color:#3a2a1e; color:#e8ddd0; }
html.dark .product-name{ color:#e8ddd0; }
html.dark .brand{ color:#6e5a48; }
html.dark .product-img{ background:#3a2a1e; }

html.dark .modal{ background:#2a1f17; }
html.dark .modal h3{ color:#e8c97a; }
html.dark .modal-close{ background:#3a2a1e; color:#e8ddd0; }
html.dark .modal-close:hover{ background:#4a3a2e; }
html.dark .modal-field label{ color:#6e5a48; }
html.dark .modal-field p{ color:#e8ddd0; }
html.dark .modal-img{ background:#3a2a1e; }

html.dark .alert{ background:#1a2e1c; color:#81c784; }
html.dark .empty h2{ color:#9e8a78; }

/* RESPONSIVE */

@media(max-width:1000px){
    .sidebar{ position:relative; width:100%; height:auto; }
    .wrapper{ flex-direction:column; }
    .main{ width:100%; margin-left:0; }
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

            <!-- TOPBAR -->

            <div class="topbar">

                <form method="GET" class="search-box">

                    <div class="search-wrapper">

                        <i class="fas fa-search search-icon"></i>

                        <input
                            type="text"
                            name="search"
                            placeholder="Search chocolates..."
                            value="<?= htmlspecialchars($search) ?>"
                            autocomplete="off">

                        <?php if(!empty($search)): ?>
                        <a href="index.php" class="clear-search">Clear</a>
                        <?php endif; ?>

                        <button type="submit" class="hidden-search-btn">Search</button>

                    </div>

                </form>

                <a href="create.php" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add Product
                </a>

            </div>

            <!-- ALERT -->

            <?php if(isset($_GET['success'])): ?>
            <div class="alert">Action completed successfully!</div>
            <?php endif; ?>

            <!-- STATS -->

            <div class="stats">

                <div class="card">
                    <div class="card-icon"><i class="fas fa-box"></i></div>
                    <div>
                        <p>Total Products</p>
                        <h2><?= $totalRecords ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon"><i class="fas fa-cubes"></i></div>
                    <div>
                        <p>Total Stock</p>
                        <h2><?= $totalStock ?></h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon"><i class="fas fa-wallet"></i></div>
                    <div>
                        <p>Inventory Value</p>
                        <h2>&#8369;<?= number_format($totalValue,0) ?></h2>
                    </div>
                </div>

            </div>

            <!-- TABLE -->

            <div class="table-container">

                <div class="table-header">

                    <h2>All Chocolates</h2>

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
                    $imgStmt = $conn->prepare("SELECT image FROM chocolate_images WHERE chocolate_id=? LIMIT 1");
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
                                    <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
                                    <div class="brand"><?= htmlspecialchars($row['brand']) ?></div>
                                </div>
                            </div>
                        </td>

                        <td><span class="category-badge"><?= htmlspecialchars($row['category']) ?></span></td>
                        <td><?= htmlspecialchars($row['manufacturer']) ?></td>
                        <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                        <td><span class="qty-badge"><?= $row['quantity'] ?></span></td>
                        <td class="price">&#8369;<?= number_format($row['price'],2) ?></td>
                        <td><?= date('Y-m-d', strtotime($row['expiration_date'])) ?></td>

                        <td>
                            <div class="actions">

                                <button
                                    class="action-btn view"
                                    onclick="openModal(
                                        '<?= addslashes(htmlspecialchars($row['product_name'])) ?>',
                                        '<?= addslashes(htmlspecialchars($row['brand'])) ?>',
                                        '<?= addslashes(htmlspecialchars($row['category'])) ?>',
                                        '<?= addslashes(htmlspecialchars($row['manufacturer'])) ?>',
                                        '<?= addslashes(htmlspecialchars($row['supplier_name'])) ?>',
                                        '<?= $row['quantity'] ?>',
                                        '<?= number_format($row['price'],2) ?>',
                                        '<?= date('Y-m-d', strtotime($row['expiration_date'])) ?>',
                                        '<?= $img ? 'uploads/'.htmlspecialchars($img['image']) : '' ?>'
                                    )">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <a href="update.php?id=<?= $row['id'] ?>" class="action-btn edit">
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

                <?php else: ?>
                <div class="empty"><h2>No Products Found</h2></div>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<!-- VIEW MODAL -->
<div class="modal-overlay" id="viewModal">
    <div class="modal">

        <button class="modal-close" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>

        <h3 id="modal-title"></h3>

        <div class="modal-img" id="modal-img-wrap"></div>

        <div class="modal-grid">

            <div class="modal-field">
                <label>Brand</label>
                <p id="modal-brand"></p>
            </div>

            <div class="modal-field">
                <label>Category</label>
                <p id="modal-category"></p>
            </div>

            <div class="modal-field">
                <label>Manufacturer</label>
                <p id="modal-manufacturer"></p>
            </div>

            <div class="modal-field">
                <label>Supplier</label>
                <p id="modal-supplier"></p>
            </div>

            <div class="modal-field">
                <label>Quantity</label>
                <p id="modal-qty"></p>
            </div>

            <div class="modal-field">
                <label>Price</label>
                <p id="modal-price"></p>
            </div>

            <div class="modal-field" style="grid-column:1/-1">
                <label>Expiration Date</label>
                <p id="modal-expiry"></p>
            </div>

        </div>

    </div>
</div>

<script>
function openModal(name, brand, category, manufacturer, supplier, qty, price, expiry, img) {
    document.getElementById('modal-title').textContent = name;
    document.getElementById('modal-brand').textContent = brand;
    document.getElementById('modal-category').textContent = category;
    document.getElementById('modal-manufacturer').textContent = manufacturer;
    document.getElementById('modal-supplier').textContent = supplier;
    document.getElementById('modal-qty').textContent = qty;
    document.getElementById('modal-price').textContent = '&#8369;' + price;
    document.getElementById('modal-expiry').textContent = expiry;

    const imgWrap = document.getElementById('modal-img-wrap');
    imgWrap.innerHTML = img
        ? '<img src="' + img + '" alt="' + name + '">'
        : '<div class="modal-no-image">No Image Available</div>';

    document.getElementById('viewModal').classList.add('open');
}

function closeModal() {
    document.getElementById('viewModal').classList.remove('open');
}

document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

</body>
</html>