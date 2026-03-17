<?php
include(__DIR__ . '/auth.php');
include(__DIR__ . '/../includes/db.php');
include(__DIR__ . '/sidebar.php');

$message = '';
if(isset($_POST['save_donor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    if(mysqli_query($conn, "INSERT INTO donors(donor_name,email,phone) VALUES('$name','$email','$phone')")) {
        header("Location: manage_donors.php?msg=added");
        exit();
    } else {
        $message = "Error: ".mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Donor</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
<h2>Add New Donor</h2>
<?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
<form method="POST">
<div class="mb-3">
<label>Full Name</label>
<input type="text" name="name" class="form-control" required>
</div>
<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>
<div class="mb-3">
<label>Phone Number</label>
<input type="text" name="phone" class="form-control" required>
</div>
<button type="submit" name="save_donor" class="btn btn-success">Save Donor</button>
<a href="manage_donors.php" class="btn btn-secondary">Cancel</a>
</form>
</div>
</body>
</html>
