<?php 
include('../includes/db.php'); 
include('../includes/auth.php'); 

// 1. Fetch Financial Totals
// Total for Children Home Support specifically
$res_ch = mysqli_query($conn, "SELECT SUM(amount) as total FROM donations WHERE status = 'Verified' AND project_name = 'Children Home Support'");
$total_ch = mysqli_fetch_assoc($res_ch)['total'] ?? 0;

// Total Verified (All Projects)
$res_v = mysqli_query($conn, "SELECT SUM(amount) as total FROM donations WHERE status = 'Verified'");
$total_verified = mysqli_fetch_assoc($res_v)['total'] ?? 0;

// Pending count for the notification bell
$res_p = mysqli_query($conn, "SELECT COUNT(*) as count FROM donations WHERE status = 'Pending'");
$pending_count = mysqli_fetch_assoc($res_p)['count'] ?? 0;

// 2. Data for the Chart (Payment Methods)
$pay_query = mysqli_query($conn, "SELECT payment_method, COUNT(*) as count FROM donations GROUP BY payment_method");
$methods = []; $counts = [];
while($row = mysqli_fetch_assoc($pay_query)) {
    $methods[] = $row['payment_method'];
    $counts[] = $row['count'];
}

// 3. Fetch Recent Donations for the Table
$recent_query = mysqli_query($conn, "SELECT * FROM donations ORDER BY id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | HELPHAND Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #27ae60; --dark-blue: #2c3e50; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        /* Fixed Sidebar Styling to prevent overlapping */
        .main-content { margin-left: 260px; padding: 40px; transition: 0.3s; }
        
        /* OVERLAP FIX: Force spacing for sidebar buttons */
        .sidebar-footer-controls {
            position: absolute;
            bottom: 20px;
            width: 85%;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            gap: 10px; /* This creates the gap between overlapping buttons */
        }
        
        /* Ensure buttons inside the sidebar don't stack poorly */
        .sidebar-footer-controls a {
            display: block;
            width: 100%;
            margin-bottom: 8px !important;
        }
        
        .stat-card { border: none; border-radius: 15px; padding: 25px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .bg-gradient-green { background: linear-gradient(45deg, #1d976c, #93f9b9); }
        .bg-gradient-blue { background: linear-gradient(45deg, #2193b0, #6dd5ed); }
        .bg-gradient-dark { background: linear-gradient(45deg, #2c3e50, #4ca1af); }
        
        .card-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 3rem; opacity: 0.3; }
        
        .table-card { border: none; border-radius: 15px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; margin-top: 30px; }
        .badge-pending { background-color: #fef9e7; color: #f1c40f; border: 1px solid #f9e79f; }
        .badge-verified { background-color: #eafaf1; color: #27ae60; border: 1px solid #d5f5e3; }
        
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Admin Dashboard</h2>
        <div class="text-end">
            <span class="text-muted d-block">Status: Connected to Database (Port 8080)</span>
            <a href="../public/index.php" target="_blank" class="small text-decoration-none text-success fw-bold">
                <i class="fas fa-external-link-alt"></i> Public Site
            </a>
        </div>
    </div>

    <?php if($pending_count > 0): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
        <span><i class="fas fa-bell fa-beat me-2"></i> You have <strong><?php echo $pending_count; ?></strong> new donations waiting for your verification.</span>
        <a href="#donations-table" class="btn btn-sm btn-dark">View Now</a>
    </div>
    <?php endif; ?>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card bg-gradient-green">
                <small class="text-uppercase fw-bold opacity-75">Children Home Fund</small>
                <h2 class="fw-bold mt-2">KES <?php echo number_format($total_ch, 2); ?></h2>
                <i class="fas fa-child-reaching card-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient-blue">
                <small class="text-uppercase fw-bold opacity-75">Total Bank Balance</small>
                <h2 class="fw-bold mt-2">KES <?php echo number_format($total_verified, 2); ?></h2>
                <i class="fas fa-building-columns card-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient-dark">
                <small class="text-uppercase fw-bold opacity-75">Verified Transactions</small>
                <h2 class="fw-bold mt-2"><?php echo array_sum($counts); ?> Total</h2>
                <i class="fas fa-receipt card-icon"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-4">Payment Method Distribution</h5>
                <canvas id="paymentChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-3">Core Management</h5>
                <a href="manage_donations.php" class="btn btn-light border mb-2 text-start p-3 d-flex align-items-center">
                    <i class="fas fa-check-double text-success me-3 fs-4"></i> 
                    <div><span class="d-block fw-bold">Verification Center</span><small class="text-muted">Process pending receipts</small></div>
                </a>
                <a href="manage_projects.php" class="btn btn-light border mb-2 text-start p-3 d-flex align-items-center">
                    <i class="fas fa-folder-plus text-primary me-3 fs-4"></i>
                    <div><span class="d-block fw-bold">Project Manager</span><small class="text-muted">Add or edit NGO causes</small></div>
                </a>
                <a href="admin_ledger.php" class="btn btn-light border mb-2 text-start p-3 d-flex align-items-center">
                    <i class="fas fa-file-invoice text-warning me-3 fs-4"></i>
                    <div><span class="d-block fw-bold">Financial Reports</span><small class="text-muted">Export data to Excel/PDF</small></div>
                </a>
            </div>
        </div>
    </div>

    <div class="table-card" id="donations-table">
        <h5 class="fw-bold mb-4">Recent Donation Activity</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Donor</th>
                        <th>Project</th>
                        <th>Amount</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($recent_query)): ?>
                    <tr>
                        <td>
                            <span class="fw-bold"><?php echo htmlspecialchars($row['donor_name']); ?></span><br>
                            <small class="text-muted"><?php echo $row['payment_method']; ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td class="fw-bold text-dark">KES <?php echo number_format($row['amount']); ?></td>
                        <td><code class="text-primary"><?php echo $row['ref_no']; ?></code></td>
                        <td>
                            <?php if($row['status'] == 'Pending'): ?>
                                <span class="badge badge-pending p-2 text-warning">Pending Verification</span>
                            <?php else: ?>
                                <span class="badge badge-verified p-2 text-success">Verified</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'Pending'): ?>
                                <a href="verify_action.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Confirm payment received?')">Verify</a>
                            <?php else: ?>
                                <a href="../public/generate_receipt.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Receipt</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($methods); ?>,
            datasets: [{
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: ['#27ae60', '#3498db', '#f1c40f', '#e74c3c', '#9b59b6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: { 
            plugins: { 
                legend: { position: 'right', labels: { usePointStyle: true, padding: 20 } } 
            },
            maintainAspectRatio: false
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>