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
    // Group medications by date with most recent first
    $medQuery = "SELECT medid, medname, petname, role, datetime, description 
             FROM medication 
             WHERE petownerid = ? 
             ORDER BY datetime"; 
    $medResults = [];
    if ($stmt = mysqli_prepare($connect, $medQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $petownerid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $date = date('Y-m-d', strtotime($row['datetime']));
                $medResults[$date][] = $row;
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

<div class="container">
    <h1>🐾 <u>Previous Medication Details</u> 🐾</h1>
    <h2>--- The sequence of the medication record is arranged in ascending order by date and time (24-hour time). ---</h2>
    <span class="info">Pet Owner Username: <?= htmlspecialchars($petOwnerUsername) ?></span><br><br>
    <span class="info">Current day and time: <?= date('Y-m-d, g:i A') ?></span> <br><br>

    <div class="timeline">
        <?php if (!empty($medResults)): ?>
            <?php foreach ($medResults as $date => $medications): ?>
                <div class="timeline-date-group">
                    <div class="timeline-date-header">
                        <span><?= htmlspecialchars(date('F j, Y', strtotime($date))) ?></span>
                    </div>
                    <div class="grid">
                        <?php foreach ($medications as $med): ?>
                            <div class="card">
                                <div class="card-header">
                                    <span class="ID" style=" font-size: 10px; margin-bottom: 30px;">Medication ID: <?= htmlspecialchars($med['medid']) ?></span>
                                    <h2><?= htmlspecialchars($med['medname']) ?></h2>
                                    <span class="med-time">
                                    Time: <?= date('g:i A', strtotime($med['datetime'])) ?>
                                    </span>
                                </div>
                                <div class="card-content">
                                    <strong>Pet Name:</strong> <u><span><?= htmlspecialchars(strtoupper($med['petname'])) ?></u></span>
                                    <strong>Role:</strong> <span><?= htmlspecialchars($med['role']) ?></span>
                                    <strong>Description:</strong> 
                                    <span class="description"><?= htmlspecialchars($med['description']) ?></span>
                                </div>
                                <div class="card-actions">
                                    <a href="edit_medication.php?medid=<?= htmlspecialchars($med['medid']) ?>" class="btn btn-edit" onclick="showLoadingOverlay()">Edit</a>
                                   
                                   
                                    <a href="delete_medication.php?medid=<?= htmlspecialchars($med['medid']) ?>" 
                                       onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-delete" onclick="showLoadingOverlay()">Delete</a>
                               
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-records" style="color: #5b6e54;">No medication records found.</div>
        <?php endif; ?>
    </div>

    <a href="medication.php" class="btn btn-create" onclick="showLoadingOverlay()">Record New Pet Medication</a>
</div>

