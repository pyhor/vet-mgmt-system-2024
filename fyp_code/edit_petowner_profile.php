<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/editProfileTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Edit Petowner Profile | VCMS</title>
    <!-- Load jQuery library first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style=" 
    background-image: linear-gradient(
        rgba(255, 255, 255, 0.64),
        rgba(255, 255, 255, 0.64)
    ), url('images/51a5da917b9258a763d51e3b1dc2c44c.jpg');
    background-size: cover;
    background-position: 0 -5cm;
    background-attachment: fixed;">
   
<nav>
    <?php include 'petowner_nav.php'; ?>
</nav>  

<article>

<?php
// Include database connection
include 'connect_to_db.php';

// Start the session
session_start(); // Ensure the session is started

// Check if the session variable is set
if (!isset($_SESSION['petownerusername'])) {
    header('Location: petowner_login.php'); 
    exit();
}

// Retrieve the username from the session
$petOwnerUsername = $_SESSION['petownerusername'];

// Query to fetch the logged-in pet owner's record
$query = "SELECT petownerid, firstname, lastname, fullname, username, password, gender, address, phonenum, email, ethnicity, role 
          FROM petowner WHERE username = ?";

if ($stmt = mysqli_prepare($connect, $query)) {
    // Bind the username parameter to the query
    mysqli_stmt_bind_param($stmt, 's', $petOwnerUsername);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Fetch the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if any record exists
    if ($row = mysqli_fetch_assoc($result)) {
        // Store data in variables for display
        $petownerid = $row['petownerid'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $fullname = $row['fullname'];
        $username = $row['username'];
        $password = $row['password'];
        $gender = $row['gender'];
        $address = $row['address'];
        $phonenum = $row['phonenum'];
        $email = $row['email'];
        $ethnicity = $row['ethnicity'];
        $role = $row['role'];
    } else {
        echo "<script>alert('No pet owner records found for the logged-in user.');</script>";
        exit;
    }

    // Free the result set and close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Failed to fetch pet owner profile.');</script>";
    exit();
}

// Handle form submission for updating the profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updatedAddress = $_POST['address'];
    $updatedPhoneNum = $_POST['phonenum'];
    $updatedEmail = $_POST['email'];

    $updateQuery = "UPDATE petowner SET address = ?, phonenum = ?, email = ? WHERE username = ?";
    if ($stmt = mysqli_prepare($connect, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, 'ssss', $updatedAddress, $updatedPhoneNum, $updatedEmail, $petOwnerUsername);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
            alert('Profile updated successfully!');
            window.location.href = 'petowner_profile.php';
          </script>";
        } else {
            echo "<script>alert('Failed to update profile.');</script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="container">
    <h1 style="text-align: center;">Edit Pet Owner Profile</h1>
    <form method="POST" action="" >
        <label>Pet Owner ID:</label>
        <input type="text" value="<?php echo htmlspecialchars($petownerid); ?>" readonly><br><br>

        <label>Full Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($fullname); ?>" readonly><br><br>

        <label>First Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($firstname); ?>" readonly><br><br>

        <label>Last Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($lastname); ?>" readonly><br><br>

        <label>Username:</label>
        <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly><br><br>

        <label>Gender:</label>
        <input type="text" value="<?php echo htmlspecialchars($gender); ?>" readonly><br><br>

        <label>Address:</label>
        <textarea name="address"><?php echo htmlspecialchars($address); ?></textarea><br><br>

        <label>Phone Number:</label>
        <input type="text" name="phonenum" pattern="^012-\d{7,8}$" 
        title="Phone number must start with 012 and follow the format 012-3456789 or 012-34567899" value="<?php echo htmlspecialchars($phonenum); ?>"><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>

        <label>Ethnicity:</label>
        <input type="text" value="<?php echo htmlspecialchars($ethnicity); ?>" readonly><br><br>

        <label>Role:</label>
        <input type="text" value="<?php echo htmlspecialchars($role); ?>" readonly><br><br>

        <button type="submit" style="padding: 10px 20px; background-color: #5b6e54; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Save Changes</button>
    </form>
</div>

</article>

<footer class="footer">
    <?php include 'footer.php'; ?>
</footer>

</body>
</html>
