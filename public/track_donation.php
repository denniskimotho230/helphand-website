<?php
include('../includes/db.php');

$receipt_link = "";
$error_message = "";

if (isset($_POST['search'])) {
    $ref_no = mysqli_real_escape_string($conn, $_POST['ref_no']);
    
    // Search for the donation by reference number
    $query = mysqli_query($conn, "SELECT id, status FROM donations WHERE ref_no = '$ref_no'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if ($data['status'] == 'Verified') {
            $receipt_link = "generate_receipt.php?id=" . $data['id'];
        } else {
            $error_message = "Your donation is still <strong>Pending</strong>. Please check back later once an admin verifies the transaction.";
        }
    } else {
        $error_message = "Reference number not found. Please check your transaction details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Donation | HELPHAND NGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .track-card { max-width: 500px; width: 100%; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-search { background: #27ae60; color: white; font-weight: bold; border-radius: 10px; }
    </style>
</head>
<body>

<div class="track-card text-center">
    <h3 class="fw-bold mb-3">Download Receipt</h3>
    <p class="text-muted mb-4">Enter your Reference Number to get your official receipt.</p>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="ref_no" class="form-control form-control-lg text-center" placeholder="e.g. QX789234" required>
        </div>
        <button type="submit" name="search" class="btn btn-search btn-lg w-100">Find My Receipt</button>
    </form>

    <?php if ($receipt_link): ?>
        <div class="mt-4 alert alert-success">
            <i class="fas fa-check-circle me-2"></i> Payment Verified!
            <br><br>
            <a href="<?php echo $receipt_link; ?>" target="_blank" class="btn btn-dark w-100">
                <i class="fas fa-file-download me-2"></i> Open Receipt
            </a>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="mt-4 alert alert-warning">
            <i class="fas fa-info-circle me-2"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="index.php" class="text-decoration-none text-success small fw-bold">← Back to Homepage</a>
    </div>
</div>

</body>
</html>