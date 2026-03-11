<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/previousTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Previous Record Pet Medication | Pet Owner | VCMS</title>
    <!-- Load jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

  <nav>
        <?php include 'petowner_nav.php'; ?>
    </nav> 

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

// Fetch schedules and Vet Fullname
$symptomname = isset($_GET['symptomid']) ? $_GET['symptomid'] : null; // Get symptom from query
$scheduleQuery = "SELECT s.appid, s.appname, s.vetid, s.role, s.scheduledstatus, s.datetime, v.fullname AS vetfullname
                  FROM schedule s
                  JOIN vet v ON s.vetid = v.vetid
                  WHERE ("; 
$conditions = [];
$symptomList = explode(',', $symptomname); // Split symptoms by a delimiter (e.g., comma)
foreach ($symptomList as $symptom) {
    $conditions[] = "s.role LIKE ?";
}
$scheduleQuery .= implode(' OR ', $conditions) . ") AND s.scheduledstatus = 'available' ORDER BY s.datetime";

$scheduleResults = [];
if ($stmt = mysqli_prepare($connect, $scheduleQuery)) {
    $types = str_repeat('s', count($symptomList));
    $params = array_map(function ($symptom) {
        return '%' . trim($symptom) . '%';
    }, $symptomList);
    mysqli_stmt_bind_param($stmt, $types, ...$params);

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
    <h1>🐾 <u>Change Appointment Slots</u> 🐾</h1>
    <h2>--- The sequence of the appointment is arranged in ascending order by date and time (12-hour time). ---</h2>
   
    <span class="info"><u>The symptoms are matched with all the symptoms you have entered in the pet symptoms tracker.</u></span><br><br><br>
    
    <span class="info">Current day and time: <?= date('Y-m-d, g:i A') ?></span><br><br>

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
                                    <strong>Vet ID:</strong> <span><?= htmlspecialchars($schedule['vetid']) ?></span>
                                    <strong>Vet Fullname:</strong> <span><?= htmlspecialchars($schedule['vetfullname']) ?></span>
                                    <strong>Appointment Name:</strong> <u><span><?= htmlspecialchars($schedule['appname']) ?></span></u>
                                    <strong>Responsible Symptoms:</strong> <u><span><?= htmlspecialchars($schedule['role']) ?></span></u>
                                    <strong>Scheduled Status:</strong> <span><?= htmlspecialchars($schedule['scheduledstatus']) ?></span>
                                </div>
                                <div class="card-actions">
                                    <?php
                                    // Check if user has already booked an appointment for this schedule
                                    $checkAppointmentQuery = "SELECT appid FROM appointment WHERE petownerid = ? AND appid = ?";
                                    if ($stmt = mysqli_prepare($connect, $checkAppointmentQuery)) {
                                        mysqli_stmt_bind_param($stmt, 'ii', $petOwnerId, $schedule['appid']);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_store_result($stmt);
                                        
                                        // If there is an existing appointment
                                        if (mysqli_stmt_num_rows($stmt) > 0) {
                                            echo '<a href="change_appointment.php" class="btn appointment">Change Appointment</a>';
                                        } else {
                                            echo '<a href="#" onclick="return bookAppointmentWithPetName(\'' . htmlspecialchars($schedule['appid']) . '\');" class="btn appointment">Change to this Appointment</a>';
                                        }
                                        
                                        mysqli_stmt_close($stmt);
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

    <a href="javascript:void(0);" class="btn btn-create" onclick="showLoadingOverlay(); history.back();">Go Back</a>
</div>

<script>
function bookAppointmentWithPetName(appId) {
    // Confirm the user's intent to book
    const confirmBooking = confirm('Are you sure you want to change to this appointment?');
    
    if (!confirmBooking) {
        // User clicked "Cancel", abort the booking process
        return false;
    }
    
    // Prompt for both pet's name and pet's type in a single input
    const petDetails = prompt('Please enter your pet\'s name and type, separated by a comma (e.g., "Buddy, Dog"):' );
    
    if (!petDetails) {
        // User canceled or left the input blank
        alert('Booking cancelled. Pet details are required.');
        return false;
    }
    
    // Split the input into pet's name and pet's type
    const petDetailsArray = petDetails.split(',');
    const petName = petDetailsArray[0]?.trim();
    const petType = petDetailsArray[1]?.trim();

    if (!petName || !petType) {
        // If either pet's name or type is missing
        alert('Booking cancelled. Both pet name and pet type are required.');
        return false;
    }
    
    // Redirect to the booking page with the appointment ID, pet name, and pet type as URL parameters
    const url = `change_appointment.php?schedule=${encodeURIComponent(appId)}&petname=${encodeURIComponent(petName)}&pettype=${encodeURIComponent(petType)}`;
    window.location.href = url;
    
    return false; // Prevent the default link behavior
}
</script>

<footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>

