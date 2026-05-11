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
        ORDER BY date_added DESC
    ");

    $like = "%$search%";

    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT * FROM chocolates
        ORDER BY date_added DESC
    ");
}

$totalRecords = $result ? $result->num_rows : 0;


// DELETE ALL
if (isset($_POST['delete_all'])) {

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

<title>R & G Chocolate Inventory</title>

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
    color:#333;
    overflow-x:hidden;
}

/* CONTAINER */
.container{
    width:100%;
    min-height:100vh;
}

/* HEADER */
.header{
    position:relative;
    height:320px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    border-bottom-left-radius:30px;
    border-bottom-right-radius:30px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

.header-video{
    position:absolute;
    width:100%;
    height:100%;
    object-fit:cover;
}

.header-overlay{
    position:absolute;
    width:100%;
    height:100%;
    background:linear-gradient(
        135deg,
        rgba(20,10,5,0.75),
        rgba(0,0,0,0.6)
    );
}

.header-content{
    position:relative;
    z-index:2;
    text-align:center;
    color:white;
    animation:fadeUp 1s ease;
}

.header-content h1{
    font-size:4rem;
    font-weight:700;
    text-shadow:0 5px 20px rgba(0,0,0,0.5);
}

.header-content p{
    margin-top:15px;
    font-size:18px;
}

/* ABOUT BUTTON */
.about-btn{
    position:absolute;
    top:25px;
    right:25px;
    padding:14px 24px;
    border-radius:50px;
    text-decoration:none;
    color:white;
    background:rgba(255,255,255,0.12);
    border:1px solid rgba(255,255,255,0.3);
    backdrop-filter:blur(10px);
    font-weight:600;
    display:flex;
    align-items:center;
    gap:10px;
    transition:0.3s;
    z-index:10;
}

.about-btn:hover{
    transform:translateY(-3px);
    background:rgba(255,255,255,0.2);
}

/* CONTENT */
.content{
    max-width:1400px;
    margin:auto;
    padding:40px 30px;
}

/* CONTROLS */
.controls{
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    align-items:center;
    margin-bottom:35px;
}

/* SEARCH */
.search-wrapper{
    flex:1;
    min-width:320px;
}

.search-form{
    position:relative;
}

.search-input{
    width:100%;
    height:68px;
    border:none;
    border-radius:50px;
    padding:0 80px 0 28px;
    font-size:17px;
    background:white;
    box-shadow:0 8px 30px rgba(0,0,0,0.08);
    outline:none;
    transition:0.3s;
}

.search-input:focus{
    transform:translateY(-2px);
    box-shadow:0 12px 35px rgba(255,152,0,0.25);
}

.search-button{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    width:52px;
    height:52px;
    border:none;
    border-radius:50%;
    background:linear-gradient(135deg,#ff9800,#ff6d00);
    color:white;
    font-size:18px;
    cursor:pointer;
}

.search-button:hover{
    transform:translateY(-50%) scale(1.08);
}

/* BUTTONS */
.btn{
    border:none;
    padding:16px 28px;
    border-radius:50px;
    color:white;
    text-decoration:none;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:10px;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
}

.btn:hover{
    transform:translateY(-4px);
}

.btn-add{
    background:linear-gradient(135deg,#ff9800,#ff6d00);
}

.btn-danger{
    background:linear-gradient(135deg,#ef5350,#c62828);
}

.btn-primary{
    background:linear-gradient(135deg,#42a5f5,#1565c0);
}

.btn-success{
    background:linear-gradient(135deg,#66bb6a,#2e7d32);
}

/* TABLE */
.table-wrapper{
    overflow-x:auto;
    border-radius:25px;
    background:white;
    box-shadow:0 15px 40px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:linear-gradient(135deg,#5d4037,#3e2723);
    color:white;
    padding:22px;
}

td{
    padding:22px;
    border-bottom:1px solid #f1f1f1;
}

tr:hover{
    background:#fff8f0;
}

/* IMAGE */
.product-img{
    width:65px;
    height:65px;
    border-radius:16px;
    object-fit:cover;
}

/* BADGES */
.category-badge{
    padding:8px 18px;
    border-radius:30px;
    background:#e3f2fd;
    color:#1565c0;
    font-size:13px;
    font-weight:600;
}

.qty-badge{
    padding:8px 18px;
    border-radius:30px;
    background:#e8f5e9;
    color:#2e7d32;
    font-size:13px;
    font-weight:600;
}

/* ALERT */
.alert{
    background:white;
    border-left:6px solid #43a047;
    padding:20px;
    border-radius:18px;
    margin-bottom:25px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

/* NO DATA */
.no-data{
    background:white;
    padding:70px 30px;
    text-align:center;
    border-radius:30px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
}

/* MODAL */
.about-modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.7);
    backdrop-filter:blur(5px);
    z-index:999;
}

.modal-content{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:90%;
    max-width:550px;
    background:white;
    border-radius:30px;
    padding:40px;
    text-align:center;
}

.close-modal{
    position:absolute;
    right:20px;
    top:15px;
    font-size:28px;
    cursor:pointer;
}

/* FOOTER */
.footer{
    text-align:center;
    padding:30px;
    color:#777;
    font-size:14px;
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

/* MOBILE */
@media(max-width:768px){

    .header{
        height:280px;
    }

    .header-content h1{
        font-size:2.2rem;
    }

    .controls{
        flex-direction:column;
    }

    .search-wrapper{
        width:100%;
    }

    .btn{
        width:100%;
        justify-content:center;
    }

    td,th{
        padding:15px;
    }
}

</style>
</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">

    <video autoplay muted loop class="header-video">
        <source src="img\uploads\ssstik.io_@maximumraw_1776216849081.mp4">
    </video>

    <div class="header-overlay"></div>

    <a href="#" class="about-btn" onclick="openAbout()">
        <i class="fas fa-info-circle"></i> About
    </a>

    <div class="header-content">
        <h1>🍫 R & G Chocolate Inventory</h1>
        <p>Total Items: <?= $totalRecords ?></p>
    </div>

</div>

<div class="content">

<?php if(isset($_GET['success'])): ?>

<div class="alert">
    <i class="fas fa-check-circle"></i>
    Action completed successfully!
</div>

<?php endif; ?>

<!-- CONTROLS -->
<div class="controls">

<div class="search-wrapper">

<form method="GET" class="search-form">

<input
type="text"
name="search"
class="search-input"
placeholder="Search chocolate..."
value="<?= htmlspecialchars($search) ?>"
autocomplete="off">

<button type="submit" class="search-button">
    <i class="fas fa-search"></i>
</button>

</form>

</div>

<a href="create.php" class="btn btn-add">
    <i class="fas fa-plus"></i> Add New
</a>

<?php if($totalRecords > 0): ?>

<form method="POST">

<button
type="submit"
name="delete_all"
class="btn btn-danger"
onclick="return confirm('Delete all items?')">

<i class="fas fa-trash"></i> Delete All

</button>

</form>

<?php endif; ?>

<?php if($search): ?>

<a href="index.php" class="btn btn-success">
    <i class="fas fa-sync-alt"></i> Reset
</a>

<?php endif; ?>

</div>

<!-- TABLE -->
<?php if($totalRecords > 0): ?>

<div class="table-wrapper">

<table>

<thead>

<tr>
<th><i class="fas fa-image"></i></th>
<th>ID</th>
<th>Name</th>
<th>Brand</th>
<th>Category</th>
<th>Price</th>
<th>Expiry</th>
<th>Supplier</th>
<th>Qty</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td>

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

<?php if($img): ?>

<img
src="uploads/<?= htmlspecialchars($img['image']) ?>"
class="product-img">

<?php else: ?>

No Image

<?php endif; ?>

</td>

<td><strong>#<?= $row['id'] ?></strong></td>

<td>
<strong><?= htmlspecialchars($row['product_name']) ?></strong>
</td>

<td><?= htmlspecialchars($row['brand']) ?></td>

<td>
<span class="category-badge">
<?= htmlspecialchars($row['category']) ?>
</span>
</td>

<td>
<strong>₱<?= number_format($row['price'],2) ?></strong>
</td>

<td>
<?= date('M j, Y', strtotime($row['expiration_date'])) ?>
</td>

<td>
<?= htmlspecialchars($row['supplier_name']) ?>
</td>

<td>
<span class="qty-badge">
<?= $row['quantity'] ?>
</span>
</td>

<td style="display:flex; gap:10px;">

<a
href="update.php?id=<?= $row['id'] ?>"
class="btn btn-primary"
style="padding:12px 16px;">

<i class="fas fa-edit"></i>

</a>

<a
href="delete.php?id=<?= $row['id'] ?>"
class="btn btn-danger"
style="padding:12px 16px;"
onclick="return confirm('Delete this item?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php else: ?>

<div class="no-data">

<i class="fas fa-box-open"
style="font-size:5rem;color:#ddd;margin-bottom:20px;">
</i>

<h2>No chocolates found</h2>

<p style="margin:15px 0;color:#777;">
Start by adding your first chocolate product.
</p>

<a href="create.php" class="btn btn-add">
    <i class="fas fa-plus"></i> Add Chocolate
</a>

</div>

<?php endif; ?>

</div>

<!-- FOOTER -->
<div class="footer">
    <i class="fas fa-cookie-bite"></i>
    © 2026 R & G Chocolate Inventory System
</div>

</div>

<!-- ABOUT MODAL -->
<div id="aboutModal" class="about-modal">

<div class="modal-content">

<span class="close-modal" onclick="closeAbout()">
    &times;
</span>

<div style="margin-bottom:20px;">

<i class="fas fa-cookie-bite"
style="font-size:70px;color:#ff9800;">
</i>

</div>

<h2>R & G Chocolate Inventory</h2>

<p style="margin-top:15px; line-height:1.8; color:#666;">

This system helps manage chocolate inventory,
track products, monitor stock quantity,
and organize supplier information efficiently.

</p>

</div>

</div>

<script>

function openAbout(){
    document.getElementById('aboutModal').style.display='block';
}

function closeAbout(){
    document.getElementById('aboutModal').style.display='none';
}

window.onclick=function(event){

    let modal=document.getElementById('aboutModal');

    if(event.target==modal){
        modal.style.display='none';
    }
}

</script>

</body>
</html>