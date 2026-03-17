<?php 
include('../includes/db.php'); 
include('../includes/auth.php'); 

$msg = "";

if(isset($_POST['add_project'])) {
    // 1. Get the name and target amount, clean for security
    $name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $target = mysqli_real_escape_string($conn, $_POST['target_amount']);
    
    // 2. Insert into the table (Including the new target_amount column)
    $sql = "INSERT INTO projects (project_name, target_amount) VALUES ('$name', '$target')";
    
    if(mysqli_query($conn, $sql)) {
        // Success: Redirect to the management page to see the new project
        header("Location: manage_projects.php?msg=Project Added Successfully");
        exit();
    } else {
        // Failure: Show the exact error to find out why it won't save
        $msg = "Database Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project | HELPHAND Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #27ae60; --dark-blue: #2c3e50; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .form-card { 
            max-width: 500px; 
            margin: 60px auto; 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            background: white;
        }
        .header-section {
            background: linear-gradient(45deg, #2c3e50, #4ca1af);
            color: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        .btn-success { 
            background: var(--primary-green); 
            border: none; 
            padding: 14px; 
            font-weight: bold; 
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-success:hover {
            background: #219150;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card form-card">
        <div class="header-section">
            <h3 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i>New Cause</h3>
            <p class="small opacity-75 mb-0 mt-2">Set goals for your fundraising projects</p>
        </div>
        
        <div class="p-4">
            <?php if($msg != ""): ?>
                <div class="alert alert-danger border-0 shadow-sm"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">PROJECT NAME</label>
                    <input type="text" name="project_name" class="form-control form-control-lg bg-light border-0" placeholder="e.g. Children Home Support" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">TARGET GOAL (KES)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 fw-bold text-muted">KES</span>
                        <input type="number" step="0.01" name="target_amount" class="form-control form-control-lg bg-light border-0" placeholder="0.00" required>
                    </div>
                    <div class="form-text mt-2 small">This goal will be used to show a progress bar to donors.</div>
                </div>

                <button type="submit" name="add_project" class="btn btn-success w-100 shadow-sm mb-3">
                    PUBLISH PROJECT
                </button>
                
                <div class="text-center">
                    <a href="manage_projects.php" class="text-decoration-none text-muted small hover-link">
                        <i class="fas fa-arrow-left me-1"></i> Cancel & Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>