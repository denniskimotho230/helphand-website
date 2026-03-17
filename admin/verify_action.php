<?php
include('../includes/db.php');
include('../includes/auth.php'); // Ensure only logged-in admins can do this

if (isset($_GET['id'])) {
    // 1. Get the ID safely
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 2. Update the status to Verified
    $update_query = "UPDATE donations SET status = 'Verified' WHERE id = '$id'";
    
    if (mysqli_query($conn, $update_query)) {
        // 3. Success! Redirect back to the dashboard with a message
        header("Location: dashboard.php?msg=success");
        exit();
    } else {
        // If database fails
        die("Error updating record: " . mysqli_error($conn));
    }
} else {
    // If no ID was sent
    header("Location: dashboard.php");
    exit();
}
?>