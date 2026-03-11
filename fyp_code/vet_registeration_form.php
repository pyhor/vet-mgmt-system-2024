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
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'connect_to_db.php';

// Function to generate Vet ID
function generateVetID($connect) {
    $query = "SELECT vetid FROM vet ORDER BY vetid DESC LIMIT 1";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['vetid'];
        $numericPart = intval(substr($lastID, 1)); // Extract numeric part
        $numericPart++;
        return '1' . str_pad($numericPart, 4, '0', STR_PAD_LEFT); // Format new ID
    }
    return '10001'; // First ID if no records exist
}

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999); // Generate 6-digit OTP
}

// Function to send OTP using PHP's mail()
function sendOTP($email, $otp) {
    $subject = "Your OTP for Vet Registration";
    $message = "Dear Vet,\n\nYour OTP for registration is: $otp\n\nPlease enter this code to verify your email and complete the registration.\n\nBest regards,\nVCMS System";
    $headers = "From: pyho9457@gmail.com\r\n"; // Replace with your email

    return mail($email, $subject, $message, $headers);
}

// Check if the form has been submitted
if (isset($_POST['vet_register'])) {
    if (
        !empty($_POST['firstname']) && !empty($_POST['lastname']) &&
        !empty($_POST['username']) && !empty($_POST['password']) &&
        !empty($_POST['gender']) && !empty($_POST['address']) &&
        !empty($_POST['phonenum']) && !empty($_POST['email']) &&
        !empty($_POST['ethnicity']) && !empty($_POST['role'])
    ) {
        $vetid = generateVetID($connect); // Generate new Vet ID
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $phonenum = $_POST['phonenum'];
        $email = $_POST['email'];
        $ethnicity = $_POST['ethnicity'];
        $role = $_POST['role'];

        // Check if email or phone number already exists
      // Check if email or phone number already exists
$check_query = "SELECT vetid, email, phonenum, username FROM vet WHERE email = ? OR phonenum = ? OR username = ?";
if ($check_stmt = mysqli_prepare($connect, $check_query)) {
    mysqli_stmt_bind_param($check_stmt, 'sss', $email, $phonenum, $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt); // Store the result set
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        mysqli_stmt_bind_result($check_stmt, $existingVetID, $existingEmail, $existingPhoneNum, $existingUsername);
        while (mysqli_stmt_fetch($check_stmt)) {
            if ($existingEmail === $email) {
                echo "<script>alert('Email already exists. Please use a different email.');</script>";
            } elseif ($existingPhoneNum === $phonenum) {
                echo "<script>alert('Phone number already exists. Please use a different phone number.');</script>";
            } elseif ($existingUsername === $username) {
                echo "<script>alert('Username already exists. Please use a different username.');</script>";
            } elseif ($existingVetID === $petownerid) {
                echo "<script>alert('Vet ID already exists. Please use a different Vet ID.');</script>";
            }
        }
    } else {
        $otp = generateOTP();

        session_start();
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['vet_registration_data'] = [
            'vetid' => $vetid,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'password' => $password,
            'gender' => $gender,
            'address' => $address,
            'phonenum' => $phonenum,
            'ethnicity' => $ethnicity,
            'role' => $role
        ];

        if (sendOTP($email, $otp)) {
            echo "<script>
                alert('OTP sent to $email. Please verify your email.');
                window.location.href = 'vetOTPVerification.php';
            </script>";
            exit;
        } else {
            echo "<script>alert('Failed to send OTP. Please try again.');</script>";
        }
    }
    mysqli_stmt_close($check_stmt);


        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>


<!-- Vet Registration Form -->
<div class="container">
    <div class="register-form">
        <h1 style="text-align: center;">Vet Registration Form</h1>
        <p style="color: #c30010; text-align: center; font-size: 12px;"><i>*Required Field</i></p>
        <form action="vet_registration.php" method="POST" onsubmit="showLoadingOverlay()">
            
            <label>*First Name:</label><br>
            <input type="text" name="firstname" placeholder="First Name" required><br><br>

            <label>*Last Name:</label><br>
            <input type="text" name="lastname" placeholder="Last Name" required><br><br>

            <label>*Username:</label><br>
            <input type="text" name="username" placeholder="Username" required><br><br>

            <label>*Password:</label><br>
            <input type="password" name="password" placeholder="Password" required>
            <label style="color:red;font-size:12px;margin-top:-5px;">Password must be at least 8 characters long, and include one uppercase letter, one lowercase letter, one number, and one special character.</label><br>

            <br>

            <label>*Gender:</label>
            <label>
                <input type="radio" name="gender" value="Male" required> Male
            </label>
            <label>
                <input type="radio" name="gender" value="Female" required> Female
            </label><br><br>

            <label>*Address:</label><br>
            <input type="text" name="address" placeholder="Address" required><br><br>

            <label>*Phone Number:</label><br>
<input type="text" name="phonenum" placeholder="012-3456789" id="nursePhoneNum" 
       required 
       pattern="^012-\d{7,8}$" 
       title="Phone number must start with 012 and follow the format 012-3456789 or 012-34567899">
<br>
<div id="nursePhoneNumMessage"></div><br>

            <label>*Email:</label><br>
            <input type="email" name="email" placeholder="email@mail.com" required><br><br>

            <label>*Ethnicity:</label>
            <label>
                <input type="radio" name="ethnicity" value="Chinese" required>
                <span>Chinese</span>
            </label>
            <label>
                <input type="radio" name="ethnicity" value="Malay" required>
                <span>Malay</span>
            </label>
            <label>
                <input type="radio" name="ethnicity" value="Indian" required>
                <span>Indian</span>
            </label><br>

            <label for="role">Responsible Symptoms:</label><br>
            <input type="text" name="role" value="Veterinarian" readonly><br><br>

            <button type="submit" class="register-button" name="vet_register">Register</button>

            <p class="login"> &#128008;Already Sign Up? <a href="vet_login.php" onclick="showLoadingOverlay()">Login in here.</a></p>
        </form>
    </div>
</div>
