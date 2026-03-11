<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vet_registeration_layout.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Pet Owner Reset Password | VCMS</title>
   <!-- Load jQuery library first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Load custom JavaScript file -->
    <!-- <script src="showCheckExisting.js"></script> -->
      <!-- <script src="addFormat.js"></script> -->
</head>
<body>
   
<article>

<?php if(empty($_COOKIE['petownerid'])){
	    		include'petownerResetPassword.php';
	    	    }else{
                    echo "<p><big>You are still logged in.<br><br> Registration is not allowed right now.";
                    echo "<br><br>You may log out <a href='logout.php'>here</a>.</big></p><br>";
	    	          }?>

</article>

<footer class="footer">
<?php include 'footer.php'; ?>
    </footer>

</body>
</html>