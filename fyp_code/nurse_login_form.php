<?php if(empty($_POST['nurse_login'])){ ?>
  
<!-- User Input Form Section -->

<div class="login-container">
    <div class="title">
<h1 style="text-align: center;">VCMS Nurse Login Form</h1>
<p class="required">*Required Field</p>
</div>

<form id="loginForm" action="nurse_login.php" method="POST" onsubmit="showLoadingOverlay()">
   
    <label>*Username:</label>
    <input type="text" name="nurse_username" placeholder="Username" required>
  
    <label>*Password:</label>
<div style="position: relative;">

    <input type="password" name="password" placeholder="Password" id="nursePassword" required>
    <span id="togglePassword">
    &#128053;
    </span>
</div>


    <input type="submit" name="nurse_login" value="LOGIN"><br>

</form>

<div class="link">
<p class="password">&#128021;<a href="nurseForgotPassword.php">Forgot Password?</a></p>
<p class="register">&#128008;New to VCMS? <a href="nurse_registration.php">Sign up here.</a></p>

</div>
</div>
<p class="footer">© 2024 Veterinary Clinic Management System. All rights reserved.</p>

<?php } else {

// Data Processing Section
// var_dump($_POST);  // for debugging

$nurse_username = $_POST['nurse_username'];  // Changed variable name
$nurse_password = $_POST['password'];      // Changed variable name

// Connect to DBMS
include 'connect_to_db.php';

// Query - use the new variable names
$query = "SELECT nurseid, username FROM nurse WHERE username = '$nurse_username' AND password = '$nurse_password'";

// Execute Query
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_array($result);
    $nurseid = $data['nurseid'];
    $nurseusername = $data['username'];

    $timeout_duration = 1800;
    setcookie('nurseusername', $nurseusername, $timeout_duration);

    session_start();
    $_SESSION['nurseusername'] = $nurseusername;
    $_SESSION["nurseid"] = $nurseid;

    $nursesOnline = json_decode(file_get_contents("livechat_nursesOnline.json"));
    $nursesOnline[] = array("id" => $nurseid, "numOfChats" => 0);
    file_put_contents("livechat_nursesOnline.json", json_encode($nursesOnline));

    echo "<script>
        alert('Congratulations, " . $nurseusername . " has successfully logged in.');
        window.location.href = 'nurse_homepage.php';
    </script>";
} else {
    echo "<script>
        alert('Sorry! Login failed. Please Try Again. \\nPlease check your password or username.');
        window.history.back();
    </script>";
}

mysqli_close($connect);
}
?>


<script>
const passwordField = document.getElementById('nursePassword');
const togglePassword = document.getElementById('togglePassword');

togglePassword.addEventListener('click', function () {
    // Toggle the type attribute
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    // Toggle the icon (optional)
    this.textContent = type === 'password' ? '\u{1F435}' : '\u{1F648}';
});
</script>
