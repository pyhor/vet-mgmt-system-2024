<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="css/resetPasswordTemplate.css"> -->
    <title>Nurse Reset Password | VCMS</title>
 
</head>
<body>
   
<article>

<?php if(empty($_COOKIE['nurseid'])){
	    		include'nurseResetPassword.php';
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