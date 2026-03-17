<?php 
include('../includes/db.php'); 
include('../includes/auth.php'); 

// --- DELETE LOGIC ---
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); 
    $delete_query = "DELETE FROM projects WHERE id = $id";
    if(mysqli_query($conn, $delete_query)) {
        header("Location: manage_projects.php?msg=Project deleted successfully");
        exit();
    }
}

// --- UPDATE/EDIT LOGIC (Including Target Amount) ---
if(isset($_POST['update_project'])) {
    $id = intval($_POST['project_id']);
    $new_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $target = mysqli_real_escape_string($conn, $_POST['target_amount']);
    
    $update_query = "UPDATE projects SET project_name = '$new_name', target_amount = '$target' WHERE id = $id";
    if(mysqli_query($conn, $update_query)) {
        header("Location: manage_projects.php?msg=Project updated successfully");
        exit();
    }
}

// Fetch projects with total verified donations
$projects = mysqli_query($conn, "
    SELECT p.*, 
    IFNULL(SUM(CASE WHEN d.status = 'Verified' THEN d.amount ELSE 0 END), 0) as total_collected
    FROM projects p 
    LEFT JOIN donations d ON p.project_name = d.project_name 
    GROUP BY p.id 
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Projects | HELPHAND Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #27ae60; --dark-blue: #2c3e50; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .main-content { margin-left: 260px; padding: 40px; }
        .table-card { border: none; border-radius: 15px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .bg-gradient-dark { background: linear-gradient(45deg, #2c3e50, #4ca1af); color: white; }
        .btn-green { background: var(--primary-green); color: white; border-radius: 8px; border: none; padding: 10px 20px; transition: 0.3s; }
        .remaining-text { font-size: 0.8rem; font-weight: bold; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Project Targets</h2>
        <a href="add_project.php" class="btn btn-green"><i class="fas fa-plus me-2"></i>New Project</a>
    </div>

    <div class="table-card">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-gradient-dark">
                <tr>
                    <th class="ps-4 py-3">Project Name</th>
                    <th class="py-3">Target (KES)</th>
                    <th class="py-3">Collected</th>
                    <th class="py-3">Remaining</th>
                    <th class="text-center py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($projects)): 
                    $remaining = $row['target_amount'] - $row['total_collected'];
                    $percent = ($row['target_amount'] > 0) ? ($row['total_collected'] / $row['target_amount']) * 100 : 0;
                ?>
                <tr>
                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['project_name']); ?></td>
                    <td>KES <?php echo number_format($row['target_amount'], 2); ?></td>
                    <td class="text-success fw-bold">KES <?php echo number_format($row['total_collected'], 2); ?></td>
                    <td>
                        <span class="remaining-text <?php echo ($remaining <= 0) ? 'text-primary' : 'text-danger'; ?>">
                            KES <?php echo number_format(max(0, $remaining), 2); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary edit-btn" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($row['project_name']); ?>"
                                data-target="<?php echo $row['target_amount']; ?>"
                                data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="manage_projects.php" method="POST" class="modal-content border-0 shadow rounded-4">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-4">Update Project Goal</h5>
                <input type="hidden" name="project_id" id="modal_id">
                <div class="mb-3">
                    <label class="small fw-bold">PROJECT NAME</label>
                    <input type="text" name="project_name" id="modal_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-danger">TARGET AMOUNT (KES)</label>
                    <input type="number" step="0.01" name="target_amount" id="modal_target" class="form-control" required>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_project" class="btn btn-green">Update Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_id').value = this.dataset.id;
            document.getElementById('modal_name').value = this.dataset.name;
            document.getElementById('modal_target').value = this.dataset.target;
        });
    });
</script>
</body>
</html>