```php
<?php
// about.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - R & G Chocolate Inventory</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI', sans-serif;
    background:#f5f1eb;
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#6d4c41,#3e2723);
    color:white;
    padding:60px 20px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

.header h1{
    font-size:3rem;
    margin-bottom:10px;
}

.header p{
    font-size:1.1rem;
    opacity:0.9;
}

/* CONTAINER */
.container{
    max-width:1000px;
    margin:40px auto;
    padding:20px;
}

/* CARD */
.card{
    background:white;
    border-radius:20px;
    padding:40px;
    box-shadow:0 15px 40px rgba(0,0,0,0.1);
    animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.icon{
    text-align:center;
    margin-bottom:25px;
}

.icon i{
    font-size:5rem;
    color:#ff9800;
}

.section-title{
    color:#5d4037;
    margin-bottom:15px;
    font-size:1.8rem;
}

.text{
    color:#555;
    line-height:1.8;
    margin-bottom:25px;
}

/* FEATURES */
.features{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-top:30px;
}

.feature-box{
    background:#faf7f3;
    border-radius:15px;
    padding:25px;
    transition:0.3s ease;
    border:1px solid #eee;
}

.feature-box:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.feature-box i{
    font-size:2rem;
    color:#ff9800;
    margin-bottom:15px;
}

.feature-box h3{
    color:#5d4037;
    margin-bottom:10px;
}

/* BUTTON */
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:10px;
    margin-top:35px;
    padding:15px 30px;
    background:linear-gradient(135deg,#ff9800,#f57c00);
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-weight:600;
    transition:0.3s ease;
    box-shadow:0 8px 20px rgba(255,152,0,0.3);
}

.back-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(255,152,0,0.4);
}

/* FOOTER */
.footer{
    text-align:center;
    padding:20px;
    color:#777;
    font-size:14px;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h1><i class="fas fa-chocolate-bar"></i> About Us</h1>
    <p>R & G Chocolate Inventory Management System</p>
</div>

<!-- CONTENT -->
<div class="container">
    
    <div class="card">

        <div class="icon">
            <i class="fas fa-box-open"></i>
        </div>

        <h2 class="section-title">About the System</h2>

        <p class="text">
            The R & G Chocolate Inventory System is a modern inventory management
            platform designed to help businesses manage chocolate products efficiently.
            It allows users to organize inventory records, monitor stock quantities,
            track expiration dates, manage suppliers, and upload product images.
        </p>

        <p class="text">
            This system provides an easy-to-use interface with professional design
            and responsive features for better productivity and inventory tracking.
        </p>

        <h2 class="section-title">Main Features</h2>

        <div class="features">

            <div class="feature-box">
                <i class="fas fa-plus-circle"></i>
                <h3>Add Products</h3>
                <p>Easily add new chocolate products with complete details.</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-search"></i>
                <h3>Search Inventory</h3>
                <p>Quickly search products by name, brand, category, or supplier.</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-image"></i>
                <h3>Upload Images</h3>
                <p>Attach product images for better inventory visualization.</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-calendar-alt"></i>
                <h3>Track Expiry Dates</h3>
                <p>Monitor expiration dates to avoid expired products.</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-boxes"></i>
                <h3>Manage Stocks</h3>
                <p>Keep track of available product quantities in real time.</p>
            </div>

            <div class="feature-box">
                <i class="fas fa-user-tie"></i>
                <h3>Supplier Management</h3>
                <p>Store and organize supplier information efficiently.</p>
            </div>

        </div>

        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    © <?php echo date("Y"); ?> R & G Chocolate Inventory System
</div>

</body>
</html>
```
