<?php
include 'db.php';

$success = "";

// UPDATE SYSTEM NAME
if(isset($_POST['save_settings'])){

    $system_name = trim($_POST['system_name']);
    $admin_name = trim($_POST['admin_name']);
    $theme = trim($_POST['theme']);

    $success = "Settings saved successfully!";
}
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

/* SETTINGS CARD */

.settings-card{
    background:white;
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    max-width:700px;
}

.form-group{
    margin-bottom:25px;
}

.form-group label{
    display:block;
    margin-bottom:10px;
    font-weight:600;
}

.form-group input,
.form-group select{
    width:100%;
    height:55px;
    border:none;
    background:#f7f7f7;
    border-radius:15px;
    padding:0 18px;
    outline:none;
    font-size:15px;
}

.save-btn{
    border:none;
    background:#c89b3c;
    color:white;
    padding:15px 30px;
    border-radius:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.save-btn:hover{
    transform:translateY(-3px);
}

.alert{
    background:#e8f5e9;
    color:#2e7d32;
    padding:15px 20px;
    border-radius:15px;
    margin-bottom:20px;
    font-weight:500;
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

            <a href="inventory.php">
                <i class="fas fa-box"></i>
                Inventory
            </a>

            <a href="create.php">
                <i class="fas fa-plus-circle"></i>
                Add Product
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

            <h1>System Settings</h1>

            <p>
                Manage your inventory system preferences.
            </p>

        </div>

        <?php if($success): ?>

        <div class="alert">
            <?= $success ?>
        </div>

        <?php endif; ?>

        <div class="settings-card">

            <form method="POST">

                <!-- SYSTEM NAME -->

                <div class="form-group">

                    <label>System Name</label>

                    <input
                    type="text"
                    name="system_name"
                    value="R & G Chocolate Inventory">

                </div>

                <!-- ADMIN NAME -->

                <div class="form-group">

                    <label>Administrator Name</label>

                    <input
                    type="text"
                    name="admin_name"
                    value="Administrator">

                </div>

                <!-- THEME -->

                <div class="form-group">

                    <label>Theme</label>

                    <select name="theme">

                        <option>Light Mode</option>
                        <option>Dark Mode</option>

                    </select>

                </div>

                <!-- SAVE BUTTON -->

                <button
                type="submit"
                name="save_settings"
                class="save-btn">

                    <i class="fas fa-save"></i>
                    Save Settings

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>