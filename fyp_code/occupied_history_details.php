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
    flex-direction: column;">
    
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>

<?php
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); 
    exit();
}

$vetUsername = $_SESSION['vetusername'];
$vetQuery = "SELECT vetid FROM vet WHERE username = ?";
$vetid = null;

// Fetch veterinarian ID
if ($stmt = mysqli_prepare($connect, $vetQuery)) {
    mysqli_stmt_bind_param($stmt, 's', $vetUsername);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $vetid);
        if (!mysqli_stmt_fetch($stmt)) {
            echo "<script>alert('Error fetching Vet ID.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error executing query to fetch Vet ID.');</script>";
        exit();
    }
    mysqli_stmt_close($stmt);
}

if ($vetid) {
    // Group appointments by date with most recent first
    $scheduleQuery = "SELECT appid, appname, vetid, department, scheduledstatus, datetime, relatedtreatment, relatedpetcare, relatedsymptoms
                      FROM schedule 
                      WHERE vetid = ? AND scheduledstatus = 'occupied' 
                      ORDER BY datetime";
    $scheduleResults = [];
    if ($stmt = mysqli_prepare($connect, $scheduleQuery)) { 
        mysqli_stmt_bind_param($stmt, 'i', $vetid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);  
            while ($row = mysqli_fetch_assoc($result)) {  
                $date = date('Y-m-d', strtotime($row['datetime']));
                $scheduleResults[$date][] = $row;  
            }
        } else {
            echo "<script>alert('Error executing query to fetch schedule.');</script>";
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

date_default_timezone_set('Asia/Kuala_Lumpur');
?>

<style>
    .card::before {
        content: '🐿️';
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 30px;
        opacity: 0.3;
    }
</style>

<div class="container">
    <h1>🐾 <u>Occupied History</u> 🐾</h1>
    <h2>--- The sequence of the schedule is arranged in ascending order by date and time (12-hour time). ---</h2>
    <span class="info">Veterinarian Username: <?= htmlspecialchars($vetUsername) ?></span><br><br>
    <span class="info">Current day and time: <?= date('Y-m-d H:i') ?></span> <br><br>

    <div class="timeline">
        <?php if (!empty($scheduleResults)): ?>
            <?php foreach ($scheduleResults as $date => $scheduleList): ?>
                <div class="timeline-date-group">
                    <div class="timeline-date-header">
                        <span><?= htmlspecialchars(date('F j, Y', strtotime($date))) ?></span>
                    </div>
                    <div class="grid">
                        <?php foreach ($scheduleList as $schedule): ?>
                            <div class="card">
                                <div class="card-header">
                                    <span class="ID">Schedule ID: <?= htmlspecialchars($schedule['appid']) ?></span>
                                    <h2><?= htmlspecialchars($schedule['appname']) ?></h2>
                                    <span class="med-time">
                                        Time: <u><?= date('H:i', strtotime($schedule['datetime'])) ?></u>
                                    </span>
                                </div>
                                <div class="card-content">
                                  
                                <strong>Appointment Name:</strong> <span><?= htmlspecialchars($schedule['appname']) ?></span>
                                    <strong>Department:</strong> <span><?= htmlspecialchars($schedule['department']) ?></span>
                                    <strong>Scheduled Status:</strong> <span><?= htmlspecialchars($schedule['scheduledstatus']) ?></span>
                                    <strong>Related Treatment:</strong> <span><?= htmlspecialchars($schedule['relatedtreatment']) ?></span>
                                    <strong>Related Pet Care:</strong> <span><?= htmlspecialchars($schedule['relatedpetcare']) ?></span>
                                    <strong>Related Symptoms:</strong> <span><?= htmlspecialchars($schedule['relatedsymptoms']) ?></span>
                               
                               
                                </div>

                              
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-records" style="color: #5b6e54;">No schedule records found.</div>
        <?php endif; ?>
    </div>
    <a href="javascript:void(0);" class="btn btn-create" onclick="showLoadingOverlay(); history.back();">Go Back</a>

</div>
