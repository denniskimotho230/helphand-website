<?php
include('../includes/db.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    // Ensure you have a 'created_at' column, or change it to the date column you use
    $query = mysqli_query($conn, "SELECT * FROM donations WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        die("Receipt not found.");
    }
} else {
    die("Invalid Request.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_<?php echo $data['ref_no']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #525659; padding: 30px 0; }
        .receipt-body {
            max-width: 800px;
            background: white;
            margin: auto;
            padding: 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
            border-top: 10px solid #27ae60;
        }
        .header-logo { font-size: 2rem; font-weight: 800; color: #27ae60; letter-spacing: -1px; }
        .receipt-title { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 30px; }
        
        .stamp {
            position: absolute;
            right: 50px;
            bottom: 150px;
            width: 150px;
            height: 150px;
            border: 4px double #27ae60;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #27ae60;
            font-weight: bold;
            opacity: 0.4;
            transform: rotate(-15deg);
            text-transform: uppercase;
            line-height: 1;
        }

        .details-table th { color: #7f8c8d; font-size: 0.8rem; text-transform: uppercase; border: none; }
        .details-table td { font-size: 1.1rem; font-weight: 600; border: none; padding-top: 0; }

        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .receipt-body { box-shadow: none; border: 1px solid #eee; margin-top: 0; }
        }
    </style>
</head>
<body>

<div class="container text-center no-print mb-4">
    <button onclick="window.print()" class="btn btn-success btn-lg px-5 shadow">
        <i class="fas fa-print me-2"></i> Print Receipt
    </button>
    <a href="index.php" class="btn btn-light btn-lg border px-5 ms-2">Back to Home</a>
</div>

<div class="receipt-body">
    <div class="stamp">
        <div>HELPHAND<br>OFFICIAL<br>VERIFIED</div>
    </div>

    <div class="row receipt-title align-items-center">
        <div class="col-sm-6">
            <div class="header-logo"><i class="fas fa-hand-holding-heart"></i> HELPHAND</div>
            <p class="text-muted small">Empowering Vulnerable Communities</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <h2 class="fw-bold m-0">RECEIPT</h2>
            <p class="text-success fw-bold">Ref: #<?php echo $data['ref_no']; ?></p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-6">
            <p class="text-muted small mb-1">ISSUED TO:</p>
            <h5 class="fw-bold"><?php echo strtoupper($data['donor_name']); ?></h5>
            <p class="text-muted">Donor / Contributor</p>
        </div>
        <div class="col-6 text-end">
            <p class="text-muted small mb-1">DATE ISSUED:</p>
            <h5 class="fw-bold"><?php echo date('d M, Y'); ?></h5>
        </div>
    </div>

    <table class="table details-table mb-5">
        <tr>
            <th>Project Supported</th>
            <th class="text-end">Contribution Amount</th>
        </tr>
        <tr>
            <td><?php echo $data['project_name']; ?></td>
            <td class="text-end text-success fs-3">KES <?php echo number_format($data['amount'], 2); ?></td>
        </tr>
    </table>

    <div class="bg-light p-4 rounded-3 mb-5">
        <div class="row">
            <div class="col-6">
                <p class="small text-muted mb-0">Payment Method</p>
                <p class="fw-bold mb-0"><?php echo $data['payment_method']; ?></p>
            </div>
            <div class="col-6 text-end">
                <p class="small text-muted mb-0">Transaction Status</p>
                <p class="text-success fw-bold mb-0">COMPLETED</p>
            </div>
        </div>
    </div>

    <div class="row mt-5 pt-4">
        <div class="col-7">
            <p class="small text-muted">
                <strong>Notes:</strong><br>
                This is a computer-generated receipt and is valid for tax purposes. 
                Thank you for your generous heart in supporting our mission.
            </p>
        </div>
        <div class="col-5 text-end">
            <div class="mt-4" style="border-top: 2px solid #2c3e50; display: inline-block; width: 100%;">
                <p class="fw-bold mb-0">Director of Operations</p>
                <p class="small text-muted">HelpHand NGO Global</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted border-top pt-3 small">www.helphand.org | info@helphand.org</p>
    </div>
</div>

</body>
</html>