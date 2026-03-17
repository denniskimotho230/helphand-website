<?php
// Protect page and connect to database
include('../includes/auth.php');
include('../includes/db.php');

// Check if project ID is provided
if(isset($_GET['id'])){
    $project_id = intval($_GET['id']); // ensure it's an integer

    // Optional: Check if project has donations before deleting
    $check = mysqli_query($conn, "SELECT * FROM donations WHERE project_id = $project_id");
    if(mysqli_num_rows($check) > 0){
        // Redirect back with error message
        header("Location: manage_projects.php?error=has_donations");
        exit();
    }

    // Use prepared statement for deletion
    $stmt = $conn->prepare("DELETE FROM projects WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);

    if($stmt->execute()){
        // Redirect to projects list with success
        header("Location: manage_projects.php?success=deleted");
        exit();
    } else {
        // Redirect with error
        header("Location: manage_projects.php?error=delete_failed");
        exit();
    }
} else {
    // No project ID provided
    header("Location: manage_projects.php?error=no_id");
    exit();
}
