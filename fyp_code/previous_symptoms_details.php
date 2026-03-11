<?php
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}

$petOwnerUsername = $_SESSION['petownerusername'];
$petownerQuery = "SELECT petownerid FROM petowner WHERE username = ?";
$petownerid = null;
$hasAppointment = false; // Initialize hasAppointment variable

// Fetch pet owner ID
if ($stmt = mysqli_prepare($connect, $petownerQuery)) {
    mysqli_stmt_bind_param($stmt, 's', $petOwnerUsername);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $petownerid);
        mysqli_stmt_fetch($stmt);
    } else {
        echo "<script>alert('Error executing query to fetch Pet ID.');</script>";
    }
    mysqli_stmt_close($stmt);
}

if ($petownerid) {
    // Check if the user already has an appointment
    $appointmentQuery = "SELECT COUNT(*) as count FROM appointment WHERE petownerid = ? AND appstatus = 'booked'";
    if ($stmt = mysqli_prepare($connect, $appointmentQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $petownerid);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $count);
            mysqli_stmt_fetch($stmt);
            $hasAppointment = $count > 0;
        }
        mysqli_stmt_close($stmt);
    }

    // Fetch symptoms 
    $symQuery = "SELECT symptomid, symptomname, symptomstatus, pettype, vetid, date, time 
                 FROM symptoms 
                 WHERE petownerid = ? 
                 ORDER BY symptomid DESC";
    $symResults = [];
    if ($stmt = mysqli_prepare($connect, $symQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $petownerid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $symResults[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "<script>alert('Unable to fetch Pet Owner ID. Please log in again.');</script>";
    exit();
}

date_default_timezone_set('Asia/Kuala_Lumpur');
?>

<div class="container">
    <h1>🐾 <u>Previous Symptoms Details</u> 🐾</h1>
    
    <span class="info">Pet Owner Username: <?= htmlspecialchars($petOwnerUsername) ?></span><br><br>
    <span class="info">Current day and time: <?= date('Y-m-d H:i') ?></span> <br><br>

    <?php if (!empty($symResults)): ?>
        <div class="symptoms-grid">
            <?php foreach ($symResults as $sym): ?>
                <div class="symptoms-card">
                    <div class="symptoms-card-content">
                        <span class="ID" style="font-size: 10px; margin-bottom: 0px;">Symptom ID: <?= htmlspecialchars($sym['symptomid']) ?></span>
                        <br><strong>Symptom Name:</strong><span><?= htmlspecialchars($sym['symptomname']) ?></span>
            
                        <strong>Symptom Status:</strong> <span style="text-decoration:underline"><?= htmlspecialchars(strtoupper($sym['symptomstatus'])) ?></span>
                        <strong>Pet Type:</strong><span><?= htmlspecialchars($sym['pettype']) ?></span>
                        <strong>Date:</strong><span><?= htmlspecialchars($sym['date']) ?></span>
                        <strong>Time:</strong><span><?= htmlspecialchars($sym['time']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-records" style="color: #5b6e54;">No symptom records found.</div>
    <?php endif; ?>

    <a href="symptoms.php" class="btn btn-create" onclick="showLoadingOverlay()">Record New Symptoms</a> 

   
</div>

<style>
    .card::before {
        content: '💊';
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        opacity: 0.3;
    }
</style>

