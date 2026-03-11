<?php
session_start();

if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); // Redirect if not logged in
    exit();
}

$medid = $_SESSION['vetusername'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Vet Homepage | VCMS </title>
</head>
<body>
    <nav>
        <?php include'vet_nav.php';?>
    </nav>

    <div class="main-content">
        
        <div class="hero-section">
        <?php
if (isset($_SESSION['vetusername'])) {
    echo "<p style='font-family: Itim, cursive; font-size: 20px; color:#5b6e54'>Welcome back, Veterinarian " . htmlspecialchars($_SESSION['vetusername']) . ".</p><br><br><br>";
}
?>
            <h1 class="hero-title">WELCOME TO VETERINARY CLINIC MANAGEMENT SYSTEM</h1>
         
            <div class="icon">
                <img src="images/download__4_-modified__1_-removebg-preview.png" alt="VCMS Icon" oncontextmenu="return false;">
            </div>

            <p class="hero-description">
                &#128054 This veterinary clinic management system allows veterinarians to easily understand the pet's symptoms and condition, enabling more accurate and effective treatment.
            </p>
        </div>

        <div class="tips-section">
            <h2> &#128570 Tips to use this system</h2>
            <ul>
                <li>&#128054 The "Appointment Management" feature allows veterinarians to easily manage appointments, whether available or occupied.</li>
                <li>&#128054 The "Analytics & Reports" feature provides insights into pet symptoms and appointment details, helping vets track and analyze important data.</li>
            </ul>
        </div>
    </div>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>

</body>
</html>
