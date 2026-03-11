<link rel="stylesheet" href="css/registerTemplate.css">
<link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">


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
session_start();

if (isset($_POST['verify_otp'])) {
    $enteredOTP = $_POST['otp'];
    $sessionOTP = $_SESSION['otp'];
    $email = $_SESSION['email'];

    if ($enteredOTP == $sessionOTP) {
        // Add logic to save the pet owner's details into the database
        include 'connect_to_db.php'; // Ensure database connection is included
        $petownerData = $_SESSION['petowner_registration_data'];

        // Ensure email is fetched from the session and used consistently
        $petownerData['email'] = $_SESSION['email'];
    
        $query = "INSERT INTO petowner (petownerid, firstname, lastname, username, password, gender, address, phonenum, email, ethnicity, role) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($connect, $query)) {
            mysqli_stmt_bind_param($stmt, 'sssssssssss', 
                $petownerData['petownerid'], $petownerData['firstname'], $petownerData['lastname'], $petownerData['username'], 
                $petownerData['password'], $petownerData['gender'], $petownerData['address'], $petownerData['phonenum'], 
                $petownerData['email'], $petownerData['ethnicity'], $petownerData['role']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    
        echo "<script>
            alert('OTP Verified! Registration complete. Please use your information to login.');
            window.location.href = 'petowner_login.php';
        </script>";
        
        // Clear OTP and session data
        unset($_SESSION['otp']);
      
        exit; // Ensure no further script execution
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>
  <title>Pet Owner OTP | VCMS</title>

<div class="container">
<div class="register-form">
<form method="POST" action="petownerOtpVerification.php" onsubmit="showLoadingOverlay()">
    <label>Enter the OTP sent to your email:</label><br>
    <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
    <button type="submit" name="verify_otp" class="register-button" >Verify OTP</button>
</div>
</div>
</form>