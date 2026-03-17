<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    // If the user tries to access dashboard.php without logging in,
    // redirect them to login.php (which is in the same folder)
    header("Location: login.php"); 
    exit();
}
?>