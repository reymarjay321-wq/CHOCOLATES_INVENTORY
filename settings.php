<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Settings</title>

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
    background:white;
    padding:30px 20px;
    box-shadow:0 0 30px rgba(0,0,0,0.06);
    position:fixed;
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

/* MAIN */

.main{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:30px;
}

.header{
    margin-bottom:30px;
}

.header h1{
    font-size:36px;
    margin-bottom:8px;
}

.header p{
    color:#888;
}

.settings-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:25px;
}

.card{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.card h2{
    margin-bottom:15px;
    color:#5d4037;
}

.setting-item{
    margin-bottom:20px;
}

.setting-item label{
    display:block;
    margin-bottom:8px;
    font-weight:500;
}

.setting-item input{
    width:100%;
    padding:14px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    font-size:14px;
}

.save-btn{
    background:#c89b3c;
    color:white;
    border:none;
    padding:14px 24px;
    border-radius:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.save-btn:hover{
    transform:translateY(-2px);
}

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

</style>
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="logo">
            <h1>🍫 R & G <span>Chocolate</span></h1>
        </div>

        <div class="menu">

            <a href="index.php">
                <i class="fas fa-chart-pie"></i>
                Dashboard
            </a>

            <a href="analytics.php">
                <i class="fas fa-chart-line"></i>
                Analytics
            </a>

            <a href="settings.php" class="active">
                <i class="fas fa-cog"></i>
                Settings
            </a>

        </div>

    </div>

    <!-- MAIN -->

    <div class="main">

        <div class="header">

            <h1>Settings</h1>

            <p>
                Manage your chocolate inventory system settings.
            </p>

        </div>

        <div class="settings-grid">

            <div class="card">

                <h2>Profile Settings</h2>

                <div class="setting-item">
                    <label>Administrator Name</label>
                    <input type="text" value="Administrator">
                </div>

                <div class="setting-item">
                    <label>Email Address</label>
                    <input type="email" value="admin@email.com">
                </div>

                <button class="save-btn">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>

            </div>

            <div class="card">

                <h2>System Settings</h2>

                <div class="setting-item">
                    <label>Store Name</label>
                    <input type="text" value="R & G Chocolate">
                </div>

                <div class="setting-item">
                    <label>Currency</label>
                    <input type="text" value="PHP Peso (₱)">
                </div>

                <button class="save-btn">
                    <i class="fas fa-cog"></i>
                    Update Settings
                </button>

            </div>

        </div>

    </div>

</div>

</body>
</html>