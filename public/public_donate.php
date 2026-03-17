<?php 
include('db.php'); 

// Capture the pre-selected project from the URL
$preselected = isset($_GET['selected_project']) ? $_GET['selected_project'] : '';

// Fetch only existing projects from your database
$project_query = mysqli_query($conn, "SELECT project_name FROM projects ORDER BY project_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate | HELPHAND NGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .donation-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 550px;
            margin: 40px auto;
            overflow: hidden;
        }
        .card-header { background: #1a252f; color: white; padding: 25px; text-align: center; }
        .btn-donate { background: #27ae60; color: white; border: none; padding: 15px; font-weight: bold; border-radius: 10px; width: 100%; }
        .btn-donate:hover { background: #2ecc71; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container">
    <div class="card donation-card">
        <div class="card-header">
            <h3 class="mb-0 fw-bold"><i class="fas fa-heart text-success"></i> HELPHAND NGO</h3>
            <p class="mb-0 small opacity-75">Your support creates lasting impact</p>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="process_donate.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Donor Name</label>
                    <input type="text" name="donor_name" class="form-control" placeholder="Full Name" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Select Project</label>
                        
                        <?php if($preselected): ?>
                            <select class="form-select bg-light" disabled>
                                <option><?php echo htmlspecialchars($preselected); ?></option>
                            </select>
                            <input type="hidden" name="project_name" value="<?php echo htmlspecialchars($preselected); ?>">
                            <small class="text-success" style="font-size: 0.7rem;"><i class="fas fa-lock"></i> Fixed to selected mission</small>
                        
                        <?php else: ?>
                            <select name="project_name" class="form-select" required>
                                <option value="">-- Select Project --</option>
                                <?php 
                                if(mysqli_num_rows($project_query) > 0) {
                                    while($row = mysqli_fetch_assoc($project_query)) {
                                        echo "<option value='".htmlspecialchars($row['project_name'])."'>".htmlspecialchars($row['project_name'])."</option>";
                                    }
                                } else {
                                    echo "<option value='General Donation'>General Donation</option>";
                                }
                                ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Transaction Reference (ID)</label>
                    <input type="text" name="ref" class="form-control" placeholder="Confirmation Code" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Amount (KES)</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">KES</span>
                        <input type="number" name="amount" class="form-control fw-bold" placeholder="1000" required>
                    </div>
                </div>

                <button type="submit" class="btn-donate shadow">
                    <i class="fas fa-shield-alt me-2"></i> Confirm Donation
                </button>
            </form>

            <div class="d-flex justify-content-center gap-3 mt-4 text-muted opacity-50 fs-4">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
                <i class="fas fa-mobile-alt"></i>
            </div>
        </div>
        
        <div class="card-footer bg-light text-center py-3">
            <a href="index.php" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</div>

</body>
</html>