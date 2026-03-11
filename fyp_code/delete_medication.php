<?php
// Include database connection and start session
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}

if (isset($_GET['medid'])) {
    $medid = intval($_GET['medid']); // Sanitize input to prevent SQL injection

    // Prepare SQL to delete the medication record
    $deleteQuery = "DELETE FROM medication WHERE medid = ?";
    if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $medid);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Medication record [Medication ID: $medid] deleted successfully.'); window.location.href = 'previous_medication.php';</script>";
           
        } else {
            echo "<script>alert('Error: Unable to delete medication record.');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing the delete query.');</script>";
    }
} else {
    echo "<script>alert('Invalid request. Medication ID is missing.');</script>";
    header('Location: previous_medication.php'); // Redirect to the list page
    exit();
}
?>
