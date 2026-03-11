
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profileTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Vet Profile | VCMS </title>
</head>
<body>
    <nav>
        <?php include'vet_nav.php';?>
    </nav>

    <article>


    <?php
// Start the session
session_start();

// Include database connection
include 'connect_to_db.php';

// Check if the session variable is set for the vet
if (!isset($_SESSION['vetusername'])) {
    header('Location: vet_login.php'); 
    exit();
}

// Retrieve the username from the session
$vetUsername = $_SESSION['vetusername'];

// Query to fetch the logged-in vet's record
$query = "SELECT vetid, firstname, lastname, username, gender, address, phonenum, email, ethnicity, role 
          FROM vet WHERE username = ?";

if ($stmt = mysqli_prepare($connect, $query)) {
    // Bind the username parameter to the query
    mysqli_stmt_bind_param($stmt, 's', $vetUsername);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Fetch the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if any record exists
    if ($row = mysqli_fetch_assoc($result)) {
        // Store data in variables for display
        $vetid = $row['vetid'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $username = $row['username'];
        $gender = $row['gender'];
        $address = $row['address'];
        $phonenum = $row['phonenum'];
        $email = $row['email'];
        $ethnicity = $row['ethnicity'];
        $role = $row['role'];
    } else {
        echo "<script>alert('No vet records found for the logged-in user.');</script>";
        exit;
    }

    // Free the result set and close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Failed to fetch vet profile.');</script>";
    exit;
}
?>

<div class="container">
    <div class="profile-section">
        <h1 style="text-align: center;">Vet Profile</h1>
        <table style="margin: 0 auto; border-collapse: collapse; width: 50%;">
            <tr>
                <th style="text-align: left;">Vet ID:</th>
                <td><?php echo htmlspecialchars($vetid); ?></td>
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
            <a href="vet_edit_profile.php?vetid=<?php echo $vetid; ?>" class="button">Edit Profile</a>
        </div>
    </div>
</div>




</article>




<footer class="footer">
<?php include 'footer.php'; ?>
    </footer>

</body>
</html>
