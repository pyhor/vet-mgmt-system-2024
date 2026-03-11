<?php
session_start();

if (!isset($_SESSION['nurseusername'])) {
    header('Location: nurse_login.php'); // Redirect if not logged in
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Nurse Homepage | VCMS </title>
</head>
<body>
    <nav>
        <?php include'nurse_nav.php';?>
    </nav>

    <div class="main-content">
        
        <div class="hero-section">
        <?php
if (isset($_SESSION['nurseusername'])) {
    echo "<p style='font-family: Itim, cursive; font-size: 20px;color:#5b6e54'>Welcome back, Nurse " . htmlspecialchars($_SESSION['nurseusername']) . ".</p><br><br><br>";
}
?>
            <h1 class="hero-title">WELCOME TO VETERINARY CLINIC MANAGEMENT SYSTEM</h1>
         
            <div class="icon">
                <img src="images/download__4_-modified__1_-removebg-preview.png" alt="VCMS Icon" oncontextmenu="return false;">
            </div>
</div>
</div>
    <footer class="nurse_footer">
        <?php include 'footer.php'; ?>
    </footer>

</body>
</html>

<style>

.nurse_footer {
    font-family: Itim, cursive;
    /* font-weight: 200; */
    /* background-color: #5b6e54; */
    color: #ffffff;
    text-align: center;
    /* padding: 10px 0; */
    /* width: 100%; */
    /* margin-top: auto; */
    /* position:static; */
}

.nurse_footer p {
    margin: 0;
    font-size: 14px;
}
</style>