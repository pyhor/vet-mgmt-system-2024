<?php
// Include database connection and start session
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); 
    exit();
}

if (isset($_GET['appid'])) {
    $appid = intval($_GET['appid']); // Sanitize input to prevent SQL injection

    // Prepare SQL to delete the medication record
    $deleteQuery = "DELETE FROM schedule WHERE appid = ?";
    if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $appid);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Medication record [Medication ID: $appid] deleted successfully.'); window.location.href = 'previous_schedule.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to delete schedule record.');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing the delete query.');</script>";
    }
} else {
    echo "<script>alert('Invalid request. Schedule ID is missing.');</script>";
    header('Location: previous_schedule.php'); // Redirect to the list page
    exit();
}
?>
