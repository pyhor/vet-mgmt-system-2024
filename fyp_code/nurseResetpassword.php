<script src="showloading.js"></script>
<link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">

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
    flex-direction: column;">
    
    <img src="images/Animation - 1732178074480.gif" alt="Loading..." style="width: 200px; height: 200px;">
    <p style="margin-top: 20px; font-size: 18px;">Loading...</p>
</div>



<?php
// Set timezone to Malaysia at the top of your script
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
include 'connect_to_db.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password']; // Direct assignment without hashing
    
    // Validate the token in the nurse table
    $query = "SELECT nurseid FROM nurse WHERE reset_token = ?";
    $stmt = mysqli_prepare($connect, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);
            $nurseid = $data['nurseid'];
            
            // Update password without hashing
            $updateQuery = "UPDATE nurse SET password = ?, reset_token = NULL WHERE nurseid = ?";
            $updateStmt = mysqli_prepare($connect, $updateQuery);
            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "si", $new_password, $nurseid);
                mysqli_stmt_execute($updateStmt);
                echo "<script>alert('Password reset successful. Please login.'); window.location.href = 'nurse_login.php';</script>";
            } else {
                echo "<script>alert('Failed to reset password. Please try again later.'); window.location.href = 'nurseForgotPassword.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid token. Please request a new password reset.'); window.location.href = 'nurseForgotPassword.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
} else if (isset($_GET['token'])) {
    $token = $_GET['token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nurse Reset Password | VCMS</title>
    <style>
        body {
            font-family: 'Itim', cursive;
            background-image: 
        linear-gradient(
            rgba(255, 255, 255, 0.9),
            rgba(232, 234, 231, 0.9)
        ),
        url('images/3f9735541996437da58e7a80b3a10d3d.jpg');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            background-color:#FAF3EB:
        }

        .reset-container {
            background-color: #FAF3EB;
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cute-animal {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            background-color: #896541;
            border-radius: 50%;
        }

        .cute-animal::before {
            content: '🐾';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 50px;
        }

        h1 {
            color: #C29A71;
            margin-top: 60px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #896541;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .password-hint {
            color: #ffffff;
            font-size: 12px;
            margin-top: -5px;
            text-align: left;
            width: 100%;
        }

        button {
            background-color: #896541;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6d5133;
        }
    </style>
</head>
<body>
<div class="reset-container">
<div class="cute-animal"></div>
    <h1>Reset Password</h1>
    <form action="nurse_reset_password.php" method="POST" onsubmit="showLoadingOverlay()">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">Enter new password:</label>
        <input type="password" name="new_password" id="new_password"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
                   title="Password must be at least 8 characters long, and include one uppercase letter, one lowercase letter, one number, and one special character." 
                  required><br>
                  <label style="color:red;font-size:12px;margin-top:-3px;">Password must be at least 8 characters long, and include one uppercase letter, one lowercase letter, one number, and one special character.</label><br>
<br><br>
        <button type="submit" class="button">Reset Password</button>
    </form>
    </div>
</body>
</html>
<?php
} else {
    echo "<script>alert('Invalid access.'); window.location.href = 'nurseForgotPassword.php';</script>";
}
?>
