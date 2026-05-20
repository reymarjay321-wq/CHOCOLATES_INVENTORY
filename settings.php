<?php
include 'db.php';

$success = "";

if(isset($_POST['save_settings'])){
    $system_name = trim($_POST['system_name']);
    $admin_name  = trim($_POST['admin_name']);
    $theme       = trim($_POST['theme']);
    $success = "Settings saved successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings</title>

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

.alert-success{
    background:#e8f5e9;
    color:#2e7d32;
}

/* SETTINGS GRID */

.settings-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    align-items:start;
}

/* CARD */

.settings-card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
    transition:background 0.3s,border-color 0.3s;
}

/* SECTION TITLE */

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

/* INPUT GROUP */

.input-group{
    display:flex;
    flex-direction:column;
    gap:8px;
    margin-bottom:18px;
}

.input-group:last-of-type{
    margin-bottom:0;
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

/* TOGGLE ROW */

.toggle-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid #f3f3f3;
    transition:border-color 0.3s;
}

.toggle-row:last-child{
    border-bottom:none;
    padding-bottom:0;
}

.toggle-info{
    display:flex;
    flex-direction:column;
    gap:3px;
}

.toggle-label{
    font-size:14px;
    font-weight:600;
    color:#333;
    transition:color 0.3s;
}

.toggle-desc{
    font-size:12px;
    color:#999;
}

/* TOGGLE SWITCH */

.switch{
    position:relative;
    width:46px;
    height:26px;
    flex-shrink:0;
}

.switch input{
    opacity:0;
    width:0;
    height:0;
}

.slider{
    position:absolute;
    inset:0;
    background:#e0e0e0;
    border-radius:34px;
    cursor:pointer;
    transition:0.3s;
}

.slider:before{
    content:'';
    position:absolute;
    width:20px;
    height:20px;
    left:3px;
    bottom:3px;
    background:white;
    border-radius:50%;
    transition:0.3s;
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
}

.switch input:checked + .slider{
    background:#c89b3c;
}

.switch input:checked + .slider:before{
    transform:translateX(20px);
}

/* ABOUT CARD */

.about-card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #eee;
    text-align:center;
    transition:background 0.3s,border-color 0.3s;
}

.about-logo{
    font-size:48px;
    margin-bottom:12px;
}

.about-name{
    font-size:20px;
    font-weight:700;
    color:#5d4037;
    margin-bottom:4px;
    transition:color 0.3s;
}

.about-version{
    font-size:12px;
    color:#aaa;
    margin-bottom:20px;
}

.about-divider{
    border:none;
    border-top:1px solid #f3f3f3;
    margin:16px 0;
    transition:border-color 0.3s;
}

.about-row{
    display:flex;
    justify-content:space-between;
    font-size:13px;
    padding:6px 0;
    color:#555;
}

.about-row span:last-child{
    font-weight:600;
    color:#333;
}

/* DANGER ZONE */

.danger-card{
    background:white;
    border-radius:20px;
    padding:25px;
    border:1px solid #ffcdd2;
    grid-column:span 2;
    transition:background 0.3s,border-color 0.3s;
}

.danger-card .section-title{
    color:#c62828;
}

.danger-card .section-title i{
    background:#ffebee;
    color:#c62828;
}

.danger-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 0;
    border-bottom:1px solid #fff5f5;
    gap:20px;
    flex-wrap:wrap;
    transition:border-color 0.3s;
}

.danger-row:last-child{
    border-bottom:none;
    padding-bottom:0;
}

.danger-info .danger-title{
    font-size:14px;
    font-weight:600;
    color:#333;
    margin-bottom:3px;
    transition:color 0.3s;
}

.danger-info .danger-desc{
    font-size:12px;
    color:#999;
}

.btn-danger{
    background:#ffebee;
    color:#c62828;
    border:none;
    padding:10px 18px;
    border-radius:12px;
    font-family:'Poppins',sans-serif;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:0.3s;
    white-space:nowrap;
    flex-shrink:0;
}

.btn-danger:hover{
    background:#ffcdd2;
}

/* SAVE BUTTON */

.save-btn{
    background:#c89b3c;
    color:white;
    border:none;
    padding:14px 24px;
    border-radius:14px;
    font-family:'Poppins',sans-serif;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:10px;
    transition:0.3s;
    margin-top:22px;
    box-shadow:0 6px 18px rgba(200,155,60,0.3);
}

.save-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 24px rgba(200,155,60,0.4);
}

/* RESPONSIVE */

@media(max-width:1000px){
    .sidebar{ position:relative; width:100%; height:auto; }
    .wrapper{ flex-direction:column; }
    .main{ width:100%; margin-left:0; }
    .settings-grid{ grid-template-columns:1fr; }
    .danger-card{ grid-column:span 1; }
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

html.dark .settings-card,
html.dark .about-card,
html.dark .danger-card{ background:#2a1f17; border-color:#3a2a1e; }

html.dark .section-title{ color:#e8c97a; border-color:#3a2a1e; }
html.dark .section-title i{ background:#3a2a1e; }

html.dark .input-group label{ color:#c4a882; }
html.dark .input-group input,
html.dark .input-group select{ background:#1a1410; border-color:#3a2a1e; color:#e8ddd0; }
html.dark .input-group input:focus,
html.dark .input-group select:focus{ background:#211a14; border-color:#c89b3c; }

html.dark .toggle-row{ border-color:#3a2a1e; }
html.dark .toggle-label{ color:#e8ddd0; }
html.dark .toggle-desc{ color:#6e5a48; }

html.dark .about-name{ color:#e8c97a; }
html.dark .about-divider{ border-color:#3a2a1e; }
html.dark .about-row{ color:#9e8a78; }
html.dark .about-row span:last-child{ color:#e8ddd0; }

html.dark .danger-card{ border-color:#5c2020; }
html.dark .danger-row{ border-color:#3d1a1a; }
html.dark .danger-info .danger-title{ color:#e8ddd0; }

html.dark .alert-success{ background:#1a2e1c; color:#81c784; }

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

            <div class="topbar">
                <div>
                    <h2>System Settings</h2>
                    <p>Manage your inventory system preferences.</p>
                </div>
            </div>

            <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-circle-check"></i>
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <form method="POST">

                <div class="settings-grid">

                    <!-- GENERAL SETTINGS -->

                    <div class="settings-card">

                        <div class="section-title">
                            <i class="fas fa-sliders"></i>
                            General
                        </div>

                        <div class="input-group">
                            <label>System Name</label>
                            <input type="text" name="system_name" value="R & G Chocolate Inventory" placeholder="System name">
                        </div>

                        <div class="input-group">
                            <label>Administrator Name</label>
                            <input type="text" name="admin_name" value="Administrator" placeholder="Admin name">
                        </div>

                        <div class="input-group">
                            <label>Theme</label>
                            <select name="theme" id="themeSelect">
                                <option value="light">☀️ Light Mode</option>
                                <option value="dark">🌙 Dark Mode</option>
                            </select>
                        </div>

                        <button type="submit" name="save_settings" class="save-btn">
                            <i class="fas fa-floppy-disk"></i>
                            Save Settings
                        </button>

                    </div>

                    <!-- ABOUT -->

                    <div class="about-card">

                        <div class="about-logo">🍫</div>
                        <div class="about-name">R & G Chocolate</div>
                        <div class="about-version">Inventory System · v1.0.0</div>

                        <hr class="about-divider">

                        <div class="about-row">
                            <span>Version</span>
                            <span>1.0.0</span>
                        </div>
                        <div class="about-row">
                            <span>Database</span>
                            <span>MySQL</span>
                        </div>
                        <div class="about-row">
                            <span>Backend</span>
                            <span>PHP</span>
                        </div>
                        <div class="about-row">
                            <span>Last Updated</span>
                            <span><?= date('M d, Y') ?></span>
                        </div>

                    </div>

                    <!-- PREFERENCES -->

                    <div class="settings-card">

                        <div class="section-title">
                            <i class="fas fa-bell"></i>
                            Preferences
                        </div>

                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-label">Low Stock Alerts</div>
                                <div class="toggle-desc">Notify when quantity falls below threshold</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-label">Expiry Warnings</div>
                                <div class="toggle-desc">Show alerts for products nearing expiry</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-label">Confirm on Delete</div>
                                <div class="toggle-desc">Ask for confirmation before deleting</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="toggle-label">Auto-redirect After Save</div>
                                <div class="toggle-desc">Return to dashboard after adding a product</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider"></span>
                            </label>
                        </div>

                    </div>

                    <!-- DANGER ZONE -->

                    <div class="danger-card">

                        <div class="section-title">
                            <i class="fas fa-triangle-exclamation"></i>
                            Danger Zone
                        </div>

                        <div class="danger-row">
                            <div class="danger-info">
                                <div class="danger-title">Clear All Products</div>
                                <div class="danger-desc">Permanently delete all chocolate products and images from the inventory.</div>
                            </div>
                            <button type="button" class="btn-danger" onclick="return confirm('Delete ALL products? This cannot be undone.')">
                                <i class="fas fa-trash"></i>
                                Clear Inventory
                            </button>
                        </div>

                        <div class="danger-row">
                            <div class="danger-info">
                                <div class="danger-title">Reset System</div>
                                <div class="danger-desc">Reset all settings back to their default values.</div>
                            </div>
                            <button type="button" class="btn-danger" onclick="return confirm('Reset all settings to default?')">
                                <i class="fas fa-rotate-left"></i>
                                Reset Defaults
                            </button>
                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
const themeSelect = document.getElementById('themeSelect');

// Sync dropdown to current saved theme
const saved = localStorage.getItem('rg_theme') || 'light';
themeSelect.value = saved;

// Apply theme live when dropdown changes
themeSelect.addEventListener('change', function() {
    const val = this.value;
    localStorage.setItem('rg_theme', val);
    if(val === 'dark'){
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

// Also persist on form submit
document.querySelector('form').addEventListener('submit', function() {
    localStorage.setItem('rg_theme', themeSelect.value);
});
</script>

</body>
</html>