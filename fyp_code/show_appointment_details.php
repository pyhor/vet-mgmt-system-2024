<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
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

// Ensure department is passed and sanitized
if (!isset($_GET['department']) || empty($_GET['department'])) {
    echo "<script>alert('No department selected. Please go back and select a department.'); window.location.href='1.php';</script>";
    exit();
}

$department = mysqli_real_escape_string($connect, $_GET['department']);
$petOwnerUsername = $_SESSION['petownerusername'];
$petOwnerQuery = "SELECT petownerid FROM petowner WHERE username = ?";
$petOwnerId = null;

// Fetch pet owner ID
if ($stmt = mysqli_prepare($connect, $petOwnerQuery)) {
    mysqli_stmt_bind_param($stmt, 's', $petOwnerUsername);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $petOwnerId);
        if (!mysqli_stmt_fetch($stmt)) {
            echo "<script>alert('Error fetching Pet Owner ID.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error executing query to fetch Pet Owner ID.');</script>";
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Fetch schedules and Vet Fullname based on department
$scheduleQuery = "SELECT s.appid, s.appname, s.vetid, s.department, s.scheduledstatus, s.datetime, s.relatedtreatment, s.relatedpetcare, s.relatedsymptoms, v.fullname AS vetfullname
                  FROM schedule s
                  JOIN vet v ON s.vetid = v.vetid
                  WHERE s.department = ? AND s.scheduledstatus = 'available'
                  ORDER BY s.datetime";

$scheduleResults = [];
if ($stmt = mysqli_prepare($connect, $scheduleQuery)) {
    mysqli_stmt_bind_param($stmt, 's', $department);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row['datetime']));
            $scheduleResults[$date][] = $row;
        }
    } else {
        echo "<script>alert('Error executing query to fetch schedule.');</script>";
        exit();
    }
    $stmt->close();
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
    
    <h1>🐾 <u>Available Appointment Slots</u> 🐾</h1>
    <h2>--- The sequence of the appointment is arranged in ascending order by date and time (12-hour time). ---</h2>
   
    <span class="info"><u>The symptoms are matched with all the symptoms you have entered in the pet symptoms tracker.</u></span><br><br><br>
    
    <span class="info">Current day and time: <?= date('Y-m-d, g:i A') ?></span><br><br>
    <span class="info"><strong >Department Selected:</strong> <span><?= htmlspecialchars($department) ?></span><br><br>


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
                                    <span class="ID">Appointment ID: <?= htmlspecialchars($schedule['appid']) ?></span>
                                    <h2><?= htmlspecialchars($schedule['appname']) ?></h2>
                                    <span class="med-time">
                                        Time: <u><?= date('g:i A', strtotime($schedule['datetime'])) ?></u>
                                    </span>
                                </div>
                                <div class="card-content">
                                    <strong>Time: </strong><u><span><?= date('g:i A', strtotime($schedule['datetime'])) ?></span></u>
                                    <strong>Appointment Name:</strong> <u><span><?= htmlspecialchars($schedule['appname']) ?></span></u>
                                    <strong>Department:</strong> <span><?= htmlspecialchars($schedule['department']) ?></span>
                                    <strong>Scheduled Status:</strong> <span><?= htmlspecialchars($schedule['scheduledstatus']) ?></span>
                                    <strong>Related Treatment:</strong> <span><?= htmlspecialchars($schedule['relatedtreatment']) ?></span>
                                    <strong>Related Pet Care:</strong> <span><?= htmlspecialchars($schedule['relatedpetcare']) ?></span>
                                    <strong>Related Symptoms:</strong> <span><?= htmlspecialchars($schedule['relatedsymptoms']) ?></span>
                                </div>
                                <div class="card-actions">
                                    <?php
                                    $checkAppointmentQuery = "SELECT appid FROM appointment WHERE petownerid = ? AND appid = ?";
                                    if ($stmt = $connect->prepare($checkAppointmentQuery)) {
                                        $stmt->bind_param('ii', $petOwnerId, $schedule['appid']);
                                        $stmt->execute();
                                        $stmt->store_result();

                                        if ($stmt->num_rows > 0) {
                                            echo '<a href="change_appointment.php" class="btn appointment">Change Appointment</a>';
                                        } else {
                                            echo '<a href="#" onclick="return bookAppointmentWithPetName(\'' . htmlspecialchars($schedule['appid']) . '\');" class="btn appointment">Book this Appointment</a>';
                                        }

                                        $stmt->close();
                                    }
                                    ?>
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

<script>
function bookAppointmentWithPetName(appId) {
    const confirmBooking = confirm('Are you sure you want to book this appointment?');
    
    if (!confirmBooking) {
        return false;
    }
    
    const petDetails = prompt('Please enter your pet\'s name and type, separated by a comma (e.g., "Buddy, Dog"):' );
    
    if (!petDetails) {
        alert('Booking cancelled. Pet details are required.');
        return false;
    }
    
    const petDetailsArray = petDetails.split(',');
    const petName = petDetailsArray[0]?.trim();
    const petType = petDetailsArray[1]?.trim();

    if (!petName || !petType) {
        alert('Booking cancelled. Both pet name and pet type are required.');
        return false;
    }
    
    const url = `book_appointment.php?schedule=${encodeURIComponent(appId)}&petname=${encodeURIComponent(petName)}&pettype=${encodeURIComponent(petType)}`;
    window.location.href = url;
    
    return false;
}
</script>
