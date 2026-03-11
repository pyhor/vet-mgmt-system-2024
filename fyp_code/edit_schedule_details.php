<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Logging in...</p>
</div>

<?php
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); // Redirect if not logged in
    exit();
}

$vetusername = $_SESSION['vetusername'];

// Get vet details
$vet_query = "SELECT vetid, fullname FROM vet WHERE username = ?";
if ($vet_stmt = mysqli_prepare($connect, $vet_query)) {
    mysqli_stmt_bind_param($vet_stmt, 's', $vetusername);
    mysqli_stmt_execute($vet_stmt);
    $vet_result = mysqli_stmt_get_result($vet_stmt);

    if ($vet_result && mysqli_num_rows($vet_result) > 0) {
        $vet_data = mysqli_fetch_assoc($vet_result);
        $vetid = $vet_data['vetid'];
    } else {
        echo "<script>alert('Vet not found in the database.');</script>";
        exit();
    }
    mysqli_stmt_close($vet_stmt);
}

// Validate AppID for edit
if (isset($_GET['appid']) && is_numeric($_GET['appid'])) {
    $appid = intval($_GET['appid']);
} else {
    echo "<script>alert('Invalid AppID.');</script>";
    header('Location: previous_schedule.php');
    exit();
}

// Fetch schedule details for the given AppID
$schedule_query = "SELECT appid, datetime, scheduledstatus, appname, department, relatedtreatment, relatedpetcare, relatedsymptoms FROM schedule WHERE appid = ? AND vetid = ?";
$schedule_data = null;
if ($stmt = mysqli_prepare($connect, $schedule_query)) {
    mysqli_stmt_bind_param($stmt, 'ii', $appid, $vetid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $schedule_data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Schedule not found for this AppID.');</script>";
        header('Location: previous_schedule.php');
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Handle form submission
if (isset($_POST['update_schedule'])) {
    $appname = $_POST['appname']; // Appointment name
    $department = isset($_POST['department']) ? implode(", ", $_POST['department']) : "-"; // Department

    // Handle checkboxes for relatedtreatment, relatedpetcare, and relatedsymptoms
    $relatedtreatment = isset($_POST['relatedtreatment']) ? implode(", ", $_POST['relatedtreatment']) : "";
    $relatedpetcare = isset($_POST['relatedpetcare']) ? implode(", ", $_POST['relatedpetcare']) : "";
    $relatedsymptoms = isset($_POST['relatedsymptoms']) ? implode(", ", $_POST['relatedsymptoms']) : "";

    // Keep date and status unchanged
    $date = $schedule_data['datetime'];
    $scheduledstatus = $schedule_data['scheduledstatus'];

    if (!empty($appname) && (!empty($relatedsymptoms) || !empty($relatedtreatment) || !empty($relatedpetcare))) {
        // Proceed with the update query
        $update_query = "UPDATE schedule 
                         SET appname = ?, department = ?, relatedtreatment = ?, relatedpetcare = ?, relatedsymptoms = ? 
                         WHERE appid = ? AND vetid = ?";

        if ($stmt = mysqli_prepare($connect, $update_query)) {
            mysqli_stmt_bind_param($stmt, 'ssssssi', $appname, $department, $relatedtreatment, $relatedpetcare, $relatedsymptoms, $appid, $vetid);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Schedule updated [Schedule ID: $appid] successfully!'); window.location.href='previous_schedule.php';</script>";
            } else {
                echo "<script>alert('Failed to update schedule.');</script>";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "<script>alert('Please fill all required fields.');</script>";
    }
}

date_default_timezone_set('Asia/Kuala_Lumpur');
?>


<div class="container">
    <div class="form-container">
        <u><h1 style="text-align: center;">Edit Schedule Slot</u></h1>
        <p style="color: #c30010; text-align: left; font-size: 12px;"><i>*Required Field</i></p><br>

        <!-- Display Vet Information -->
        <div class="vet-info" style="margin-bottom: 20px; padding: 10px; background: #f5f5f5;">
            <p style="color: #5b6e54; text-align: left; font-size: 15px; font-family: Itim, cursive;">
                <strong>Schedule ID:</strong> <?php echo htmlspecialchars($schedule_data['appid']); ?>
            </p>
            <p style="color: #5b6e54; text-align: left; font-size: 15px; font-family: Itim, cursive;">
                <strong>Vet ID:</strong> <?php echo htmlspecialchars($vet_data['vetid']); ?>
            </p>
            <p style="color: #5b6e54; text-align: left; font-size: 15px; font-family: Itim, cursive;">
                <strong>Vet Name:</strong> <?php echo htmlspecialchars($vet_data['fullname']); ?>
            </p>
        </div>

        <form action="" method="POST" onsubmit="showLoadingOverlay()">
            <!-- Disabled Date and Time -->
            <label>Date and Time:</label>
            <input type="datetime-local" name="date" value="<?= date('Y-m-d\TH:i', strtotime($schedule_data['datetime'])) ?>" disabled><br><br>

            <!-- Disabled Status -->
            <label>Scheduled Status:</label>
            <input type="radio" name="scheduledstatus" value="Available" <?= ($schedule_data['scheduledstatus'] === 'Available') ? 'checked' : '' ?> disabled>
            <span >Available</span><br><br>

            <label>Appointment Name:</label>
            <input type="text" name="appname" id="appname" value="<?= htmlspecialchars($schedule_data['appname']) ?>" required><br><br>

           <!-- Departments -->
           <label>*Departments:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="radio" name="department[]" value="General Health and Medicine">
                    <label for="General Health and Medicine">General Health and Medicine</label>
                </div>
                <div class="select-checkbox">
                    <input type="radio" name="department[]" value="Dermatology and Skin Care">
                    <label for="Dermatology and Skin Care">Dermatology and Skin Care</label>
                </div>
            </div><br><br>

            <!-- Related Symptoms -->
            <label>*Related Symptoms:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Lethargy">
                    <label for="Lethargy">Lethargy</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Vomiting/Diarrhea">
                    <label for="Vomiting/Diarrhea">Vomiting/Diarrhea</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Coughing/Sneezing">
                    <label for="Coughing/Sneezing">Coughing/Sneezing</label>
                </div>
            </div><br><br>

            <!-- Related Treatment -->
            <label>*Related Treatment:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedtreatment[]" value="Oxygen Therapy">
                    <label for="Oxygen Therapy">Oxygen Therapy</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedtreatment[]" value="Fluid Therapy">
                    <label for="Fluid Therapy">Fluid Therapy</label>
                </div>
            </div><br><br>

            <!-- Related Pet Care -->
            <label>*Related Pet Care:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedpetcare[]" value="Routine Checkups">
                    <label for="Routine Checkups">Routine Checkups</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedpetcare[]" value="Regular Grooming">
                    <label for="Regular Grooming">Regular Grooming</label>
                </div>
            </div>
            <p id="checkbox-error" style="color: red; display: none; font-size: 13px;">Please select at least one symptom.</p>

            <button type="submit" class="submit-button" name="update_schedule" onclick="return validateCheckboxes()">Update Schedule</button>
            <br><br><a href="previous_schedule.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
</div>

<script>
    function validateCheckboxes() {
        const checkboxes = document.querySelectorAll('input[name="relatedsymptoms[]"]:checked, input[name="relatedtreatment[]"]:checked, input[name="relatedpetcare[]"]:checked');
        const errorMessage = document.getElementById('checkbox-error');
        if (checkboxes.length === 0) {
            errorMessage.style.display = 'block';
            return false; // Prevent form submission
        }
        errorMessage.style.display = 'none';
        return true; // Allow form submission
    }
</script>
