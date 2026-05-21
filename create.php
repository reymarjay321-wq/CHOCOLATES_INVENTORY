<?php
include 'db.php';

$error = "";

if (isset($_POST['submit'])) {

    $product_name   = trim($_POST['product_name']);
    $brand          = trim($_POST['brand']);
    $category       = trim($_POST['category']);
    $quantity       = (int)$_POST['quantity'];
    $price          = (float)$_POST['price'];
    $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $manufacturer   = trim($_POST['manufacturer']);
    $supplier_name  = trim($_POST['supplier_name']);

    if (empty($product_name)) {

        $error = "Product name is required!";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO chocolates
            (product_name, brand, category, quantity, price, expiration_date, manufacturer, supplier_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssidsss",
            $product_name,
            $brand,
            $category,
            $quantity,
            $price,
            $expiration_date,
            $manufacturer,
            $supplier_name
        );

        if ($stmt->execute()) {

            $product_id = $conn->insert_id;

            if (!empty($_FILES['images']['name'][0])) {

                $targetDir = "uploads/";

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                foreach ($_FILES['images']['name'] as $key => $name) {

                    $tmp = $_FILES['images']['tmp_name'][$key];

                    if (!file_exists($tmp)) continue;

                    $newName = time() . "_" . uniqid() . "_" . basename($name);

                    if (move_uploaded_file($tmp, $targetDir . $newName)) {

                        $stmtImg = $conn->prepare("
                            INSERT INTO chocolate_images (chocolate_id, image) VALUES (?, ?)
                        ");

                        $stmtImg->bind_param("is", $product_id, $newName);
                        $stmtImg->execute();
                    }
                }
            }

            header("Location: index.php?success=added");
            exit();

        } else {
            $error = "Insert failed!";
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
<title>Add Chocolate</title>

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

.topbar h2{
    font-size:22px;
    color:#5d4037;
    transition:color 0.3s;
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

/* ALERT */

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

.alert-error{
    background:#ffebee;
    color:#c62828;
}

/* FORM CARD */

.form-card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
    transition:background 0.3s,border-color 0.3s;
}

/* SECTION */

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
    transition:color 0.3s,border-color 0.3s;
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
    transition:background 0.3s;
}

.form-section{
    margin-bottom:28px;
}

.form-section:last-of-type{
    margin-bottom:0;
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
    transition:color 0.3s;
}

.input-group input,
.input-group select{
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
    appearance:none;
    -webkit-appearance:none;
}

.input-group input::placeholder{
    color:#bbb;
}

.input-group input:focus,
.input-group select:focus{
    border-color:#c89b3c;
    background:white;
    box-shadow:0 4px 16px rgba(200,155,60,0.1);
}

/* FILE UPLOAD */

.file-label{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
    padding:28px 20px;
    border:2px dashed #eee;
    border-radius:14px;
    background:#fafafa;
    cursor:pointer;
    transition:0.3s;
    text-align:center;
    position:relative;
}

.file-label:hover{
    border-color:#c89b3c;
    background:#fdf8f0;
}

.file-label input[type="file"]{
    position:absolute;
    inset:0;
    opacity:0;
    cursor:pointer;
    width:100%;
    height:100%;
}

.file-icon{
    width:48px;
    height:48px;
    border-radius:50%;
    background:#f8efd9;
    color:#c89b3c;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    transition:background 0.3s;
}

.file-text{
    font-size:14px;
    font-weight:600;
    color:#555;
    transition:color 0.3s;
}

.file-hint{
    font-size:12px;
    color:#aaa;
}

.file-names{
    font-size:13px;
    color:#c89b3c;
    font-weight:600;
}

/* BUTTON GROUP */

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
    transition:background 0.3s,border-color 0.3s,color 0.3s;
}

.btn-secondary:hover{
    border-color:#c89b3c;
    color:#c89b3c;
}

/* RESPONSIVE */

@media(max-width:1000px){
    .sidebar{ position:relative; width:100%; height:auto; }
    .wrapper{ flex-direction:column; }
    .main{ width:100%; margin-left:0; }
}

@media(max-width:700px){
    .form-grid{ grid-template-columns:1fr; }
    .full{ grid-column:span 1; }
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
html.dark .topbar h2{ color:#e8c97a; }
html.dark .topbar p{ color:#6e5a48; }

html.dark .back-btn{
    background:#2a1f17;
    border-color:#3a2a1e;
    color:#9e8a78;
}
html.dark .back-btn:hover{
    border-color:#c89b3c;
    color:#c89b3c;
}

html.dark .form-card{ background:#2a1f17; border-color:#3a2a1e; }

html.dark .section-title{ color:#e8c97a; border-color:#3a2a1e; }
html.dark .section-title i{ background:#3a2a1e; }

html.dark .input-group label{ color:#c4a882; }

html.dark .input-group input,
html.dark .input-group select{
    background:#1a1410;
    border-color:#3a2a1e;
    color:#e8ddd0;
}
html.dark .input-group input::placeholder{ color:#6e5a48; }
html.dark .input-group input:focus,
html.dark .input-group select:focus{
    background:#211a14;
    border-color:#c89b3c;
}

html.dark .file-label{
    background:#1a1410;
    border-color:#3a2a1e;
}
html.dark .file-label:hover{
    border-color:#c89b3c;
    background:#211a14;
}
html.dark .file-icon{ background:#3a2a1e; }
html.dark .file-text{ color:#c4a882; }
html.dark .file-hint{ color:#6e5a48; }

html.dark .btn-secondary{
    background:#2a1f17;
    border-color:#3a2a1e;
    color:#9e8a78;
}
html.dark .btn-secondary:hover{
    border-color:#c89b3c;
    color:#c89b3c;
}

html.dark .alert-error{ background:#3d1a1a; color:#ef9a9a; }

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
                    <h2>Add New Chocolate</h2>
                    <p>Fill in the details below to add a product to your inventory.</p>
                </div>

                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>

            </div>

            <!-- ALERT -->

            <?php if(!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <!-- FORM CARD -->

            <div class="form-card">

                <form method="POST" enctype="multipart/form-data">

                    <!-- PRODUCT INFO -->

                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-tag"></i>
                            Product Information
                        </div>

                        <div class="form-grid">

                            <div class="input-group full">
                                <label>Product Name</label>
                                <input type="text" name="product_name" placeholder="e.g. Dark Truffle Bar" required>
                            </div>

                            <div class="input-group">
                                <label>Brand</label>
                                <input type="text" name="brand" placeholder="e.g. Cadbury">
                            </div>

                            <div class="input-group">
                                <label>Category</label>
                                <input
                                    type="text"
                                    name="category"
                                    list="category-list"
                                    placeholder="e.g. Milk Chocolate"
                                    autocomplete="off"
                                    required>
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
                                <input type="number" name="quantity" placeholder="0" min="0" required>
                            </div>

                            <div class="input-group">
                                <label>Price (&#8369;)</label>
                                <input type="number" step="0.01" name="price" placeholder="0.00" min="0" required>
                            </div>

                            <div class="input-group full">
                                <label>Expiration Date</label>
                                <input type="date" name="expiration_date">
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
                                <input type="text" name="manufacturer" placeholder="e.g. Nestlé Philippines">
                            </div>

                            <div class="input-group">
                                <label>Supplier</label>
                                <input type="text" name="supplier_name" placeholder="e.g. Sweet Goods Co.">
                            </div>

                        </div>

                    </div>

                    <!-- IMAGES -->

                    <div class="form-section">

                        <div class="section-title">
                            <i class="fas fa-image"></i>
                            Product Images
                        </div>

                        <div class="input-group">
                            <label class="file-label" id="uploadArea">

                                <input
                                    type="file"
                                    name="images[]"
                                    multiple
                                    accept="image/*"
                                    id="fileInput">

                                <div class="file-icon">
                                    <i class="fas fa-cloud-arrow-up"></i>
                                </div>

                                <div class="file-text">Click to upload or drag & drop</div>
                                <div class="file-hint">PNG, JPG, WEBP · Multiple files allowed</div>
                                <div class="file-names" id="fileNames"></div>

                            </label>
                        </div>

                    </div>

                    <!-- BUTTONS -->

                    <div class="button-group">

                        <button type="submit" name="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Chocolate
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
const fileInput = document.getElementById('fileInput');
const fileNames = document.getElementById('fileNames');
const uploadArea = document.getElementById('uploadArea');

fileInput.addEventListener('change', function() {
    const files = Array.from(this.files);
    fileNames.textContent = files.length === 1
        ? files[0].name
        : files.length > 1
            ? files.length + ' files selected'
            : '';
});

uploadArea.addEventListener('dragover', e => {
    e.preventDefault();
    uploadArea.style.borderColor = '#c89b3c';
    uploadArea.style.background = '#fdf8f0';
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.style.borderColor = '';
    uploadArea.style.background = '';
});

uploadArea.addEventListener('drop', () => {
    uploadArea.style.borderColor = '';
    uploadArea.style.background = '';
});
</script>

</body>
</html>