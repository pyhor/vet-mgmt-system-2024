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
    <p style="margin-top: 20px; font-size: 18px;">Logging in...</p>
</div>

<?php 
session_start();

if(empty($_POST['petowner_login'])){ ?>
<!-- User Input Form Section -->
<div class="login-container">
    <div class="title">
<h1 style="text-align: center;">VCMS Pet Owner Login Form</h1>
<p class="required">*Required Field</p>
</div>


<form id="loginForm" action="petowner_login.php" method="POST" onsubmit="showLoadingOverlay()">

    <label>*Username:</label>
    <input type="text" name="petowner_username" placeholder="Username" required>
   
    <label>*Password:</label>
<div style="position: relative;">

    <input type="password" name="password" placeholder="Password" id="petownerPassword" required>
    <span id="togglePassword">
    &#128053;
    </span>
</div>


    <input type="submit" name="petowner_login" value="LOGIN"><br>
</form>


<div class="link">
<p class="password">&#128021;<a href="petownerForgotPassword.php">Forgot Password?</a></p>
<p class="register">&#128008;New to VCMS? <a href="petowner_registration.php">Sign up here.</a></p>

</div>
</div>

<p class="footer">© 2024 Veterinary Clinic Management System. All rights reserved.</p>

<?php } else {

// Data Processing Section
$petowner_username = $_POST['petowner_username'];
$petowner_password = $_POST['password'];

// Connect to DBMS
include 'connect_to_db.php';

// IMPORTANT: Use Prepared Statements to prevent SQL Injection
$query = "SELECT petownerid, username FROM petowner WHERE username = ? AND password = ?";

// Prepare the statement
$stmt = mysqli_prepare($connect, $query);

if ($stmt) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ss", $petowner_username, $petowner_password);
    
    // Execute the statement
    mysqli_stmt_execute($stmt);
    
    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_array($result);
        $petownerid = $data['petownerid'];
        $petownerusername = $data['username'];

        $timeout_duration = 1800;
        setcookie('petownerusername', $petownerusername, time() + $timeout_duration);

        $_SESSION['petownerusername'] = $petownerusername;
        $_SESSION["petownerid"] = $petownerid;

        echo "<script>
          alert('Congratulations, " . $petownerusername . " has successfully logged in.');
            window.location.href = 'petowner_homepage.php';
        </script>";
    } else {
        echo "<script>
            alert('Sorry! Login failed. Please Try Again. \\nPlease check your password or username.');
            window.location.href = 'petowner_login.php';
        </script>";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "<script>
        alert('Database error occurred.');
        window.location.href = 'petowner_login.php';
    </script>";
}

mysqli_close($connect);
}
?>

<script>
const passwordField = document.getElementById('petownerPassword');
const togglePassword = document.getElementById('togglePassword');

togglePassword.addEventListener('click', function () {
    // Toggle the type attribute
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    // Toggle the icon (optional)
    this.textContent = type === 'password' ? '\u{1F435}' : '\u{1F648}';
});
</script>
