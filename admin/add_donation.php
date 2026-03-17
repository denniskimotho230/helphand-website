<?php
include(__DIR__ . '/auth.php');
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/sidebar.php');

$message = '';
$donors = mysqli_query($conn, "SELECT donor_name FROM donors ORDER BY donor_name ASC");
$projects = mysqli_query($conn, "SELECT project_name FROM projects ORDER BY project_name ASC");

if(isset($_POST['save_donation'])) {
    $donor = mysqli_real_escape_string($conn, $_POST['donor_name']);
    $project = mysqli_real_escape_string($conn, $_POST['project_name']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $ref = uniqid('REF'); // Generate unique reference
    if(mysqli_query($conn, "INSERT INTO donations(donor_name,project_name,amount,ref,status,donation_date) VALUES('$donor','$project','$amount','$ref','Pending',NOW())")) {
        header("Location: manage_donations.php?msg=added");
        exit();
    } else {
        $message = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Donation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
<h2>Add Donation</h2>
<?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
<form method="POST">
<div class="mb-3">
<label>Select Donor</label>
<select name="donor_name" class="form-select" required>
<option value="">-- Select Donor --</option>
<?php while($row = mysqli_fetch_assoc($donors)): ?>
<option value="<?php echo $row['donor_name']; ?>"><?php echo $row['donor_name']; ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="mb-3">
<label>Select Project</label>
<select name="project_name" class="form-select" required>
<option value="">-- Select Project --</option>
<?php while($row = mysqli_fetch_assoc($projects)): ?>
<option value="<?php echo $row['project_name']; ?>"><?php echo $row['project_name']; ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="mb-3">
<label>Amount (KES)</label>
<input type="number" name="amount" class="form-control" required>
</div>

<button type="submit" name="save_donation" class="btn btn-success">Save Donation</button>
<a href="manage_donations.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>
