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
session_start();
include 'connect_to_db.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['vet_email'];

    // Check if the email exists in the database
    $query = "SELECT vetid FROM vet WHERE email = ?";
    $stmt = mysqli_prepare($connect, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);
            $vetid = $data['vetid'];

            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Save token in the database
            $tokenQuery = "UPDATE vet SET reset_token = ? WHERE vetid = ?";
            $tokenStmt = mysqli_prepare($connect, $tokenQuery);

            if ($tokenStmt) {
                mysqli_stmt_bind_param($tokenStmt, "si", $token, $vetid);
                mysqli_stmt_execute($tokenStmt);

                // Send the reset password link via email
                $resetLink = "http://localhost/Vet_System_Fyp/fyp_code/vetResetPassword.php?token=" . $token;

                // Email content
                $subject = "Password Reset Request";
                $message = "Hi,\n\nClick the link below to reset your password:\n$resetLink\n\nThank you..";
                $headers = "From: pyho9457@gmail.com";

                if (mail($email, $subject, $message, $headers)) {
                    echo "<script>alert('Password reset link has been sent to your email.'); window.location.href = 'vet_login.php';</script>";
                } else {
                    echo "<script>alert('Error sending email. Please try again later.');</script>";
                }
            }
        } else {
            echo "<script>alert('Email not found. Please check and try again.'); window.location.href = 'vetforgotPassword.php';</script>";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Vet Forgot Password | VCMS</title>
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

        .forgot-password-container {
            background-color: #C29A71;
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cute-animal::before {
            content: '🐾';
            font-size: 50px;
        }

        h1 {
            color: #ffffff;
            margin-top: 60px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            color: #ffffff;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #896541;
            border-radius: 8px;
            box-sizing: border-box;
            background-color: #ffffff;
        }

        button {
            background-color: #896541;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6d5133;
        }
    </style>
</head>
<body>
<div class="forgot-password-container">
<div class="cute-animal"></div>
    <h1>Forgot Password</h1>
    <form action="vetforgotPassword.php" method="POST" onsubmit="showLoadingOverlay()">
        <label for="vet_email">Enter your registered email:</label>
        <input type="email" name="vet_email" id="vet_email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    </div>
</body>
</html>
