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
// Include database connection
include 'connect_to_db.php';
session_start();

// Function to generate next AppID
function generateAppID($connect) {
    $query = "SELECT medid FROM medication ORDER BY medid DESC LIMIT 1";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['medid'];
        $numericPart = intval(substr($lastID, 1));
        $numericPart++;
        return '4' . str_pad($numericPart, 4, '0', STR_PAD_LEFT);
    }
    return '40001'; // First ID if no records exist
}

  if (!isset($_SESSION['petownerusername'])) {
            header('Location: petowner_login.php'); // Redirect if not logged in
            exit();
        }
        
        $medid = $_SESSION['petownerusername'];

// Check if the form has been submitted
if (isset($_POST['medication'])) {
    // Generate a new Medication ID
    $medid = generateAppID($connect);

    // Fetch petownerid based on logged-in username
    $petownerUsername = $_SESSION['petownerusername'];
    $petownerQuery = "SELECT petownerid FROM petowner WHERE username = ?";
    $petownerid = null;

    if ($stmt = mysqli_prepare($connect, $petownerQuery)) {
        mysqli_stmt_bind_param($stmt, 's', $petownerUsername);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $petownerid);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Check if we successfully fetched a petownerid
    if ($petownerid) {
        // Validate that all required fields are filled
        if (
            !empty($medid) && !empty($_POST['medname']) &&
            !empty($_POST['petname']) && !empty($_POST['role']) &&
            !empty($_POST['datetime']) && !empty($_POST['description'])
        ) {
            // Retrieve other form data
            $medname = $_POST['medname'];
            $petname = $_POST['petname'];
            $role = $_POST['role'];
            $datetime = $_POST['datetime'];
            $description = $_POST['description'];

            // Prepare and execute the insert query
            $query = "INSERT INTO medication (medid, medname, petownerid, petname, role, datetime, description) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($connect, $query)) {
                mysqli_stmt_bind_param($stmt, 'isissss', $medid, $medname, $petownerid, $petname, $role, $datetime, $description);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script type='text/javascript'>
                        alert('Medication record [Medication ID: $medid] added successfully!');
                        window.location.href = 'previous_medication.php';
                    </script>";
                } else {
                    echo "<script>alert('Registration failed. Please try again.');</script>";
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "<script>alert('Failed to prepare statement.');</script>";
            }
        } else {
            echo "<script>alert('Please fill in all required fields.');</script>";
        }
    } else {
        echo "<script>alert('Unable to fetch Pet Owner ID. Please log in again.');</script>";
    }
}

date_default_timezone_set('Asia/Kuala_Lumpur');

?>

<div class="container">
<div class="form-container">
        <u><h1 style="text-align: center;">Record New Pet Medication</u></h1>
        <p style="color: #c30010; text-align: center; font-size: 12px;"><i>*Required Field</i></p>
        <form action="medication.php" method="POST" id="medicationRegistrationForm" onsubmit="showLoadingOverlay()">

           <br> <div class="pet-info" style="margin-bottom: 20px; padding: 10px; background: #f5f5f5;">

            <p style="color: #5b6e54; text-align: left; font-size: 15px;  font-family: Itim, cursive;"><strong>Pet Owner Username:</strong> <?php echo htmlspecialchars($_SESSION['petownerusername']); ?></p>

            </div>
            <label>*Date Time:</label>
            <input type="datetime-local" name="datetime" max="<?php echo date('Y-m-d\TH:i'); ?>" required><br><br>

            <label>*Pet Name:</label>
<input type="text" name="petname" placeholder="Enter your pet's name" required><br><br>

<label>*Medication Name:</label>
<input type="text" name="medname" placeholder="Medication Name" maxlength="15" required>
<p style="color: #c30010; text-align: left; font-size: 11px;">*Limited to 15 characters only.</p>
<br><br>

<label>*Role (Select One):</label>
<div class="role-container">
    <div class="role-option">
        <input type="radio" id="cat" name="role" value="Cat" required>
        <label for="cat">Cat</label>
    </div>
    <div class="role-option">
        <input type="radio" id="dog" name="role" value="Dog" required>
        <label for="dog">Dog</label>
    </div>
    <div class="role-option">
        <input type="radio" id="fish" name="role" value="Fish" required>
        <label for="fish">Fish</label>
    </div>

    <div class="role-option">
        <input type="radio" id="horse" name="role" value="Horse" required>
        <label for="horse">Horse</label>
    </div>
</div><br>
            <label>*Description:</label>
            <textarea name="description" placeholder="Keep track of your pet's medications! Write down what medicine your pet took, the dose, and the time. This way, you'll have all the info handy for their health and your peace of mind! " required></textarea><br><br>

            <button type="submit" class="submit-button" name="medication" >Record Medication</button>
        </form>
    </div>
</div>
</div>
</div>

