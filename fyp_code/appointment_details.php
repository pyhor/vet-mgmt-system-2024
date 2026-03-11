<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
">
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>

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
    // First, check for the latest booked appointment
    $bookedAppQuery = "SELECT appid, symptom, appstatus, vetid, petname, pettype, date, time,department,relatedtreatment,relatedpetcare,relatedsymptoms,appname
    FROM appointment
    WHERE petownerid = ? AND appstatus = 'Booked'
    ORDER BY appid DESC
    LIMIT 1"; 

    // Group appointments by date with most recent first
    $appQuery = "SELECT appid, symptom, appstatus, vetid, petname, pettype, date, time,department,relatedtreatment,relatedpetcare,relatedsymptoms,appname
    FROM appointment_temp
    WHERE petownerid = ?
    ORDER BY appid DESC
    LIMIT 1"; 

    $appResults = [];
    
    // First, try to get the booked appointment
    if ($stmt = mysqli_prepare($connect, $bookedAppQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $petownerid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $date = $row['date'];
                $appResults[$date][] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }

    // If no booked appointment, then get the temp appointment
    if (empty($appResults)) {
        if ($stmt = mysqli_prepare($connect, $appQuery)) {
            mysqli_stmt_bind_param($stmt, 'i', $petownerid);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = $row['date'];
                    $appResults[$date][] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
} else {
    echo "<script>alert('Unable to fetch Pet Owner ID. Please log in again.');</script>";
    exit();
}


date_default_timezone_set('Asia/Kuala_Lumpur');
?>




<style>
    .card::before {
        content: '📅';
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        opacity: 0.3;
    }
</style>




<div class="container">
    <h1>🐶 <u>Pet's Appointment</u> 🐶</h1>
    <h2>--- The sequence of the appointment record is arranged in ascending order by date and time (24-hour time). ---</h2>
    <span class="info">Pet Owner Username: <?= htmlspecialchars($petOwnerUsername) ?></span><br><br>
    <span class="info">Current day and time: <?= date('Y-m-d, g:i A') ?></span><br><br>

    <div class="timeline">
        <?php if (!empty($appResults)): ?>
            <?php foreach ($appResults as $date => $appointments): ?>
                <div class="timeline-date-group">
                    <div class="timeline-date-header">
                        <span><?= htmlspecialchars(date('F j, Y', strtotime($date))) ?></span>
                    </div>
                    <div class="grid">
                        <?php foreach ($appointments as $app): ?>
                            <div class="card">
                                <div class="card-header">
                                    <span class="ID" style=" font-size: 13px; margin-bottom: 0px;">Appointment ID: <?= htmlspecialchars($app['appid']) ?>
                                </span>
                                   
                                    <span class="med-time" style=" font-size: 13px; margin-bottom: 0px;">
                                    Time: <?= date('g:i A', strtotime($app['time'])) ?>
                                    </span>
                                </div>
                                <div class="card-content">

                                <strong>Vet ID:</strong> <u><span><?= htmlspecialchars($app['vetid']) ?></u></span>
  <strong>Symptoms:</strong> <u><span><?= htmlspecialchars($app['symptom']) ?></u></span>
                                    <strong>Pet Name:</strong> <span><?= htmlspecialchars(strtoupper($app['petname'])) ?></span>
                                    <strong>Pet Type:</strong> <span><?= htmlspecialchars($app['pettype']) ?></span>
                                    <strong>Status:</strong> 
                                    <span class="description"><?= htmlspecialchars($app['appstatus']) ?></span>
                                    <strong>Department:</strong> <span><?= htmlspecialchars(strtoupper($app['department'])) ?></span>
                                    <strong>Appoitnment Name:</strong> <span><?= htmlspecialchars(strtoupper($app['appname'])) ?></span>
                                    <strong>Related Symptoms:</strong> <span><?= htmlspecialchars(strtoupper($app['relatedsymptoms'])) ?></span>
                                    <strong>Related Treatment:</strong> <span><?= htmlspecialchars($app['relatedtreatment']) ?></span>
                                    <strong>Related Pet Care:</strong> <span><?= htmlspecialchars($app['relatedpetcare']) ?></span>
                                </div>

                                <?php
                                // Query to check if the user already has a "Booked" appointment
                                $hasBookedAppointment = false;
                                $checkBookedQuery = "SELECT appid FROM appointment WHERE petownerid = ? AND appstatus = 'Booked' LIMIT 1";

                                if ($stmt = mysqli_prepare($connect, $checkBookedQuery)) {
                                    mysqli_stmt_bind_param($stmt, 'i', $petownerid);
                                    mysqli_stmt_execute($stmt);
                                    mysqli_stmt_store_result($stmt);

                                    if (mysqli_stmt_num_rows($stmt) > 0) {
                                        $hasBookedAppointment = true;
                                    }

                                    mysqli_stmt_close($stmt);
                                }
                                ?>



                                <div class="card-actions">
                                    <?php if (!$hasBookedAppointment): ?>
                                        <a href="selectDepartment.php"
                                            class="btn change_app" onclick="if (!confirm('Do you really want to change the appointment?')) return false; showLoadingOverlay();">Change Appointment</a>

                                        <?php
                                        // Query to find the corresponding schedule ID
                                        $scheduleQuery = "SELECT appid FROM schedule 
                                                          WHERE vetid = ? AND datetime = CONCAT(?, ' ', ?) 
                                                          LIMIT 1";
                                        
                                        if ($stmt = mysqli_prepare($connect, $scheduleQuery)) {
                                            $combinedDateTime = $app['date'] . ' ' . $app['time'];
                                            mysqli_stmt_bind_param($stmt, 'iss', $app['vetid'], $app['date'], $app['time']);
                                            mysqli_stmt_execute($stmt);
                                            $result = mysqli_stmt_get_result($stmt);
                                            $scheduleRow = mysqli_fetch_assoc($result);
                                            mysqli_stmt_close($stmt);

                                            if ($scheduleRow) {
                                                $scheduleId = $scheduleRow['appid'];
                                        ?>
                                                <a href="confirm_app.php?schedule=<?= htmlspecialchars($scheduleId) ?>&petname=<?= urlencode($app['petname']) ?>&pettype=<?= urlencode($app['pettype']) ?>" 
                                                    class="btn change_app" onclick="if (!confirm('Do you really want to confirm the appointment?')) return false; showLoadingOverlay();"
                                                    style=" background-color: #5b6e54;
                                            color: #ffffffcf;">Confirm Appointment</a>
                                        <?php
                                            } else {
                                                echo "<div class='btn change_app' style='color:red;'>No available schedule found</div>";
                                            }
                                        }
                                        ?>
                                    <?php else: ?>
                                        <div class="btn change_app" style="color: red;">Appointment already booked</div>
                                    <?php endif; ?>
                                </div>



                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-records" style="color: #5b6e54;">No appointment records found.</div>
        <?php endif; ?>
    </div>

</div>
