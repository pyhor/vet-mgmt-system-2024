<?php
// Include database connection and start session
include 'connect_to_db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}

// Function to generate next AppID
function generateAppID($connect) {
    $query = "SELECT appid FROM appointment ORDER BY appid DESC LIMIT 1";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['appid'];
        $numericPart = intval(substr($lastID, 1)); // Remove '6' and increment
        $numericPart++;
        return '6' . str_pad($numericPart, 4, '0', STR_PAD_LEFT);
    }
    return '60001'; // First ID if no records exist
}

// Get data from the request
$scheduleId = isset($_GET['schedule']) ? $_GET['schedule'] : null;
$petName = isset($_GET['petname']) ? trim($_GET['petname']) : null;
$petType = isset($_GET['pettype']) ? trim($_GET['pettype']) : null;  // Retrieve pettype from the URL

// Check if required data is provided
if (!$scheduleId || !$petName || !$petType) { // Add a check for petType here
    echo "<script>alert('Missing schedule ID, pet name or pet type. Please try again.'); window.history.back();</script>";
    exit();
}

// Fetch petownerid from session username
$petOwnerUsername = $_SESSION['petownerusername'];
$query = "SELECT petownerid FROM petowner WHERE username = ?";
$petOwnerId = null;

if ($stmt = mysqli_prepare($connect, $query)) {
    mysqli_stmt_bind_param($stmt, 's', $petOwnerUsername);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $petOwnerId);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Check if petownerid was found
if (!$petOwnerId) {
    echo "<script>alert('Failed to identify pet owner. Please log in again.'); window.location.href = 'petowner_login.php';</script>";
    exit();
}

// Fetch symptom, pettype, vet details, and appname based on schedule ID
$scheduleQuery = "SELECT vetid, datetime, department, relatedtreatment, relatedpetcare, relatedsymptoms, appname FROM schedule WHERE appid = ?";
$symptomQuery = "SELECT pettype FROM symptoms WHERE symptomid = (SELECT symptomid FROM schedule WHERE appid = ?)";
$scheduleDetails = null;
$petTypeFromDB = null;

if ($stmt = mysqli_prepare($connect, $scheduleQuery)) {
    mysqli_stmt_bind_param($stmt, 'i', $scheduleId);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $scheduleDetails = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// Fetch pettype from the symptoms table
if ($stmt = mysqli_prepare($connect, $symptomQuery)) {
    mysqli_stmt_bind_param($stmt, 'i', $scheduleId);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $symptomDetails = mysqli_fetch_assoc($result);
        $petTypeFromDB = $symptomDetails['pettype'];
    }
    mysqli_stmt_close($stmt);
}

// Check if schedule details were found
if (!$scheduleDetails) {
    echo "<script>alert('Invalid schedule ID. Please try again.'); window.history.back();</script>";
    exit();
}

// Check if pettype was found
if (!$petTypeFromDB) {
    echo "<script>alert('Pet type not found. Please check the symptoms table.'); window.history.back();</script>";
    exit();
}

// Extract details from schedule
$symptom = $scheduleDetails['department'];
$vetId = $scheduleDetails['vetid'];
$dateTime = $scheduleDetails['datetime'];
$date = date('Y-m-d', strtotime($dateTime));
$time = date('H:i:s', strtotime($dateTime));
$department = $scheduleDetails['department'];
$relatedTreatment = $scheduleDetails['relatedtreatment'];
$relatedPetCare = $scheduleDetails['relatedpetcare'];
$relatedSymptoms = $scheduleDetails['relatedsymptoms'];
$appName = $scheduleDetails['appname'];

// Generate new AppID
$appId = generateAppID($connect);

// Add the appointment data to the database
$insertQuery = "
    INSERT INTO appointment (appid, petownerid, symptom, appstatus, vetid, date, time, petname, pettype, department, relatedtreatment, relatedpetcare, relatedsymptoms, appname)
    VALUES (?, ?, ?, 'Booked', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

if ($stmt = mysqli_prepare($connect, $insertQuery)) {
    mysqli_stmt_bind_param($stmt, 'sisssssssssss', $appId, $petOwnerId, $symptom, $vetId, $date, $time, $petName, $petType, $department, $relatedTreatment, $relatedPetCare, $relatedSymptoms, $appName);  
    if (mysqli_stmt_execute($stmt)) {
        // Update the schedule table to set scheduledstatus to 'occupied'
        $updateScheduleQuery = "UPDATE schedule SET scheduledstatus = 'Occupied' WHERE appid = ?";
        if ($stmtUpdate = mysqli_prepare($connect, $updateScheduleQuery)) {
            mysqli_stmt_bind_param($stmtUpdate, 'i', $scheduleId);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
        }
        echo "<script>alert('Appointment [Appointment ID: $appId] successfully booked! '); window.location.href = 'appointment.php';</script>";
    } else {
        echo "<script>alert('Failed to book appointment. Please try again later.'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Failed to prepare database query.'); window.history.back();</script>";
}
?>
