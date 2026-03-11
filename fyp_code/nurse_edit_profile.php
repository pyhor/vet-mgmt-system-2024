<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/editProfileTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Edit Nurse Profile | VCMS </title>
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
        <?php include 'nurse_nav.php'; ?>
    </nav>

    <article>

    <?php
    // Start the session
    session_start();

    // Include database connection
    include 'connect_to_db.php';

    // Check if the session variable is set for the nurse
    if (!isset($_SESSION['nurseusername'])) {
        header('Location: nurse_login.php'); 
        exit();
    }

    // Retrieve the username from the session
    $nurseUsername = $_SESSION['nurseusername'];

    // Query to fetch the logged-in nurse's record
    $query = "SELECT nurseid, firstname, lastname, username, gender, address, phonenum, email, ethnicity, role 
              FROM nurse WHERE username = ?";

    if ($stmt = mysqli_prepare($connect, $query)) {
        // Bind the username parameter to the query
        mysqli_stmt_bind_param($stmt, 's', $nurseUsername);

        // Execute the query
        mysqli_stmt_execute($stmt);

        // Fetch the result
        $result = mysqli_stmt_get_result($stmt);

        // Check if any record exists
        if ($row = mysqli_fetch_assoc($result)) {
            // Store data in variables for display
            $nurseid = $row['nurseid'];
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
            echo "<script>alert('No nurse records found for the logged-in user.');</script>";
            exit;
        }

        // Free the result set
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Failed to fetch nurse profile.');</script>";
        exit;
    }

    // Check if form is submitted and update the profile
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Fetch updated data from form
        $updatedAddress = $_POST['address'];
        $updatedPhoneNum = $_POST['phonenum'];
        $updatedEmail = $_POST['email'];

        // Update query to update nurse profile
        $updateQuery = "UPDATE nurse SET address = ?, phonenum = ?, email = ? WHERE username = ?";

        if ($stmt = mysqli_prepare($connect, $updateQuery)) {
            // Bind updated data to the query
            mysqli_stmt_bind_param($stmt, 'ssss', $updatedAddress, $updatedPhoneNum, $updatedEmail, $nurseUsername);

            // Execute the update query
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'nurse_profile.php';
              </script>";
            } else {
                echo "<script>alert('Failed to update profile.');</script>";
            }

            // Free the result set and close the statement
            mysqli_stmt_close($stmt);
        }
    }
    ?>

    <div class="container">
        <h1 style="text-align: center;">Edit Nurse Profile</h1>
        <form method="POST" action="">
           
                <label>Nurse ID:</label>
                <input type="text" name="nurseid" value="<?php echo htmlspecialchars($nurseid); ?>" readonly>
                <br>

                <label>First Name:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" readonly>
                <br>

                <label>Full Name:</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>" readonly>
                <br>

                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                <br>

                <label>Gender:</label>
                <input type="text" name="gender" value="<?php echo htmlspecialchars($gender); ?>" readonly>
                <br>

                <label>Address:</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <br>

                <label>Phone Number:</label>
                <input type="text" name="phonenum" pattern="^012-\d{7,8}$" 
                title="Phone number must start with 012 and follow the format 012-3456789 or 012-34567899" value="<?php echo htmlspecialchars($phonenum); ?>">
                <br>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <br>

                <label>Ethnicity:</label>
                <input type="text" name="ethnicity" value="<?php echo htmlspecialchars($ethnicity); ?>" readonly>
                <br>

                <label>Role:</label>
                <input type="text" name="role" value="<?php echo htmlspecialchars($role); ?>" readonly>
                <br>

            </table>
            <div style="text-align: center; margin-top: 20px;">
                <button type="submit" class="button">Update Profile</button>
            </div>
        </form>
    </div>

    </article>

    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>

</body>
</html>
