<?php
include(__DIR__ . '/auth.php');
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/sidebar.php');

// Fetch total donations per project
$chart_data = mysqli_query($conn, "SELECT project_name, SUM(amount) as total FROM donations WHERE status='Verified' GROUP BY project_name");
$names = $totals = [];
while($row = mysqli_fetch_assoc($chart_data)) {
    $names[] = $row['project_name'];
    $totals[] = $row['total'];
}

// Summary statistics
$grand_total = array_sum($totals);
$total_donors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM donors"))['count'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="main-content">
<h2>Fundraising Reports</h2>
<div class="row">
<div class="col-md-8">
<div class="card p-3 mb-4">
<h5>Donations per Project</h5>
<canvas id="chart"></canvas>
</div>
</div>
<div class="col-md-4">
<div class="card p-3 mb-4">
<h5>Summary</h5>
<hr>
<p>Total Funds Raised: <strong>KES <?php echo number_format($grand_total); ?></strong></p>
<p>Registered Donors: <strong><?php echo $total_donors; ?></strong></p>
</div>
</div>
</div>
</div>
<script>
const ctx = document.getElementById('chart');
new Chart(ctx, {
    type:'bar',
    data:{
        labels: <?php echo json_encode($names); ?>,
        datasets:[{
            label:'Total Raised (KES)',
            data: <?php echo json_encode($totals); ?>,
            backgroundColor:'rgba(39,174,96,0.7)',
            borderColor:'#27ae60',
            borderWidth:1
        }]
    },
    options:{
        responsive:true,
        scales:{ y:{ beginAtZero:true } }
    }
});
</script>
</body>
</html>
