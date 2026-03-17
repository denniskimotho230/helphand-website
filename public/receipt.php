<?php
include('../includes/db.php');

// Get the last donation ID from the URL
if (!isset($_GET['id'])) {
    die("Invalid Access");
}
$donation_id = intval($_GET['id']);

$query = "SELECT * FROM donations WHERE id = $donation_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Donation record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Receipt | HELPHAND</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .receipt-card {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 8px solid #27ae60;
        }
        @media print {
            .no-print { display: none; }
            .receipt-card { box-shadow: none; border: 1px solid #eee; margin: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success">HELPHAND NGO</h2>
            <p class="text-muted">Official Donation Acknowledgment</p>
        </div>
        
        <hr>
        
        <div class="row my-4">
            <div class="col-6">
                <p class="text-muted mb-1">Donor Name:</p>
                <h5 class="fw-bold"><?php echo $data['donor_name']; ?></h5>
            </div>
            <div class="col-6 text-end">
                <p class="text-muted mb-1">Date:</p>
                <h5 class="fw-bold"><?php echo date('d M Y', strtotime($data['date_added'])); ?></h5>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Contribution to: <strong><?php echo $data['project_name']; ?></strong></td>
                    <td class="text-end">KES <?php echo number_format($data['amount'], 2); ?></td>
                </tr>
                <tr>
                    <td><small class="text-muted">Payment Method: <?php echo $data['payment_method']; ?></small></td>
                    <td></td>
                </tr>
                <tr>
                    <td><small class="text-muted">Transaction Ref: <?php echo $data['ref_no']; ?></small></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-end">Total Amount Received:</th>
                    <th class="text-end text-success">KES <?php echo number_format($data['amount'], 2); ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 text-center">
            <p class="small text-muted">Thank you for your generosity. This receipt is valid only for verified transactions.</p>
            <div class="no-print mt-4">
                <button onclick="window.print()" class="btn btn-success px-4 me-2">Download PDF / Print</button>
                <a href="index.php" class="btn btn-outline-secondary">Return Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>