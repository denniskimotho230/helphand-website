<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // These names MUST match the 'name' attribute in your HTML form
    $donor_name = mysqli_real_escape_string($conn, $_POST['donor_name']);
    $amount     = mysqli_real_escape_string($conn, $_POST['amount']);
    $project    = mysqli_real_escape_string($conn, $_POST['project_name']);
    $ref        = mysqli_real_escape_string($conn, $_POST['ref']); // Matched to 'ref'
    
    $status = "Pending";
    $date   = date('Y-m-d H:i:s');

    $sql = "INSERT INTO donations (donor_name, amount, project_name, ref, status, donation_date) 
            VALUES ('$donor_name', '$amount', '$project', '$ref', '$status', '$date')";

    if (mysqli_query($conn, $sql)) {
        header("Location: thank_you.php");
        exit();
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}
?>