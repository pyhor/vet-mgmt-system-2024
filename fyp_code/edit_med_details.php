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

// Redirect if user is not logged in
if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php');
    exit();
}

// Validate and fetch medid
if (isset($_GET['medid']) && is_numeric($_GET['medid'])) {
    $medid = intval($_GET['medid']);

    // Fetch medication record
    $medQuery = "SELECT medname, petname, role, datetime, description FROM medication WHERE medid = ?";
    $medRecord = null;

    if ($stmt = mysqli_prepare($connect, $medQuery)) {
        mysqli_stmt_bind_param($stmt, 'i', $medid);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $medRecord = mysqli_fetch_assoc($result);
            $petname = $medRecord['petname']; 
        }
        mysqli_stmt_close($stmt);
    }

    // Check if the record was found
    if (!$medRecord) {
        echo "<script>alert('Medication record not found.');</script>";
        header('Location: previous_medication.php');
        exit();
    }
} else {
    echo "<script>alert('Invalid medication ID.');</script>";
    header('Location: previous_medication.php');
    exit();
}

// Update record if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medname = $_POST['medname'];
    $petname = $_POST['petname'];
    $role = $_POST['role'];
    $datetime = $_POST['datetime'];
    $description = $_POST['description'];

    $updateQuery = "UPDATE medication SET medname = ?, petname = ?, role = ?, datetime = ?, description = ? WHERE medid = ?";
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, 'sssssi', $medname, $petname, $role, $datetime, $description, $medid);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
            alert('Medication record [Medication ID: $medid] updated successfully.');
            window.location.href = 'previous_medication.php';
        </script>";
        
        } else {
            echo "<script>alert('Failed to update medication record.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

date_default_timezone_set('Asia/Kuala_Lumpur');

?>

<div class="container">
<div class="form-container">
    <u><h1 style="text-align: center;">Edit Medication Record</u></h1>
    <p style="color: #c30010; text-align: center; font-size: 12px;"><i>*Required Field</i></p>
    <form action="" method="POST" onsubmit="showLoadingOverlay()">
    
    <br>
    <div class="pet-info" style="margin-bottom: 20px; padding: 10px; background: #f5f5f5;">

        <p style="color: #5b6e54; text-align: left; font-size: 15px;  font-family: Itim, cursive;"><strong>Pet Owner Username:</strong> <?php echo htmlspecialchars($_SESSION['petownerusername']); ?></p>
        <p style="color: #5b6e54; text-align: left; font-size: 15px;  font-family: Itim, cursive;"><span class="medID">Medication ID: <?= htmlspecialchars($medid) ?></p></span>

</div>
            <label for="medname">Medication Name:</label>
            <input type="text" id="medname" name="medname" value="<?= htmlspecialchars($medRecord['medname']) ?>" required>
            <p style="color: #c30010; text-align: left; font-size: 11px;">*Limited to 15 characters only.<br><br>
            <br>

            <label>*Pet Name:</label>
<input type="text" name="petname" placeholder="Enter your pet's name" required><br><br>

<p style="color: #5b6e54; text-align: left; font-size: 15px;  font-family: Itim, cursive;"><span class="prePet">Previous Pet Name: <u><?= htmlspecialchars($petname) ?></u></p></span>
<br><br>

            <label for="role">Role (Select One):</label>
            <div class="role-container">
            <div class="role-option">
            <input type="radio" id="cat" name="role" value="Cat" <?= $medRecord['role'] === 'Cat' ? 'checked' : '' ?> required>
            <label for="cat">Cat</label>
</div>

<div class="role-option">
            <input type="radio" id="dog" name="role" value="Dog" <?= $medRecord['role'] === 'Dog' ? 'checked' : '' ?> required>
            <label for="dog">Dog</label><br><br>

</div>

</div><br>
            <label for="datetime">Date and Time:</label>
            <input type="datetime-local" id="datetime" name="datetime" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($medRecord['datetime']))) ?>" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($medRecord['description']) ?></textarea><br>
            <br><button type="submit" class="submit-button" >Save Changes</button>
            <br><br><a href="previous_medication.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
    </div>