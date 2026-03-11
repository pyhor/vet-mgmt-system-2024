<?php
include 'connect_to_db.php';
session_start();

if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/formTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Pet's Appointment | Pet Owner | VCMS</title>
    <!-- Load jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <nav>
        <?php include 'petowner_nav.php'; ?>
    </nav>  

    <article>
    <div class="container">
        <div class="form-container">
            <h1>🐾 <u>Make Appointment</u> 🐾</h1><br>
            
            <!-- Departments -->
            <form action="show_appointment.php" method="GET" onsubmit="return validateCheckboxes();">
                <label>*Department Selection:</label>
                <div class="select-container">
                    <div class="select-checkbox">
                        <input type="radio" name="department" value="General Health and Medicine" id="general-health">
                        <label for="general-health">General Health and Medicine</label>
                    </div>
                    <div class="select-checkbox">
                        <input type="radio" name="department" value="Dermatology and Skin Care" id="dermatology">
                        <label for="dermatology">Dermatology and Skin Care</label>
                    </div>
                </div>
                <p id="department-error" style="color: red; display: none; font-size: 13px;">Please select a department.</p>
                <br>

                <button type="submit" class="btn submit-button">Book Appointment</button>
            </form>
        </div>
    </div>
    </article>

    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>

<script>
    function validateCheckboxes() {
        const departmentCheckboxes = document.querySelectorAll('input[name="department"]');
        let isDepartmentChecked = false;

        departmentCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                isDepartmentChecked = true;
            }
        });

        if (!isDepartmentChecked) {
            document.getElementById('department-error').style.display = 'block';
            return false; // Prevent form submission
        } else {
            document.getElementById('department-error').style.display = 'none';
        }

        return true; // Allow form submission
    }
</script>
