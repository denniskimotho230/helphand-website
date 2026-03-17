<?php 
include('../includes/db.php'); 
include('../includes/auth.php'); 

// Fetch only Verified donations for the ledger
$query = "SELECT * FROM donations WHERE status = 'Verified' ORDER BY date_added DESC";
$result = mysqli_query($conn, $query);

// Calculate total verified funds
$total_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM donations WHERE status = 'Verified'");
$total_row = mysqli_fetch_assoc($total_query);
$grand_total = $total_row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Ledger | HELPHAND Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #27ae60; --dark-blue: #2c3e50; }
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        .main-content { margin-left: 260px; padding: 40px; transition: 0.3s; }
        
        /* Dashboard-style Cards */
        .ledger-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            overflow: hidden;
            border: none;
        }
        
        /* Matching Dashboard Gradient */
        .bg-gradient-green { background: linear-gradient(45deg, #1d976c, #93f9b9); color: white; }
        .bg-gradient-dark { background: linear-gradient(45deg, #2c3e50, #4ca1af); color: white; }
        
        .search-container { position: relative; max-width: 400px; }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; }
        .search-container input { padding-left: 40px; border-radius: 50px; border: 1px solid #ddd; }

        @media print { 
            .no-print, #sidebar, .search-container { display: none !important; } 
            .main-content { margin-left: 0; padding: 0; } 
            .ledger-card { box-shadow: none; border: 1px solid #ddd; }
        }
        
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="fw-bold text-dark m-0">Financial Ledger</h2>
            <p class="text-muted small">Official record of all verified KES transactions.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark px-3">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="export_csv.php" class="btn btn-success px-3">
                <i class="fas fa-file-excel me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <div class="card border-0 bg-gradient-green shadow-sm mb-4 p-4 rounded-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="text-uppercase small fw-bold mb-1 opacity-75">Total Verified Revenue</p>
                <h1 class="fw-bold mb-0">KES <?php echo number_format($grand_total, 2); ?></h1>
            </div>
            <i class="fas fa-vault fa-3x opacity-25"></i>
        </div>
    </div>

    <div class="search-container mb-4 no-print">
        <i class="fas fa-search"></i>
        <input type="text" id="ledgerSearch" class="form-control form-control-lg" placeholder="Search by donor, project or ref code...">
    </div>

    <div class="ledger-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="ledgerTable">
                <thead class="bg-gradient-dark">
                    <tr>
                        <th class="ps-4 py-3">Date</th>
                        <th class="py-3">Donor Details</th>
                        <th class="py-3">Project</th>
                        <th class="py-3">Reference</th>
                        <th class="text-end pe-4 py-3">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="ledger-row">
                            <td class="ps-4">
                                <span class="d-block fw-bold"><?php echo date('d M, Y', strtotime($row['date_added'])); ?></span>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($row['date_added'])); ?></small>
                            </td>
                            <td>
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['donor_name']); ?></span>
                                <br><small class="badge bg-light text-dark border"><?php echo $row['payment_method']; ?></small>
                            </td>
                            <td class="project-name"><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><code class="text-primary fw-bold"><?php echo $row['ref_no']; ?></code></td>
                            <td class="text-end pe-4">
                                <span class="fw-bold text-success">KES <?php echo number_format($row['amount'], 2); ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No verified transactions found in the database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 no-print">
        <a href="dashboard.php" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>

<script>
    document.getElementById('ledgerSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.ledger-row');
        
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>