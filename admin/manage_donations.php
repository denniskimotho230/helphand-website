<?php 
// 1. Connection and Security
include('../includes/db.php'); 
include('../includes/auth.php'); 

// 2. Handle Actions (Verify or Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'verify') {
        // Fetch details for the email receipt first
        $donor_res = mysqli_query($conn, "SELECT * FROM donations WHERE id = $id");
        $donor = mysqli_fetch_assoc($donor_res);

        if ($donor) {
            // Update status
            $update = "UPDATE donations SET status = 'Verified' WHERE id = $id";
            mysqli_query($conn, $update);

            // Prepare Email Content
            $to = "donor@example.com"; // In production, use $donor['email']
            $subject = "Official Donation Receipt - HELPHAND NGO";
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #27ae60;'>Thank You, " . htmlspecialchars($donor['donor_name']) . "!</h2>
                <p>Your contribution to <strong>" . $donor['project_name'] . "</strong> has been verified.</p>
                <hr>
                <p><strong>Amount:</strong> KES " . number_format($donor['amount'], 2) . "</p>
                <p><strong>Ref Number:</strong> " . $donor['ref_no'] . "</p>
                <p><strong>Payment Method:</strong> " . $donor['payment_method'] . "</p>
                <hr>
                <p style='font-size: 12px; color: #777;'>Verified on: " . date('Y-m-d H:i:s') . "</p>
            </body>
            </html>";
            
            // To send on a live server:
            // $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
            // mail($to, $subject, $message, $headers);

            header("Location: manage_donations.php?msg=Donation Verified & Receipt Generated");
        }
    } elseif ($action == 'delete') {
        mysqli_query($conn, "DELETE FROM donations WHERE id = $id");
        header("Location: manage_donations.php?msg=Record Deleted");
    }
    exit();
}

// 3. Fetch All Donations
$query = "SELECT * FROM donations ORDER BY date_added DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donations | HELPHAND</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; }
        .main-content { margin-left: 260px; padding: 40px; }
        .table-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .ref-tag { font-family: monospace; background: #eef2ff; color: #3730a3; padding: 3px 8px; border-radius: 5px; font-weight: bold; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Donation Records</h2>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Donor Name</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Ref Number</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['donor_name']); ?></strong></td>
                        <td class="text-success fw-bold">KES <?php echo number_format($row['amount'], 2); ?></td>
                        <td><span class="badge border text-dark bg-light"><?php echo $row['payment_method']; ?></span></td>
                        <td><span class="ref-tag"><?php echo $row['ref_no']; ?></span></td>
                        <td class="small"><?php echo $row['project_name']; ?></td>
                        <td>
                            <?php if($row['status'] == 'Verified'): ?>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Verified</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'Pending'): ?>
                                <a href="manage_donations.php?action=verify&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-success px-3 rounded-pill"
                                   onclick="return confirm('Confirm this payment against your bank/M-Pesa records?')">Verify</a>
                            <?php endif; ?>
                            <a href="manage_donations.php?action=delete&id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm text-danger ms-2"
                               onclick="return confirm('Delete this record permanently?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>