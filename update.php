<?php
include 'db.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID!");
}
$id = (int)$_GET['id'];

$success = false;
$error = "";

// Fetch chocolate
$stmt = $conn->prepare("SELECT * FROM chocolates WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Chocolate not found!");
}
$row = $result->fetch_assoc();
$stmt->close();

// Update
if (isset($_POST['update'])) {

    $product_name    = trim($_POST['product_name']);
    $brand           = trim($_POST['brand']);
    $category        = trim($_POST['category']);
    $quantity        = (int)$_POST['quantity'];
    $price           = (float)$_POST['price'];
    $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $manufacturer    = trim($_POST['manufacturer']);
    $supplier_name   = trim($_POST['supplier_name']);

    if (empty($product_name)) {
        $error = "Product name is required!";
    } elseif ($quantity < 0) {
        $error = "Quantity cannot be negative!";
    } elseif ($price < 0) {
        $error = "Price cannot be negative!";
    } else {

        $stmt = $conn->prepare("UPDATE chocolates SET
            product_name=?, brand=?, category=?, quantity=?, price=?,
            expiration_date=?, manufacturer=?, supplier_name=?
            WHERE id=?");

        $stmt->bind_param("sssidsssi",
            $product_name,
            $brand,
            $category,
            $quantity,
            $price,
            $expiration_date,
            $manufacturer,
            $supplier_name,
            $id
        );

        if ($stmt->execute()) {
            $success = true;
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM chocolates WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Update failed!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Chocolate</title>

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

.topbar h2{
    font-size:22px;
    color:#5d4037;
}

.topbar p{
    font-size:13px;
    color:#888;
    margin-top:3px;
}

.back-btn{
    background:white;
    color:#777;
    border:1px solid #eee;
    padding:14px 22px;
    border-radius:14px;
    font-weight:600;
    font-size:14px;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:10px;
    transition:0.3s;
    box-shadow:0 4px 12px rgba(0,0,0,0.03);
    white-space:nowrap;
    flex-shrink:0;
}

.back-btn:hover{
    border-color:#c89b3c;
    color:#c89b3c;
}

/* ALERTS */

.alert{
    padding:16px 20px;
    border-radius:18px;
    margin-bottom:20px;
    font-size:14px;
    font-weight:500;
    display:flex;
    align-items:center;
    gap:12px;
}

.alert-success{
    background:#e8f5e9;
    color:#2e7d32;
}

.alert-error{
    background:#ffebee;
    color:#c62828;
}

/* PRODUCT INFO CARD */

.info-card{
    background:white;
    border-radius:18px;
    padding:20px 24px;
    border:1px solid #eee;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:16px;
}

.info-card-icon{
    width:52px;
    height:52px;
    border-radius:14px;
    background:#f8efd9;
    color:#c89b3c;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    flex-shrink:0;
}

.info-card-name{
    font-size:17px;
    font-weight:700;
    color:#5d4037;
}

.info-card-meta{
    font-size:13px;
    color:#999;
    margin-top:3px;
}

.info-card-meta span{
    margin-right:14px;
}

/* FORM CARD */

.form-card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
}

/* SECTION */

.form-section{
    margin-bottom:28px;
}

.form-section:last-of-type{
    margin-bottom:0;
}

.section-title{
    font-size:15px;
    font-weight:600;
    color:#5d4037;
    margin-bottom:18px;
    padding-bottom:12px;
    border-bottom:1px solid #f3f3f3;
    display:flex;
    align-items:center;
    gap:10px;
}

.section-title i{
    width:32px;
    height:32px;
    border-radius:10px;
    background:#f8efd9;
    color:#c89b3c;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:13px;
}

/* GRID */

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}

.full{
    grid-column:span 2;
}

/* INPUT GROUP */

.input-group{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.input-group label{
    font-size:13px;
    font-weight:600;
    color:#555;
}

.input-group input{
    width:100%;
    height:52px;
    border:1px solid #eee;
    background:#fafafa;
    border-radius:14px;
    padding:0 18px;
    outline:none;
    font-family:'Poppins',sans-serif;
    font-size:14px;
    color:#333;
    transition:0.3s;
}

.input-group input::placeholder{
    color:#bbb;
}

.input-group input:focus{
    border-color:#c89b3c;
    background:white;
    box-shadow:0 4px 16px rgba(200,155,60,0.1);
}

/* BUTTONS */

.button-group{
    display:flex;
    gap:14px;
    margin-top:24px;
    flex-wrap:wrap;
}

.btn{
    padding:14px 24px;
    border:none;
    border-radius:14px;
    font-family:'Poppins',sans-serif;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:10px;
    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
}

.btn-primary{
    background:#c89b3c;
    color:white;
    box-shadow:0 6px 18px rgba(200,155,60,0.3);
}

.btn-primary:hover{
    box-shadow:0 10px 24px rgba(200,155,60,0.4);
}

.btn-secondary{
    background:white;
    color:#777;
    border:1px solid #eee;
}

.btn-secondary:hover{
    border-color:#c89b3c;
    color:#c89b3c;
}

/* RESPONSIVE */

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

@media(max-width:700px){
    .form-grid{
        grid-template-columns:1fr;
    }
    .full{
        grid-column:span 1;
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

                <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>

                <a href="analytics.php" class="<?= basename($_SERVER['PHP_SELF'])=='analytics.php'?'active':'' ?>">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>

                <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF'])=='settings.php'?'active':'' ?>">
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

                <div>
                    <h2>Edit Chocolate</h2>
                    <p>Update the details for this product.</p>
                </div>

                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>

            </div>

            <!-- ALERTS -->

            <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-circle-check"></i>
                Updated successfully! Redirecting...
            </div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <!-- PRODUCT INFO CARD -->

            <div class="info-card">

                <div class="info-card-icon">
                    <i class="fas fa-box"></i>
                </div>

                <div>
                    <div class="info-card-name">
                        <?= htmlspecialchars($row['product_name']) ?>
                    </div>
                    <div class="info-card-meta">
                        <span><i class="fas fa-copyright" style="margin-right:5px;font-size:11px;"></i><?= htmlspecialchars($row['brand'] ?: '—') ?></span>
                        <span><i class="fas fa-peso-sign" style="margin-right:4px;font-size:11px;"></i><?= number_format($row['price'],2) ?></span>
                        <span><i class="fas fa-cubes" style="margin-right:5px;font-size:11px;"></i>Stock: <?= $row['quantity'] ?></span>
                    </div>
                </div>

            </div>

            <!-- FORM CARD -->

            <div class="form-card">

                <form method="POST">

                    <!-- PRODUCT INFO -->

                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-tag"></i>
                            Product Information
                        </div>

                        <div class="form-grid">

                            <div class="input-group full">
                                <label>Product Name</label>
                                <input type="text" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>" required>
                            </div>

                            <div class="input-group">
                                <label>Brand</label>
                                <input type="text" name="brand" value="<?= htmlspecialchars($row['brand']) ?>">
                            </div>

                            <div class="input-group">
                                <label>Category</label>
                                <input
                                    type="text"
                                    name="category"
                                    list="category-list"
                                    value="<?= htmlspecialchars($row['category']) ?>"
                                    autocomplete="off">
                                <datalist id="category-list">
                                    <option value="Milk Chocolate">
                                    <option value="Dark Chocolate">
                                    <option value="White Chocolate">
                                </datalist>
                            </div>

                        </div>

                    </div>

                    <!-- STOCK & PRICING -->

                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-boxes-stacked"></i>
                            Stock & Pricing
                        </div>

                        <div class="form-grid">

                            <div class="input-group">
                                <label>Quantity</label>
                                <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="0" required>
                            </div>

                            <div class="input-group">
                                <label>Price (₱)</label>
                                <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" min="0" required>
                            </div>

                            <div class="input-group full">
                                <label>Expiration Date</label>
                                <input type="date" name="expiration_date" value="<?= $row['expiration_date'] ?>">
                            </div>

                        </div>

                    </div>

                    <!-- SUPPLIER & MANUFACTURER -->

                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-truck"></i>
                            Supplier & Manufacturer
                        </div>

                        <div class="form-grid">

                            <div class="input-group">
                                <label>Manufacturer</label>
                                <input type="text" name="manufacturer" value="<?= htmlspecialchars($row['manufacturer']) ?>">
                            </div>

                            <div class="input-group">
                                <label>Supplier</label>
                                <input type="text" name="supplier_name" value="<?= htmlspecialchars($row['supplier_name']) ?>">
                            </div>

                        </div>

                    </div>

                    <!-- BUTTONS -->

                    <div class="button-group">

                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fas fa-floppy-disk"></i>
                            Save Changes
                        </button>

                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-xmark"></i>
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
document.querySelector('[name="price"]').addEventListener('blur', function() {
    let v = parseFloat(this.value);
    if (!isNaN(v)) this.value = v.toFixed(2);
});

<?php if($success): ?>
setTimeout(() => {
    window.location.href = "index.php?success=updated";
}, 1200);
<?php endif; ?>
</script>

</body>
</html>