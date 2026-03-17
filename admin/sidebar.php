<?php
// Fix the path to point to includes/auth.php
include(__DIR__ . '/../includes/auth.php');
?>

<style>
:root { 
    --sidebar-width: 260px;
    --sidebar-bg: #1a252f;
    --accent-color: #27ae60;
    --hover-bg: #2c3e50;
}
#sidebar { 
    position: fixed; 
    top:0; 
    left:0; 
    width:var(--sidebar-width); 
    height:100vh; 
    background:var(--sidebar-bg); 
    color:#fff; 
    overflow-y:auto; 
    z-index:1000; 
    padding-top:20px; 
    box-shadow:4px 0 12px rgba(0,0,0,0.1); 
    display: flex;
    flex-direction: column; /* Allows us to push footer to bottom */
}
#sidebar .sidebar-header { padding:20px; text-align:center; border-bottom:1px solid #2c3e50; }
#sidebar .sidebar-header h3 { margin:0; font-size:1.3rem; font-weight:700; }
#sidebar ul { list-style:none; padding:0; margin:0; flex-grow: 1; } /* flex-grow fills the space */
#sidebar ul li a { display:flex; align-items:center; color:#adb5bd; text-decoration:none; padding:12px 20px; font-weight:500; border-left:4px solid transparent; transition:all 0.2s; }
#sidebar ul li a i { margin-right:10px; width: 20px; text-align: center; }
#sidebar ul li a:hover { background:var(--hover-bg); color:#fff; border-left:4px solid var(--accent-color); }
#sidebar ul li.active a { background:var(--hover-bg); color:#fff; font-weight:600; border-left:4px solid var(--accent-color); }

.nav-label { font-size:0.65rem; font-weight:700; color:#6c757d; padding:15px 20px 5px; text-transform:uppercase; letter-spacing:1px; display: block; }

/* FIX: New Sidebar Footer Logic */
.sidebar-footer {
    padding: 20px;
    border-top: 1px solid #2c3e50;
    margin-top: auto; /* Pushes to the bottom */
}
.sidebar-footer .btn-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: 0.3s;
    margin-bottom: 10px;
}
.btn-public {
    background: transparent;
    border: 1px solid var(--accent-color);
    color: var(--accent-color);
}
.btn-public:hover {
    background: var(--accent-color);
    color: #fff;
}
.btn-logout {
    border: 1px solid #dc3545;
    color: #dc3545;
}
.btn-logout:hover {
    background: #dc3545;
    color: #fff;
}

@media(max-width:768px){ 
    #sidebar { position:relative; width:100%; height:auto; } 
    .main-content { margin-left:0 !important; } 
}
</style>

<div id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-hand-holding-heart text-success me-2"></i>HELPHAND</h3>
        <p class="text-muted small">Welcome, Admin</p>
    </div>
    
    <ul>
        <li class="<?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <span class="nav-label">Management</span>
        <li class="<?php echo basename($_SERVER['PHP_SELF'])=='manage_donors.php'?'active':''; ?>">
            <a href="manage_donors.php"><i class="fas fa-users-cog"></i> Manage Donors</a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF'])=='manage_projects.php'?'active':''; ?>">
            <a href="manage_projects.php"><i class="fas fa-tasks"></i> Manage Projects</a>
        </li>
        <span class="nav-label">Finance & Growth</span>
        <li class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_ledger.php'?'active':''; ?>">
            <a href="admin_ledger.php"><i class="fas fa-book"></i> Project Ledgers</a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF'])=='manage_donations.php'?'active':''; ?>">
            <a href="manage_donations.php"><i class="fas fa-file-invoice-dollar"></i> All Donations</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../public/index.php" target="_blank" class="btn-footer btn-public">
            <i class="fas fa-globe me-2"></i> View Site
        </a>
        <a href="../logout.php" class="btn-footer btn-logout">
            <i class="fas fa-power-off me-2"></i> Logout
        </a>
    </div>
</div>