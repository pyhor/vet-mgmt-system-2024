<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/loginTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Pet Owner Login | VCMS</title>
</head>
<body>
   
<article>

<?php 
if(empty($_COOKIE['petownerid'])){
	    		include'petowner_login_form.php';
	    	    }else{
                    echo "<p><big>You are still logged in.<br><br> Login is not allowed right now.";
                    echo "<br><br>You may log out <a href='petowner_logout.php'>here</a>.</big></p><br>";
	    	          }?>

</article>

</body>
</html>