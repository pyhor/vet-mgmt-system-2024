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
// Start by enabling error reporting (optional, for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'connect_to_db.php';

// Function to generate next symptomID
function generateSymptomID($connect) {
    $query = "SELECT symptomid FROM symptoms ORDER BY symptomid DESC LIMIT 1";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['symptomid'];
        $numericPart = intval($lastID);
        $numericPart++;
        return $numericPart;
    }
    return 1; // First ID if no records exist
}

// Check if the user is logged in by verifying the session
session_start();
if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); // Redirect if not logged in
    exit();
}

$petownerusername = $_SESSION['petownerusername'];

// Get petowner details from database
$petowner_query = "SELECT petownerid FROM petowner WHERE username = ?";
if ($petowner_stmt = mysqli_prepare($connect, $petowner_query)) {
    mysqli_stmt_bind_param($petowner_stmt, 's', $petownerusername);
    mysqli_stmt_execute($petowner_stmt);
    $petowner_result = mysqli_stmt_get_result($petowner_stmt);

    if ($petowner_result && mysqli_num_rows($petowner_result) > 0) {
        $petowner_data = mysqli_fetch_assoc($petowner_result);
        $petownerid = $petowner_data['petownerid'];
    } else {
        echo "<script>alert('Petowner not found in the database.');</script>";
        exit();
    }
    mysqli_stmt_close($petowner_stmt);
}

// Generate symptom ID
$symptomid = generateSymptomID($connect);

// Check if the form has been submitted
if (isset($_POST['symptoms'])) {
    // Check if user has a booked appointment
    $booked_app_query = "SELECT appid FROM appointment WHERE petownerid = ? AND appstatus = 'Booked'";
    $has_booked_appointment = false;
    
    if ($booked_stmt = mysqli_prepare($connect, $booked_app_query)) {
        mysqli_stmt_bind_param($booked_stmt, 'i', $petownerid);
        mysqli_stmt_execute($booked_stmt);
        mysqli_stmt_store_result($booked_stmt);

        if (mysqli_stmt_num_rows($booked_stmt) > 0) {
            $has_booked_appointment = true;
        }
        mysqli_stmt_close($booked_stmt);
    }

    // Validate that all required fields are filled
    if (!empty($_POST['symptomname']) && !empty($_POST['symptomstatus']) && !empty($_POST['pettype'])) {
        // Retrieve form data
        $symptomnameArray = $_POST['symptomname'];
        $symptomname = implode(', ', $symptomnameArray);
        $symptomstatus = $_POST['symptomstatus'];
        $pettype = $_POST['pettype'];
        $vetid = NULL; // Set to NULL as per database schema

        // Insert symptom data into the database
        $query = "INSERT INTO symptoms (symptomid, symptomname, symptomstatus, petownerid, pettype, vetid) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($connect, $query)) {
            mysqli_stmt_bind_param($stmt, 'isssss', $symptomid, $symptomname, $symptomstatus, $petownerid, $pettype, $vetid);

            if (mysqli_stmt_execute($stmt)) {
                // If user has a booked appointment, redirect back to the previous page
                if ($has_booked_appointment) {
                    echo "<script>
                        alert('Symptom [Symptom ID: $symptomid] recorded successfully. You already have a booked appointment.');
                        window.location.href='previous_symptoms.php';
                    </script>";
                } else {
                    // If no booked appointment, proceed to select department
                    echo "<script>alert('Symptom [Symptom ID: $symptomid] recorded successfully. Please book an appointment.');window.location.href='selectDepartment.php';</script>";
                }
            } else {
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>




<div class="container">
<div class="form-container">
        <h1 style="text-align: center;">Symptom Registration Form</h1>
        <p style="color: #c30010; text-align: center; font-size: 12px;"><i>*Required Field</i></p>
        <form action="symptoms.php" method="POST" id="symptomRegistrationForm" onsubmit="showLoadingOverlay()">
            
        
        <label>*Symptom Name:</label>
    
            <div class="select-container">
    <div class="select-grid">
        <div class="select-checkbox">
            <input type="checkbox" id="diarrhea" name="symptomname[]" value="Diarrhea" data-group="symptoms">
            <label for="diarrhea">Diarrhea</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="weight-loss" name="symptomname[]" value="Weight Loss" data-group="symptoms">
            <label for="weight-loss">Weight Loss</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="shortness-breath" name="symptomname[]" value="Shortness of Breath" data-group="symptoms">
            <label for="shortness-breath">Shortness of Breath</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="coughing" name="symptomname[]" value="Coughing" data-group="symptoms">
            <label for="coughing">Coughing</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="anorexia" name="symptomname[]" value="Anorexia" data-group="symptoms">
            <label for="anorexia">Anorexia</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="ear-infections" name="symptomname[]" value="Ear Infections" 

data-group="symptoms">
            <label for="ear-infections">Ear Infections</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="sneezing" name="symptomname[]" value="Sneezing" 


data-group="symptoms">
            <label for="sneezing">Sneezing</label>
        </div>
        <div class="select-checkbox">
            <input type="checkbox" id="itchiness" name="symptomname[]" value="Itchiness" 


data-group="symptoms">
            <label for="itchiness">Itchiness</label>
        </div>
        <div class="symptom-checkbox">
            <input type="checkbox" id="lethargy" name="symptomname[]" value="Lethargy" 


data-group="symptoms">
            <label for="lethargy">Lethargy</label>
        </div>
    </div>
</div>


<br>

            <label>*Symptom Status:</label>
        
            <input type="radio" name="symptomstatus" value="Critical" required>
            <span>Critical</span><br><br>

            <input type="radio" name="symptomstatus" value="Mild" required>
            <span>Mild</span><br><br>


            <label>*Pet Type:</label>

            <input type="radio" name="pettype" value="Dog" required>
            <span>Dog</span><br><br>

            <input type="radio" name="pettype" value="Cat" required>
            <span>Cat</span><br><br>



            <button type="submit" class="submit-button" name="symptoms" >Register Symptom</button>
        </form>
    </div>
</div>