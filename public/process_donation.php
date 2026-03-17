<?php
include('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_name = mysqli_real_escape_string($conn, $_POST['donor_name']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $ref_no = mysqli_real_escape_string($conn, $_POST['ref_no']);
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);

    // Insert into database - Note: Default status in DB should be 'Pending'
    $query = "INSERT INTO donations (donor_name, amount, payment_method, ref_no, project_name, status) 
              VALUES ('$donor_name', '$amount', '$payment_method', '$ref_no', '$project_name', 'Pending')";

    if (mysqli_query($conn, $query)) {
        // We no longer need $donation_id for a direct link here
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thank You | HELPHAND</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
                .success-card { background: white; padding: 50px; border-radius: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center; max-width: 550px; }
                .icon-box { width: 80px; height: 80px; background: #f1c40f; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 40px; }
                .btn-home { background: #2c3e50; color: white; border-radius: 50px; padding: 12px 30px; text-decoration: none; transition: 0.3s; display: inline-block; }
                .btn-home:hover { background: #1a252f; color: white; }
                .status-badge { background: #fff9e6; color: #d4ac0d; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; border: 1px solid #f9e79f; }
            </style>
        </head>
        <body>
            <div class="success-card animate__animated animate__fadeInUp">
                <div class="icon-box"><i class="fas fa-clock"></i></div>
                <h1 class="fw-bold text-dark">Donation Submitted!</h1>
                <p class="text-muted fs-5">Thank you, <strong><?php echo htmlspecialchars($donor_name); ?></strong>.</p>
                
                <div class="alert alert-light border mb-4">
                    <p class="mb-2">Your contribution of <strong>KES <?php echo number_format($amount); ?></strong> is currently:</p>
                    <span class="status-badge">PENDING VERIFICATION</span>
                </div>

                <p class="small text-muted mb-4">
                    Our team is verifying your transaction (Ref: <?php echo htmlspecialchars($ref_no); ?>). 
                    Once verified, you can download your official receipt using the <strong>"Get Receipt"</strong> button on our homepage.
                </p>
                
                <a href="index.php" class="btn btn-home">
                    <i class="fas fa-arrow-left me-2"></i> Return to Home
                </a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>