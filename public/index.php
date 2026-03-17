<?php 
include('../includes/db.php'); 

// Fetch projects with total verified donations
$projects_query = mysqli_query($conn, "
    SELECT p.*, 
    IFNULL(SUM(CASE WHEN d.status = 'Verified' THEN d.amount ELSE 0 END), 0) as total_collected
    FROM projects p 
    LEFT JOIN donations d ON p.project_name = d.project_name 
    GROUP BY p.id 
    ORDER BY p.id DESC
");

/**
 * FETCH FROM C:\xampp\htdocs\ngo_donations\public\image
 */
function getProjectPhoto($projectName) {
    $name = $projectName; // Exact name from Database
    $lowerName = strtolower($projectName);
    
    // Extensions to check
    $exts = ['jpg', 'jpeg', 'png', 'webp'];

    foreach ($exts as $ext) {
        // 1. Try exact match (e.g., "Clean Water.jpg")
        if (file_exists("image/$name.$ext")) return "image/$name.$ext?v=" . time();
        
        // 2. Try lowercase match (e.g., "clean water.jpg")
        if (file_exists("image/$lowerName.$ext")) return "image/$lowerName.$ext?v=" . time();

        // 3. Try underscore match (e.g., "clean_water.jpg")
        $underscoreName = str_replace(' ', '_', $lowerName);
        if (file_exists("image/$underscoreName.$ext")) return "image/$underscoreName.$ext?v=" . time();
    }

    // FALLBACKS (If nothing found in image folder)
    if (preg_match('/water|well|borehole/i', $lowerName)) return "https://images.unsplash.com/photo-1541252260730-0412e8e2108e?w=800";
    if (preg_match('/food|hunger|feed/i', $lowerName)) return "https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800";
    if (preg_match('/school|education/i', $lowerName)) return "https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800";
    
    return "https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=800";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HELPHAND | NGO Empowerment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root { --primary-green: #27ae60; --dark-blue: #2c3e50; }
        body { font-family: 'Segoe UI', sans-serif; scroll-behavior: smooth; overflow-x: hidden; background-color: #fdfdfd; }
        .navbar { background: rgba(255,255,255,0.98); z-index: 1050; border-bottom: 1px solid #eee; }
        .navbar-brand { color: var(--primary-green) !important; font-size: 1.8rem; letter-spacing: 1px; }
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&v=final');
            background-size: cover; background-position: center; height: 90vh;
            display: flex; align-items: center; color: white; margin-top: -76px;
        }
        .project-card { border: none; border-radius: 20px; transition: all 0.4s ease; overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); height: 100%; position: relative; }
        .project-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .project-img { height: 260px; width: 100%; object-fit: cover; background-color: #f0f0f0; }
        .progress { height: 12px; border-radius: 10px; background-color: #e9ecef; margin: 15px 0; }
        .progress-bar { background-color: var(--primary-green); border-radius: 10px; }
        .status-badge { position: absolute; top: 15px; right: 15px; padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 0.8rem; z-index: 10; }
        .btn-main { background: var(--primary-green); color: white; padding: 14px 30px; border-radius: 50px; font-weight: bold; border: none; transition: 0.3s; }
        .btn-main:hover { background: #219150; color: white; transform: scale(1.05); }
        .section-title { font-weight: 800; color: var(--dark-blue); margin-bottom: 10px; }
        .underline { width: 70px; height: 4px; background: var(--primary-green); margin: 0 auto 30px; border-radius: 2px; }
        footer { background: #1a252f; color: #ecf0f1; padding: 60px 0; margin-top: 80px; }
        .hidden-admin { opacity: 0.2; color: #fff; text-decoration: none; font-size: 0.8rem; }
        .hidden-admin:hover { opacity: 1; }
        .modal-content { border-radius: 20px; border: none; }
        .btn-submit { background: var(--primary-green); color: white; border: none; padding: 15px; border-radius: 10px; width: 100%; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-hand-holding-heart me-2"></i>HELPHAND</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3 fw-bold text-dark" href="#about">About Us</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold text-dark" href="#projects">Projects</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold text-success" href="track_donation.php">Get Receipt</a></li>
                <li class="nav-item ms-lg-3"><button class="btn btn-main btn-sm" data-bs-toggle="modal" data-bs-target="#donateModal">Donate Now</button></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container text-center">
        <h1 class="display-3 fw-bold animate__animated animate__fadeInDown">Empowerment & Hope.</h1>
        <p class="lead mb-5 fs-4">Your contribution provides clean water, food, and education to those in need.</p>
        <button class="btn btn-main btn-lg" data-bs-toggle="modal" data-bs-target="#donateModal">Get Started Today</button>
    </div>
</section>

<section id="about" class="py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6"><img src="https://images.unsplash.com/photo-1509099836639-18ba1795216d?w=800&v=final" class="img-fluid rounded-4 shadow-lg" alt="About HelpHand"></div>
            <div class="col-md-6 ps-md-5 mt-4 mt-md-0">
                <h6 class="text-success fw-bold text-uppercase">Who We Are</h6>
                <h2 class="section-title display-5">Compassion in Action</h2>
                <p class="text-muted fs-5">HELPHAND is a community-driven NGO focused on sustainable solutions.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> 100% Transparency</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Real-time Updates</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section id="projects" class="py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title display-5">Our Active Projects</h2>
            <div class="underline"></div>
        </div>
        <div class="row g-4 mt-2">
            <?php if(mysqli_num_rows($projects_query) > 0): while($row = mysqli_fetch_assoc($projects_query)): 
                $target = $row['target_amount'];
                $collected = $row['total_collected'];
                $remaining = $target - $collected;
                $percent = ($target > 0) ? ($collected / $target) * 100 : 0;
                if($percent > 100) $percent = 100;
                $is_completed = ($remaining <= 0 && $target > 0);
            ?>
            <div class="col-md-4">
                <div class="project-card animate__animated animate__fadeInUp">
                    <?php if($is_completed): ?>
                        <span class="status-badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> GOAL REACHED</span>
                    <?php else: ?>
                        <span class="status-badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i> ONGOING</span>
                    <?php endif; ?>
                    
                    <img src="<?php echo getProjectPhoto($row['project_name']); ?>" class="project-img" alt="Project Image">
                    
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center"><?php echo htmlspecialchars($row['project_name']); ?></h4>
                        <div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $percent; ?>%"></div></div>
                        <div class="d-flex justify-content-between small fw-bold mb-3">
                            <span class="text-success">Raised: KES <?php echo number_format($collected); ?></span>
                            <span class="text-muted">Goal: KES <?php echo number_format($target); ?></span>
                        </div>
                        <?php if($is_completed): ?>
                            <button class="btn btn-secondary rounded-pill w-100 fw-bold py-2" disabled>Fully Funded!</button>
                        <?php else: ?>
                            <div class="text-center mb-3"><span class="badge bg-light text-danger p-2 w-100">Remaining: KES <?php echo number_format($remaining); ?></span></div>
                            <button class="btn btn-outline-success rounded-pill w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#donateModal" onclick="document.getElementById('target_project_select').value='<?php echo addslashes($row['project_name']); ?>'">Donate Now</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div class="col-12 text-center"><p>No projects found.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="donateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg p-3">
            <div class="modal-header border-0"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="text-center mb-4"><h3 class="fw-bold">Donation Details</h3></div>
            <form action="process_donation.php" method="POST">
                <div class="mb-3"><label class="small fw-bold">FULL NAME</label><input type="text" name="donor_name" class="form-control" required></div>
                <div class="row">
                    <div class="col-6 mb-3"><label class="small fw-bold">AMOUNT (KES)</label><input type="number" name="amount" class="form-control" required></div>
                    <div class="col-6 mb-3"><label class="small fw-bold">PAYMENT TYPE</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Bank">Bank Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3"><label class="small fw-bold">TRANSACTION REFERENCE</label><input type="text" name="ref_no" class="form-control border-success" required></div>
                <div class="mb-4"><label class="small fw-bold">SELECT PROJECT</label>
                    <select name="project_name" id="target_project_select" class="form-select" required>
                        <?php mysqli_data_seek($projects_query, 0); while($drop = mysqli_fetch_assoc($projects_query)): 
                            if(($drop['target_amount'] - $drop['total_collected']) > 0): ?>
                        <option value="<?php echo htmlspecialchars($drop['project_name']); ?>"><?php echo htmlspecialchars($drop['project_name']); ?></option>
                        <?php endif; endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn-submit">SUBMIT DONATION</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <div class="container text-center">
        <h3 class="fw-bold">HELPHAND NGO</h3>
        <p class="small text-muted mb-4">Empowering communities across the globe.</p>
        <p class="small">&copy; 2026 HELPHAND. All Rights Reserved.</p>
        <div class="mb-3"><a href="../admin/login.php" class="hidden-admin">Admin Dashboard</a></div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>