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
<title>Edit Chocolate</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f1eb;
    margin: 0;
}

/* Container */
.container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}

/* Header */
.header {
    background: linear-gradient(135deg, #4e342e, #8d6e63);
    color: #fff;
    padding: 20px;
    text-align: center;
}

/* Content */
.content {
    padding: 25px;
}

/* Info card */
.info {
    background: #fff3e0;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.info strong {
    font-size: 18px;
}

/* Form */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 13px;
    color: #666;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-top: 5px;
}

/* Buttons */
.btn {
    padding: 10px 15px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: #6d4c41;
    color: white;
}

.btn-secondary {
    background: #ccc;
}

/* Alerts */
.alert {
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
}
</style>

</head>
<body>

<div class="container">

<div class="header">
    <h2><i class="fas fa-edit"></i> Edit Chocolate</h2>
</div>

<div class="content">

<?php if ($success): ?>
<div class="alert alert-success">Updated successfully!</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="info">
    <strong><?= htmlspecialchars($row['product_name']) ?></strong><br>
    Brand: <?= htmlspecialchars($row['brand'] ?: '-') ?> |
    ₱<?= number_format($row['price'],2) ?> |
    Stock: <?= $row['quantity'] ?>
</div>

<form method="POST">

<div class="form-group">
<label>Product Name</label>
<input type="text" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>" required>
</div>

<div class="form-group">
<label>Brand</label>
<input type="text" name="brand" value="<?= htmlspecialchars($row['brand']) ?>">
</div>

<div class="form-group">
<label>Category</label>
<input type="text" name="category" value="<?= htmlspecialchars($row['category']) ?>">
</div>

<div class="form-group">
<label>Quantity</label>
<input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="0">
</div>

<div class="form-group">
<label>Price</label>
<input type="number" name="price" value="<?= $row['price'] ?>" step="0.01">
</div>

<div class="form-group">
<label>Expiration Date</label>
<input type="date" name="expiration_date" value="<?= $row['expiration_date'] ?>">
</div>

<div class="form-group">
<label>Manufacturer</label>
<input type="text" name="manufacturer" value="<?= htmlspecialchars($row['manufacturer']) ?>">
</div>

<div class="form-group">
<label>Supplier</label>
<input type="text" name="supplier_name" value="<?= htmlspecialchars($row['supplier_name']) ?>">
</div>

<button type="submit" name="update" class="btn btn-primary">Update</button>
<a href="index.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<script>
// Format price
document.querySelector('[name="price"]').addEventListener('blur', function() {
    let v = parseFloat(this.value);
    if (!isNaN(v)) this.value = v.toFixed(2);
});

// Redirect
<?php if ($success): ?>
setTimeout(() => {
    window.location.href = "index.php?updated=1";
}, 1200);
<?php endif; ?>
</script>

</body>
</html>