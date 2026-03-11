<?php
// Include database connection and start session
include 'connect_to_db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}

// Get appointment ID from request
$appId = isset($_GET['appid']) ? $_GET['appid'] : null;

// Check if appointment ID is provided
if (!$appId) {
    echo "<script>alert('Missing appointment ID. Please try again.'); window.history.back();</script>";
    exit();
}

// Fetch schedule ID linked to the appointment
$scheduleId = null;
$query = "SELECT a.appid, s.appid AS schedule_appid 
          FROM appointment a 
          JOIN schedule s ON a.vetid = s.vetid AND a.date = DATE(s.datetime) AND a.time = TIME(s.datetime)
          WHERE a.appid = ?";
if ($stmt = mysqli_prepare($connect, $query)) {
    mysqli_stmt_bind_param($stmt, 's', $appId);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $scheduleId = $row['schedule_appid'];
    }
    mysqli_stmt_close($stmt);
}

// Check if a schedule was found
if (!$scheduleId) {
    echo "<script>alert('Failed to find the associated schedule. Please try again.'); window.history.back();</script>";
    exit();
}

// Delete the appointment from the appointment table
$deleteQuery = "DELETE FROM appointment WHERE appid = ?";
if ($stmt = mysqli_prepare($connect, $deleteQuery)) {
    mysqli_stmt_bind_param($stmt, 's', $appId);
    if (mysqli_stmt_execute($stmt)) {
        // Update the schedule to set scheduledstatus back to 'Available'
        $updateScheduleQuery = "UPDATE schedule SET scheduledstatus = 'Available' WHERE appid = ?";
        if ($stmtUpdate = mysqli_prepare($connect, $updateScheduleQuery)) {
            mysqli_stmt_bind_param($stmtUpdate, 'i', $scheduleId);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
        }
        echo "<script>alert('Appointment successfully changed. Please choose another available appointment.'); window.location.href = 'appointment.php';</script>";
    } else {
        echo "<script>alert('Failed to remove appointment. Please try again.'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Failed to prepare database query.'); window.history.back();</script>";
}
?>
