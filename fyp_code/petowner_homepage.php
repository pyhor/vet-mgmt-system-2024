<?php
session_start();


if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); // Redirect if not logged in
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="stylesheet" href="css/petowner_livechat_layout.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Pet Owner Homepage | VCMS</title>
</head>
<body>
    <nav>
        <?php include 'petowner_nav.php'; ?>
    </nav>  

    <div class="main-content">
        <div class="hero-section">
            <?php
            if (isset($_SESSION['petownerusername'])) {
                echo "<p style='font-family: Itim, cursive; font-size: 20px; color:#5b6e54'>Welcome back, Pet Owner " . htmlspecialchars($_SESSION['petownerusername']) . ".</p><br><br><br>";
            }
            ?>

            <h1 class="hero-title">WELCOME TO VETERINARY CLINIC MANAGEMENT SYSTEM</h1>

            <div class="icon">
                <img src="images/download__4_-modified__1_-removebg-preview.png" alt="VCMS Icon" oncontextmenu="return false;">
            </div>

            <p class="hero-description">
                &#128054 This veterinary clinic management system enables pet owners to manage their pets more easily and conveniently, especially for those with busy daily schedules.
            </p>
        </div>

        <div class="tips-section">
            <h2>&#128570 Tips to use this system</h2>
            <ul>
                <li>&#128054 To begin booking for your pet(s), please use the "Pet Symptoms Tracker" to record your pet’s symptoms first.</li>
                <li>&#128054 After that, you'll be directed to the appointment page, where you can schedule a symptoms-related appointment with a professional vet for your beloved pet(s).</li>
                <li>&#128054 If you have any questions, please don't hesitate to ask using the chat below.</li>
            </ul>
        </div>
        <?php include "petowner_livechat.php"; ?>
    </div>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>

