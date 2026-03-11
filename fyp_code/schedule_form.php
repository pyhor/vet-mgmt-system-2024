<script src="showloading.js"></script>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center; flex-direction: column;">
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>

<?php
include 'connect_to_db.php';
session_start();

// Function to generate next AppID
function generateAppID($connect) {
    $query = "SELECT appid FROM schedule ORDER BY appid DESC LIMIT 1";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['appid'];
        $numericPart = intval(substr($lastID, 1));
        $numericPart++;
        return '5' . str_pad($numericPart, 4, '0', STR_PAD_LEFT);
    }
    return '50001'; // First ID if no records exist
}

if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); // Redirect if not logged in
    exit();
}

$vetusername = $_SESSION['vetusername'];

// Get vet details from database
$vet_query = "SELECT vetid, fullname FROM vet WHERE username = ?";
if ($vet_stmt = mysqli_prepare($connect, $vet_query)) {
    mysqli_stmt_bind_param($vet_stmt, 's', $vetusername);
    mysqli_stmt_execute($vet_stmt);
    $vet_result = mysqli_stmt_get_result($vet_stmt);

    if ($vet_result && mysqli_num_rows($vet_result) > 0) {
        $vet_data = mysqli_fetch_assoc($vet_result);
        $vetid = $vet_data['vetid']; // Correctly assign vetid
    } else {
        echo "<script>alert('Vet not found in the database.');</script>";
        exit();
    }
    mysqli_stmt_close($vet_stmt);
}

// Handle form submission
if (isset($_POST['schedule'])) {
    if (!empty($_POST['scheduledstatus']) && !empty($_POST['datetime']) && !empty($_POST['appname'])) {
        $datetime = $_POST['datetime'];
        $appname = $_POST['appname'];
        $scheduledstatus = $_POST['scheduledstatus'];

        // Retrieve checkbox values
        $department = !empty($_POST['department']) ? implode(', ', $_POST['department']) : '';
        $relatedsymptoms = !empty($_POST['relatedsymptoms']) ? implode(', ', $_POST['relatedsymptoms']) : '';
        $relatedtreatment = !empty($_POST['relatedtreatment']) ? implode(', ', $_POST['relatedtreatment']) : '';
        $relatedpetcare = !empty($_POST['relatedpetcare']) ? implode(', ', $_POST['relatedpetcare']) : '';

        // Check for duplicate datetime for the same vet
        $check_query = "SELECT * FROM schedule WHERE datetime = ? AND vetid = ?";
        if ($check_stmt = mysqli_prepare($connect, $check_query)) {
            mysqli_stmt_bind_param($check_stmt, 'ss', $datetime, $vetid);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if ($check_result && mysqli_num_rows($check_result) > 0) {
                echo "<script>alert('The selected date and time are already scheduled for you. Please choose a different time.');
                window.history.back();</script>";
                mysqli_stmt_close($check_stmt);
                exit();
            }
            mysqli_stmt_close($check_stmt);
        }

        // Generate AppID and Insert into Database
        $appid = generateAppID($connect);
        $query = "INSERT INTO schedule (appid, appname, vetid, department, scheduledstatus, datetime, relatedsymptoms, relatedtreatment, relatedpetcare)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($connect, $query)) {
            mysqli_stmt_bind_param($stmt, 'issssssss', $appid, $appname, $vetid, $department, $scheduledstatus, $datetime, $relatedsymptoms, $relatedtreatment, $relatedpetcare);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Schedule [Schedule ID: $appid] recorded successfully!');
                      window.location.href='previous_schedule.php';</script>";
            } else {
                echo "<script>alert('Registration failed.');</script>";
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
        <h1 style="text-align: center;">Manage Appointment Schedule</h1>
        <p style="color: #c30010; text-align: left; font-size: 12px;"><i>*Required Field</i></p><br>

        <!-- Display Vet Information -->
        <div class="vet-info" style="margin-bottom: 20px; padding: 10px; background: #f5f5f5;">
            <p style="color: #5b6e54; text-align: left; font-size: 15px; font-family: Itim, cursive;"><strong>Vet ID:</strong> <?php echo htmlspecialchars($vet_data['vetid']); ?></p>
            <p style="color: #5b6e54; text-align: left; font-size: 15px; font-family: Itim, cursive;"><strong>Vet Name:</strong> <?php echo htmlspecialchars($vet_data['fullname']); ?></p>
        </div>

        <form action="schedule.php" method="POST" onsubmit="showLoadingOverlay()">
            <label>*Date and Time:</label>
            <input type="datetime-local" id="appointmentDateTime" name="datetime" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
            <p id="error-message" style="color: red; display: none; font-size: 13px;">Please select a valid date and time (Mon-Fri: 9 AM - 9 PM, Sat-Sun: 9 AM - 6 PM).</p>
            <script>
                document.getElementById('appointmentDateTime').addEventListener('input', function () {
                    const input = this.value;
                    const date = new Date(input);
                    if (isNaN(date.getTime())) return;

                    const day = date.getDay(); 
                    const hours = date.getHours();
                    const minutes = date.getMinutes();

                    let isValid = false;
                    if (day >= 1 && day <= 5 && (hours >= 9 && hours < 21 || (hours === 21 && minutes === 0))) {
                        isValid = true;
                    } else if ((day === 0 || day === 6) && (hours >= 9 && hours < 18 || (hours === 18 && minutes === 0))) {
                        isValid = true;
                    }

                    document.getElementById('error-message').style.display = isValid ? 'none' : 'block';
                    if (!isValid) this.value = '';
                });
            </script>
            <br><br>

            <!-- Scheduled Status -->
            <label>Scheduled Status:</label>
            <input type="radio" value="Available" checked disabled>
            <span>Available</span>
            <input type="hidden" name="scheduledstatus" value="Available"><br><br>

            <label>*Appointment Name:</label>
            <input type="text" name="appname" required><br><br>

            <!-- Departments -->
            <label>*Departments:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="radio" name="department[]" value="General Health and Medicine" data-group="symptoms">
                    <label for="General Health and Medicine">General Health and Medicine</label>
                </div>
                <div class="select-checkbox">
                    <input type="radio" name="department[]" value="Dermatology and Skin Care" data-group="symptoms">
                    <label for="Dermatology and Skin Care">Dermatology and Skin Care</label>
                </div>
            </div>
            <p id="department-error" style="color: red; display: none; font-size: 13px;">Please select at least one department.</p>
            <br><br>

            <!-- Related Symptoms -->
            <label>*Related Symptoms:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Lethargy" data-group="symptoms">
                    <label for="Lethargy">Lethargy</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Vomiting/Diarrhea" data-group="symptoms">
                    <label for="Vomiting/Diarrhea">Vomiting/Diarrhea</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedsymptoms[]" value="Coughing/Sneezing" data-group="symptoms">
                    <label for="Coughing/Sneezing">Coughing/Sneezing</label>
                </div>
            </div>
            <p id="symptoms-error" style="color: red; display: none; font-size: 13px;">Please select at least one symptom.</p>
            <br><br>

            <!-- Related Treatment -->
            <label>*Related Treatment:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" id="oxygen-therapy" name="relatedtreatment[]" value="Oxygen Therapy" data-group="treatment">
                    <label for="Oxygen Therapy">Oxygen Therapy</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" id="fluid-therapy" name="relatedtreatment[]" value="Fluid Therapy" data-group="treatment">
                    <label for="Fluid Therapy">Fluid Therapy</label>
                </div>
            </div>
            <p id="treatment-error" style="color: red; display: none; font-size: 13px;">Please select at least one treatment.</p>
            <br><br>

            <!-- Related Pet Care -->
            <label>*Related Pet Care:</label>
            <div class="select-container">
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedpetcare[]" value="Routine Checkups" data-group="care">
                    <label for="Routine Checkups">Routine Checkups</label>
                </div>
                <div class="select-checkbox">
                    <input type="checkbox" name="relatedpetcare[]" value="Regular Grooming" data-group="care">
                    <label for="Regular Grooming">Regular Grooming</label>
                </div>
            </div>
            <p id="care-error" style="color: red; display: none; font-size: 13px;">Please select at least one pet care option.</p>
            <br><br>

            <button type="submit" class="submit-button" name="schedule" id="submitButton" onclick="return validateCheckboxes()">Submit Schedule</button>
        </form>
    </div>
</div>

<script>
    function validateCheckboxes() {
        const departmentCheckboxes = document.querySelectorAll('input[name="department[]"]');
        const symptomsCheckboxes = document.querySelectorAll('input[name="relatedsymptoms[]"]');
        const treatmentCheckboxes = document.querySelectorAll('input[name="relatedtreatment[]"]');
        const careCheckboxes = document.querySelectorAll('input[name="relatedpetcare[]"]');

        let isDepartmentChecked = false;
        let isSymptomsChecked = false;
        let isTreatmentChecked = false;
        let isCareChecked = false;

        departmentCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isDepartmentChecked = true;
            }
        });

        symptomsCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isSymptomsChecked = true;
            }
        });

        treatmentCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isTreatmentChecked = true;
            }
        });

        careCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isCareChecked = true;
            }
        });

        let isValid = true;

        // Show or hide department error message
        if (!isDepartmentChecked) {
            document.getElementById('department-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('department-error').style.display = 'none';
        }

        // Show or hide symptoms error message
        if (!isSymptomsChecked) {
            document.getElementById('symptoms-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('symptoms-error').style.display = 'none';
        }

        // Show or hide treatment error message
        if (!isTreatmentChecked) {
            document.getElementById('treatment-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('treatment-error').style.display = 'none';
        }

        // Show or hide pet care error message
        if (!isCareChecked) {
            document.getElementById('care-error').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('care-error').style.display = 'none';
        }

        return isValid; // If everything is checked, allow form submission
    }
</script>
