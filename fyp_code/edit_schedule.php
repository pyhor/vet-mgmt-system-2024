<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/formTemplate.css">
    <link rel="stylesheet" href="css/layoutTemplate.css">
    <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
    <title>Vet Registration | VCMS</title>
   
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
        <?php include'vet_nav.php';?>
    </nav>

<article>

<?php if(empty($_COOKIE['appid'])){
	    		include'edit_schedule_details.php';
	    	    }else{
                    echo "<p><big>You are still logged in.<br><br> Registration is not allowed right now.";
                    echo "<br><br>You may log out <a href='logout.php'>here</a>.</big></p><br>";
	    	          }?>

</article>

<footer>
        <?php include 'footer.php'; ?>
    </footer>


</body>
</html>