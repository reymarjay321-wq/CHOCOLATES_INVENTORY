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

    // VALIDATION
    if (empty($product_name)) {

        $error = "Product name is required!";

    } else {

        // INSERT PRODUCT
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* BODY */
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(to bottom,#f8f5f1,#efe7dd);
    min-height:100vh;
    padding:40px 20px;
}

/* CONTAINER */
.container{
    max-width:1000px;
    margin:auto;
    background:white;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,0.12);
    animation:fadeUp 0.6s ease;
}

/* HEADER */
.header{
    position:relative;
    background:linear-gradient(135deg,#4e342e,#8d6e63);
    padding:40px;
    text-align:center;
    color:white;
}

.header h2{
    font-size:2.3rem;
    margin-bottom:10px;
}

.header p{
    opacity:0.9;
    font-size:15px;
}

/* CONTENT */
.content{
    padding:40px;
}

/* ALERT */
.alert{
    padding:16px 20px;
    border-radius:14px;
    margin-bottom:25px;
    font-weight:500;
}

.alert-error{
    background:#ffebee;
    color:#c62828;
    border-left:5px solid #e53935;
}

/* FORM GRID */
.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:25px;
}

.full{
    grid-column:span 2;
}

/* INPUT GROUP */
.input-group{
    display:flex;
    flex-direction:column;
}

.input-group label{
    margin-bottom:10px;
    font-weight:600;
    color:#5d4037;
}

/* INPUTS */
input,
select{
    width:100%;
    padding:15px 18px;
    border-radius:16px;
    border:2px solid #eee;
    background:#fafafa;
    font-size:15px;
    transition:0.3s;
    outline:none;
}

input:focus,
select:focus{
    border-color:#ff9800;
    background:white;
    box-shadow:0 8px 25px rgba(255,152,0,0.15);
    transform:translateY(-2px);
}

/* FILE INPUT */
input[type="file"]{
    background:white;
    padding:18px;
    border:2px dashed #ccc;
    cursor:pointer;
}

/* BUTTONS */
.button-group{
    margin-top:35px;
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.btn{
    padding:15px 28px;
    border:none;
    border-radius:50px;
    text-decoration:none;
    color:white;
    font-weight:600;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:10px;
    transition:0.3s;
    font-size:15px;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

.btn:hover{
    transform:translateY(-4px);
}

.btn-primary{
    background:linear-gradient(135deg,#ff9800,#f57c00);
}

.btn-secondary{
    background:linear-gradient(135deg,#9e9e9e,#757575);
}

/* ICON BOX */
.icon-box{
    width:90px;
    height:90px;
    background:rgba(255,255,255,0.12);
    margin:0 auto 20px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    backdrop-filter:blur(10px);
}

.icon-box i{
    font-size:40px;
}

/* ANIMATION */
@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* RESPONSIVE */
@media(max-width:768px){

    .form-grid{
        grid-template-columns:1fr;
    }

    .full{
        grid-column:span 1;
    }

    .content{
        padding:25px;
    }

    .header h2{
        font-size:1.8rem;
    }

    .btn{
        width:100%;
        justify-content:center;
    }
}

</style>

</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div class="icon-box">
            <i class="fas fa-cookie-bite"></i>
        </div>

        <h2>Add New Chocolate</h2>

        <p>
            Add delicious chocolate products into your inventory system.
        </p>

    </div>

    <!-- CONTENT -->
    <div class="content">

        <?php if (!empty($error)): ?>

        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>

        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <!-- PRODUCT NAME -->
                <div class="input-group full">

                    <label>
                        <i class="fas fa-box"></i>
                        Product Name
                    </label>

                    <input
                    type="text"
                    name="product_name"
                    placeholder="Enter chocolate product name"
                    required>

                </div>

                <!-- BRAND -->
                <div class="input-group">

                    <label>
                        <i class="fas fa-copyright"></i>
                        Brand
                    </label>

                    <input
                    type="text"
                    name="brand"
                    placeholder="Cadbury">

                </div>

                <!-- CATEGORY -->
                <div class="input-group">

                    <label>
                        <i class="fas fa-layer-group"></i>
                        Category
                    </label>

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

                <!-- QUANTITY -->
                <div class="input-group">

                    <label>
                        <i class="fas fa-cubes"></i>
                        Quantity
                    </label>

                    <input
                    type="number"
                    name="quantity"
                    placeholder="0"
                    required>

                </div>

                <!-- PRICE -->
                <div class="input-group">

                    <label>
                        <i class="fas fa-peso-sign"></i>
                        Price
                    </label>

                    <input
                    type="number"
                    step="0.01"
                    name="price"
                    placeholder="0.00"
                    required>

                </div>

                <!-- EXPIRATION -->
                <div class="input-group">

                    <label>
                        <i class="fas fa-calendar"></i>
                        Expiration Date
                    </label>

                    <input
                    type="date"
                    name="expiration_date">

                </div>

                <!-- MANUFACTURER -->
                <div class="input-group full">

                    <label>
                        <i class="fas fa-industry"></i>
                        Manufacturer
                    </label>

                    <input
                    type="text"
                    name="manufacturer"
                    placeholder="Manufacturer name">

                </div>

                <!-- SUPPLIER -->
                <div class="input-group full">

                    <label>
                        <i class="fas fa-truck"></i>
                        Supplier
                    </label>

                    <input
                    type="text"
                    name="supplier_name"
                    placeholder="Supplier name">

                </div>

                <!-- IMAGES -->
                <div class="input-group full">

                    <label>
                        <i class="fas fa-image"></i>
                        Upload Images
                    </label>

                    <input
                    type="file"
                    name="images[]"
                    multiple
                    accept="image/*">

                </div>

            </div>

            <!-- BUTTONS -->
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

</body>
</html>