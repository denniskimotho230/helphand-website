<?php
include(__DIR__ . '/auth.php');
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/sidebar.php');

$name = $_GET['name'] ?? '';
$res = mysqli_query($conn, "SELECT * FROM projects WHERE project_name='$name'");
$project = mysqli_fetch_assoc($res);

if(isset($_POST['update'])) {
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $target = $_POST['target'];
    mysqli_query($conn, "UPDATE projects SET description='$desc', target_amount='$target' WHERE project_name='$name'");
    header("Location: manage_projects.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Project</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
<h2>Edit Project: <?php echo htmlspecialchars($name); ?></h2>
<form method="POST">
<div class="mb-3">
<label>Description</label>
<textarea name="description" class="form-control" rows="4"><?php echo $project['description']; ?></textarea>
</div>
<div class="mb-3">
<label>Target Amount</label>
<input type="number" name="target" class="form-control" value="<?php echo $project['target_amount']; ?>" required>
</div>
<button type="submit" name="update" class="btn btn-primary">Update</button>
<a href="manage_projects.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>
