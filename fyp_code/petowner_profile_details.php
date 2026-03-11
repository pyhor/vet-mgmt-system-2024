<?php
// Start by enabling error reporting (optional, for debugging)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
$query = "SELECT petownerid, firstname, lastname, fullname, username, gender, address, phonenum, email, ethnicity,  role 
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
        $fullname = $row['fullname']; // Add fullname
        $username = $row['username'];
      
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
    exit;
}
?>

<div class="container">
    <div class="profile-section">
        <h1 style="text-align: center;">Pet Owner Profile</h1>
        <table style="margin: 0 auto; border-collapse: collapse; width: 50%;">
            <tr>
                <th style="text-align: left;">Pet Owner ID:</th>
                <td><?php echo htmlspecialchars($petownerid); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Full Name:</th>
                <td><?php echo htmlspecialchars($fullname); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">First Name:</th>
                <td><?php echo htmlspecialchars($firstname); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Last Name:</th>
                <td><?php echo htmlspecialchars($lastname); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Username:</th>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            
            <tr>
                <th style="text-align: left;">Gender:</th>
                <td><?php echo htmlspecialchars($gender); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Address:</th>
                <td><?php echo nl2br(htmlspecialchars($address)); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Phone Number:</th>
                <td><?php echo htmlspecialchars($phonenum); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Email:</th>
                <td><?php echo htmlspecialchars($email); ?></td>
            </tr>
            <tr>
                <th style="text-align: left;">Ethnicity:</th>
                <td><?php echo htmlspecialchars($ethnicity); ?></td>
            </tr>
           
            <tr>
                <th style="text-align: left;">Role:</th>
                <td><?php echo htmlspecialchars($role); ?></td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <a href="edit_petowner_profile.php?petownerid=<?php echo $petownerid; ?>" class="button">Edit Profile</a>
        </div>
    </div>
</div>
