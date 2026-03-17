<?php 
include('../includes/db.php'); 
include('../includes/auth.php'); 

// Fetch unique donors
$query = "SELECT donor_name, SUM(amount) as total_given, MAX(date_added) as last_donation 
          FROM donations GROUP BY donor_name ORDER BY total_given DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donors | HELPHAND</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .main-content { margin-left: 260px; padding: 40px; }
        .donor-badge { width: 40px; height: 40px; background: #27ae60; color: white; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 10px; }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2 class="fw-bold mb-4">Donor Directory</h2>
    <div class="card border-0 shadow-sm p-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Donor Name</th>
                    <th>Total Contributed</th>
                    <th>Last Activity</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <div class="donor-badge"><?php echo strtoupper(substr($row['donor_name'], 0, 1)); ?></div>
                        <strong><?php echo $row['donor_name']; ?></strong>
                    </td>
                    <td>KES <?php echo number_format($row['total_given'], 2); ?></td>
                    <td class="text-muted small"><?php echo date('d M Y', strtotime($row['last_donation'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>