<?php
include 'db.php';

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    $product_name = trim($_POST['product_name']);
    $brand = trim($_POST['brand']);
    $category = trim($_POST['category']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : null;
    $manufacturer = trim($_POST['manufacturer']);
    $supplier_name = trim($_POST['supplier_name']);

    if (empty($product_name)) {

        $error = "Product name is required!";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO chocolates
            (
                product_name,
                brand,
                category,
                quantity,
                price,
                expiration_date,
                manufacturer,
                supplier_name
            )
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

            // IMAGE UPLOAD
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
                            INSERT INTO chocolate_images
                            (chocolate_id, image)
                            VALUES (?, ?)
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

/* ALERT */

.alert{
    padding:16px 20px;
    border-radius:15px;
    margin-bottom:20px;
    font-weight:500;
}

.alert-error{
    background:#ffebee;
    color:#c62828;
}

/* FORM */

.form-card{
    background:white;
    border-radius:22px;
    padding:35px;
    border:1px solid #eee;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:22px;
}

.full{
    grid-column:span 2;
}

.input-group{
    display:flex;
    flex-direction:column;
}

.input-group label{
    margin-bottom:10px;
    font-weight:600;
    font-size:14px;
}


/* INPUTS */
input{

input,
select{

    width:100%;
    height:55px;
    border:1px solid #eee;
    background:#fafafa;
    border-radius:15px;
    padding:0 18px;
    outline:none;
    font-size:15px;
    transition:0.3s;
}


input:focus{
    border-color:#ff9800;
=======
input:focus,
select:focus{
    border-color:#c89b3c;

    background:white;
}

input[type="file"]{
    padding:14px;
    height:auto;
}

/* BUTTONS */

.button-group{
    margin-top:30px;
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.btn{
    padding:15px 28px;
    border:none;
    border-radius:14px;
    text-decoration:none;
    color:white;
    font-weight:600;
    cursor:pointer;
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
}

.btn-secondary{
    background:#757575;
}

/* MOBILE */

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

@media(max-width:768px){

    .form-grid{
        grid-template-columns:1fr;
    }

    .full{
        grid-column:span 1;
    }

    .btn{
        width:100%;
        justify-content:center;
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

            <!-- DASHBOARD -->

            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </a>

            <!-- ANALYTICS -->

            <a href="analytics.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                Analytics
            </a>

            <!-- SETTINGS -->

            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                Settings
            </a>

        </div>

    </div>

    <!-- USER -->

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

                <h1>Add New Chocolate</h1>

                <p>
                    Add delicious chocolate products into your inventory.
                </p>

            </div>

            <?php if (!empty($error)): ?>

            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>

            <?php endif; ?>

            <div class="form-card">


                    <input
                    type="text"
                    name="category"
                    placeholder="Enter category"
                    required>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-grid">

                        <div class="input-group full">

                            <label>Product Name</label>

                            <input
                            type="text"
                            name="product_name"
                            placeholder="Enter chocolate name"
                            required>

                        </div>


                        <div class="input-group">

                            <label>Brand</label>

                            <input
                            type="text"
                            name="brand"
                            placeholder="Cadbury">

                        </div>

                        <div class="input-group">

                            <label>Category</label>

                            <select name="category" required>

                                <option value="">Select Category</option>

                                <option value="Milk Chocolate">
                                    Milk Chocolate
                                </option>

                                <option value="Dark Chocolate">
                                    Dark Chocolate
                                </option>

                                <option value="White Chocolate">
                                    White Chocolate
                                </option>

                            </select>

                        </div>

                        <div class="input-group">

                            <label>Quantity</label>

                            <input
                            type="number"
                            name="quantity"
                            placeholder="0"
                            required>

                        </div>

                        <div class="input-group">

                            <label>Price</label>

                            <input
                            type="number"
                            step="0.01"
                            name="price"
                            placeholder="0.00"
                            required>

                        </div>

                        <div class="input-group">

                            <label>Expiration Date</label>

                            <input
                            type="date"
                            name="expiration_date">

                        </div>

                        <div class="input-group full">

                            <label>Manufacturer</label>

                            <input
                            type="text"
                            name="manufacturer"
                            placeholder="Manufacturer name">

                        </div>

                        <div class="input-group full">

                            <label>Supplier</label>

                            <input
                            type="text"
                            name="supplier_name"
                            placeholder="Supplier name">

                        </div>

                        <div class="input-group full">

                            <label>Upload Images</label>

                            <input
                            type="file"
                            name="images[]"
                            multiple
                            accept="image/*">

                        </div>

                    </div>

                    <div class="button-group">

                        <button
                        type="submit"
                        name="submit"
                        class="btn btn-primary">

                            <i class="fas fa-plus"></i>
                            Add Chocolate

                        </button>

                        <a href="index.php" class="btn btn-secondary">

                            <i class="fas fa-arrow-left"></i>
                            Back

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>