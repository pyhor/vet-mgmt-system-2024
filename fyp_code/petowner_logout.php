<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/logoutTemplate.css">
  <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
  <title>Pet Owner Logout | VCMS</title>
  
</head>
<body>
  <article>
    <?php
      session_start();

      
      
      // Unset specific session variables
      unset($_SESSION["petownerid"]);
      unset($_SESSION["petownerusername"]);
     
      


    ?>
    
    <div class="logout-container">
        <div class="logout-icon">&#10003;</div>
        <h2 class="logout-message">Logout Successful</h2>
        <h3>Please visit us again. Thank you.</h3>
        <p class="redirect-message">You have been securely logged out. Redirecting to login page...</p>
    </div>
  </article>
  
  <footer class="footer">
  <?php include 'footer.php'; ?>
  </footer>

  <script>
    // Enhanced redirect with timeout and error handling
    (function() {
        // Clear any existing session data from browser storage
        localStorage.clear();
        sessionStorage.clear();

        // Redirect after a short delay to allow user to read the message
        setTimeout(function() {
            try {
                window.location.href = 'petowner_login.php';
            } catch (error) {
                console.error('Redirect failed:', error);
                alert('Logout successful. Please manually return to the login page.');
            }
        }, 2000); // 2-second delay
    })();
  </script>
</body>
</html>