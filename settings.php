<?php
include 'db.php';

$success = "";

// SAVE SETTINGS
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

.settings-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:25px;
}
/* ALERT */

.alert{
    background:#e8f5e9;
    color:#2e7d32;
    padding:15px 20px;
    border-radius:15px;
    margin-bottom:20px;
    font-weight:500;
}

/* SETTINGS CARD */

.card{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    border-radius:22px;
    padding:35px;
    border:1px solid #eee;
    max-width:750px;
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
    margin-bottom:10px;
    font-weight:600;
    font-size:14px;
}

.setting-item input{
    width:100%;
    padding:14px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    font-size:14px;
    height:55px;
    border:1px solid #eee;
    background:#fafafa;
    border-radius:15px;
    padding:0 18px;
    outline:none;
    font-size:15px;
    transition:0.3s;
}

.form-group input:focus,
.form-group select:focus{
    border-color:#c89b3c;
    background:white;
}

.save-btn{
    background:#c89b3c;
    color:white;
    border:none;
    padding:14px 24px;
    padding:15px 28px;
    border-radius:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    font-size:15px;
}

.save-btn:hover{
    transform:translateY(-2px);
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

        <div>

            <div class="logo">
                <h1>🍫 R & G <br><span>Chocolate</span></h1>
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

        <div class="sidebar-footer">

            <a href="analytics.php">
                <i class="fas fa-chart-line"></i>
                Analytics
            </a>
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

            <h1>Settings</h1>

            <p>
                Manage your chocolate inventory system settings.
            </p>
            <div class="header">

                <h1>System Settings</h1>

                <p>
                    Manage your inventory system preferences.
                </p>

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

</div>

</body>
</html>